<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Retire IDX Broker from the imported pages: search forms and result
     * links now target our own /listings (numeric IDX city ids translated
     * back to city names). Idempotent; content text untouched.
     */
    private const CITY_IDS = [
        '253' => 'Addison',
        '620' => 'Algonquin',
        '1327' => 'Antioch',
        '1615' => 'Arlington Heights',
        '2676' => 'Barrington',
        '2680' => 'Barrington Hills',
        '2710' => 'Bartlett',
        '3046' => 'Beach Park',
        '3493' => 'Bellwood',
        '3640' => 'Bensenville',
        '4485' => 'Bloomingdale',
        '5557' => 'Bridgeport',
        '6258' => 'Buffalo Grove',
        '7386' => 'Carol Stream',
        '7409' => 'Carpentersville',
        '7526' => 'Cary',
        '8569' => 'Chicago',
        '8901' => 'Clarendon Hills',
        '11067' => 'Crystal Lake',
        '11829' => 'Deerfield',
        '12146' => 'Des Plaines',
        '12685' => 'Downers Grove',
        '13345' => 'East Dundee',
        '14188' => 'Elgin',
        '14259' => 'Elk Grove Village',
        '14456' => 'Elmhurst',
        '14496' => 'Elmwood Park',
        '15111' => 'Evanston',
        '16798' => 'Fox Lake',
        '16803' => 'Fox River Grove',
        '16911' => 'Franklin Park',
        '18068' => 'Glen Ellyn',
        '18123' => 'Glencoe',
        '18152' => 'Glendale Heights',
        '18212' => 'Glenview',
        '18923' => 'Grayslake',
        '19525' => 'Gurnee',
        '19994' => 'Hanover Park',
        '20371' => 'Harwood Heights',
        '21175' => 'Highland Park',
        '21206' => 'Highwood',
        '21314' => 'Hillside',
        '21376' => 'Hinsdale',
        '21472' => 'Hoffman Estates',
        '22601' => 'Ingleside',
        '22662' => 'Inverness',
        '22829' => 'Island Lake',
        '22864' => 'Itasca',
        '24765' => 'La Grange Park',
        '25001' => 'Lake Barrington',
        '25004' => 'Lake Bluff',
        '25070' => 'Lake Forest',
        '25106' => 'Lake In The Hills',
        '25241' => 'Lake Villa',
        '26382' => 'Libertyville',
        '26540' => 'Lincolnwood',
        '26565' => 'Lindenhurst',
        '26667' => 'Lisle',
        '27031' => 'Lombard',
        '29636' => 'Melrose Park',
        '31301' => 'Morton Grove',
        '31561' => 'Mount Prospect',
        '31798' => 'Mundelein',
        '32998' => 'Niles',
        '33175' => 'Norridge',
        '33214' => 'North Barrington',
        '33276' => 'North Chicago',
        '33610' => 'Northbrook',
        '33631' => 'Northfield',
        '33753' => 'Norwood Park',
        '33853' => 'Oak Brook',
        '33925' => 'Oakbrook Terrace',
        '35177' => 'Palatine',
        '35511' => 'Park Ridge',
        '37959' => 'Prospect Heights',
        '39525' => 'River Grove',
        '40040' => 'Rolling Meadows',
        '40212' => 'Roselle',
        '40218' => 'Rosemont',
        '40331' => 'Round Lake',
        '40335' => 'Round Lake Beach',
        '40336' => 'Round Lake Heights',
        '40337' => 'Round Lake Park',
        '40809' => 'Saint Charles',
        '41730' => 'Schaumburg',
        '43130' => 'Skokie',
        '43554' => 'South Barrington',
        '43645' => 'South Elgin',
        '45006' => 'Streamwood',
        '46202' => 'Third Lake',
        '48964' => 'Vernon Hills',
        '49229' => 'Villa Park',
        '49478' => 'Volo',
        '50253' => 'Wauconda',
        '50258' => 'Waukegan',
        '50334' => 'Wayne',
        '50795' => 'West Dundee',
        '51184' => 'Westchester',
        '51197' => 'Western Springs',
        '51261' => 'Westmont',
        '51426' => 'Wheaton',
        '51447' => 'Wheeling',
        '52260' => 'Winnetka',
        '52332' => 'Winthrop Harbor',
        '52455' => 'Wood Dale',
        '53088' => 'Zion',
    ];

    public function up(): void
    {
        DB::table('pages')->where('body_html', 'like', '%search.dawnsellshomes.com%')
            ->orderBy('id')->chunkById(25, function ($pages) {
                foreach ($pages as $p) {
                    $body = $this->rewire($p->body_html);
                    if ($body !== $p->body_html) {
                        DB::table('pages')->where('id', $p->id)->update(['body_html' => $body]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Forward-only; pages_navy_body_backup remains the disaster restore.
    }

    private function rewire(string $body): string
    {
        // 1) Search forms: action + field names; hidden city id -> city name.
        $body = str_replace('action="https://search.dawnsellshomes.com/idx/results/listings" method="get"', 'action="/listings" method="get"', $body);
        $body = preg_replace('/<input type="hidden" name="(?:pt|ccz|widgetReferer)"[^>]*>\s*/', '', $body);
        $body = preg_replace_callback('/<input type="hidden" name="city\[\]" value="(\d+)">/',
            fn ($m) => isset(self::CITY_IDS[$m[1]])
                ? '<input type="hidden" name="city" value="'.self::CITY_IDS[$m[1]].'">' : '',
            $body);
        $body = str_replace(['name="lp"', 'name="hp"', 'name="bd"', 'name="ba"'],
            ['name="min"', 'name="max"', 'name="beds"', 'name="baths"'], $body);

        // 2) Result links: keep the city when the URL carries one.
        $body = preg_replace_callback('/https:\/\/search\.dawnsellshomes\.com\/idx\/[^"\x27\s<>]*/',
            function ($m) {
                parse_str((string) parse_url($m[0], PHP_URL_QUERY), $q);
                $id = $q['city'][0] ?? null;

                return isset(self::CITY_IDS[$id])
                    ? '/listings?city='.rawurlencode(self::CITY_IDS[$id]) : '/listings';
            }, $body);

        return $body;
    }
};
