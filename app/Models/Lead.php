<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_spam' => 'boolean',
            'forwarded_at' => 'datetime',
        ];
    }
}
