<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationBroadcast extends Model
{
    public const AUDIENCES = ['customers', 'vendors', 'all', 'user'];

    protected $fillable = [
        'title_ar', 'title_en', 'body_ar', 'body_en', 'url',
        'audience', 'user_id', 'with_push', 'recipients_count', 'sent_by',
    ];

    protected $casts = [
        'with_push' => 'boolean',
        'recipients_count' => 'integer',
    ];
}
