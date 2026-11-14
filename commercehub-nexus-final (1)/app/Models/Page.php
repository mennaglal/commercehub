<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'slug',
        'content_ar',
        'content_en',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar'
            ? $this->title_ar
            : $this->title_en;
    }

    public function getContentAttribute()
    {
        return app()->getLocale() === 'ar'
            ? $this->content_ar
            : $this->content_en;
    }
}
