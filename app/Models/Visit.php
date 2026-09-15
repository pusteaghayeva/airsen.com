<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'ip_hash',
        'event_type',
        'browser',
        'os',
        'device',
        'country',
        'language',
        'user_agent',
        'page_url',
    ];
}
