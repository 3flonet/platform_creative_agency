<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    protected $fillable = [
        'ip_address',
        'country_code',
        'country',
        'city',
        'user_agent',
        'url',
        'referer',
        'is_unique',
    ];
}
