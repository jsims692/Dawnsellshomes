<?php

return [

    // MRED agent ids for Dawn + Josh — matches the team's own listings in the
    // feed (list, co-list or buyer side). Matching only; never displayed.
    'team_agent_ids' => array_values(array_filter(array_map('trim', explode(',', env('TEAM_AGENT_MLS_IDS', ''))))),


    // IDX listings routes go live only when this is on (env LISTINGS_ENABLED)
    'listings_enabled' => env('LISTINGS_ENABLED', false),

    // Service-area towns WITHOUT a hand-built city page (the sync's coverage
    // filter unions these with city-page slugs). Lowercase, spaces.
    'extra_coverage_cities' => [
        'hawthorn woods', 'long grove', 'lake zurich', 'kildeer', 'deer park',
        'lincolnshire', 'riverwoods', 'johnsburg', 'spring grove', 'wonder lake',
        'lakemoor', 'port barrington', 'tower lakes', 'oakwood hills',
        'prairie grove', 'richmond', 'green oaks', 'hainesville',
    ],

    // Early-career closings that predate the mapped sales table (2007+).
    // Career total shown sitewide = sales rows + this. 89 reconciles the
    // 644 career count with the 555 mapped sales as of Aug 2026 — correct
    // it here if the real archive count differs.
    'sales_baseline' => (int) env('SALES_BASELINE', 89),
    'josh_cell' => '(224) 628-4013',

    // 30-yr fixed assumption for payment-first search. Update weekly-ish
    // (env MORTGAGE_RATE); shoppers can override per-search within 2-12%.
    'mortgage_rate' => (float) env('MORTGAGE_RATE', 6.1),
    // Public Google reviews shown on /reviews (verbatim; also embedded as Review schema). Update as new ones come in.
    'reviews' => [
        ['Mark Kegermann', 'First-Time Buyer', 'We had an awesome experience working with Josh throughout our home buying process. He was incredibly responsive, transparent, and always quick to answer questions. His professionalism and communication made the entire experience smooth and stress-free. Made our first home purchase experience 11/10!'],
        ['Charles Boyle', 'Seller', 'Dawn is the best Realtor out there. She is kind, efficient, hard working and my house sold in 2 days. She is awesome.'],
        ['Kurt Koziol', 'Repeat Client', 'Dawn did us good again. She is the absolute best. Hooray for Dawn. Also Josh is super. He learned well from Mom.'],
        ['Karen Koziol', 'Buyer', 'Great realtor. Wouldn\'t have won the bidding war without her help. Thanks Dawn for completing another Dawn deal.'],
        ['Lidia Stoklosa', 'Client', 'Dawn and Josh team are just wonderful. They are very pleasant to work with, positive attitude. We highly recommend them as your real estate agents.'],
        ['Nick Wienold', 'Buyer', 'Josh and Dawn are true professionals. They have the resources to pull any deal off and save you if a mortgage falls through. Truly exceptional service.'],
        ['Kris Mulcahy', 'Client', 'Dawn is a great pleasure to work with. Very professional. She knows the market and goes above & beyond with superior service!'],
        ['Athena Weimer', 'Buyer', 'I was out of state looking for a place to rent and have never felt so supported by an agent. Dawn is an asset to this company.'],
        ['John M', 'Client (Feb 2026)', 'Josh is very knowledgeable and pleasant to work with.'],
        ['G B', 'Client (Dec 2025)', 'Dawn was super helpful and responsive. She knows her stuff!! Highly recommend.'],
        ['Lalo Z', 'First-Time Buyer (Sep 2025)', 'Josh was the agent helping my find a new home and have been working with him for months now and I only have good things to say to him and his team . As a first time home buyer things were difficult to understand but he made the process fun and exciting . I learned a lot throughout this process and now thanks to him and many others , me and my family can finally enjoy our new home.'],
        ['Jon K', 'Client (Sep 2025)', 'Dawn was amazing! Her team handled my transaction flawlessly. Would recommend to all my friends and family.'],
        ['Karen C', 'Client (Sep 2025)', 'The Dawn Simmons team was excellent from start to finish. Their market knowledge, and friendly demeanor put me at ease. I felt overwhelmed selling my home .. and this team made it stress free. My advice is if you want a positive experience.. reach out to this Team'],
        ['Jim S', 'Client (Jul 2025)', 'Dawn and her team are excellent at their work- much appreciated for making real estate issues easier and more manageable! Love working with her crew!'],
        ['Margaret R', 'Client (Apr 2025)', 'Dawn and Josh are both very hard workers and extremely enthusiastic and dedicated to their passion of selling real estate.<br><br>No job is too challenging or a bother to do. They are not afraid or hesitant to roll up their sleeves to get things moving quickly both physically with moving furniture around for listing pictures and in delivering the paperwork needed to sell any property.<br><br>Dawn is super flexible, highly responsive, and above all, very kind and helpful ... great sense of humor to boot too. She helped sell a house for us in Prospect Heights 15 years ago and we will never work with another realtor ever again. She is stuck with us!! ❤️'],
        ['Jim Q', 'Buyer (Mar 2025)', 'Dawn made the home-buying process smooth and stress-free! She found the perfect home, negotiated effectively with the sellers, and kept the buyer informed every step of the way. Working with her was an absolute pleasure!'],
        ['JP T', 'Commercial Rental Client (Sep 2024)', 'I gave Josh a difficult request concerning a business rental, and he\'s doing a great job sending offers.'],
        ['Rose B', 'Client (Aug 2024)', 'Josh is great! Very knowledgeable and efficient. Friendly and returns calls and emails promptly.'],
    ],
];
