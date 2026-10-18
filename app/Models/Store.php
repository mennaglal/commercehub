<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'user_id', 'name', 'slug', 'description', 'logo', 'banner',
        'status', 'approval_status',
    ];

    protected $appends = ['logo_url', 'banner_url'];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset($this->logo) : null;
    }

    public function getBannerUrlAttribute()
    {
        return $this->banner ? asset($this->banner) : null;
    }
}
