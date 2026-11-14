<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'currency_id',
        'is_active',
    ];


    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
    protected $appends = ['name'];

    public function getNameAttribute(): string
    {
        return (app()->getLocale() == "ar")
            ? $this->name_ar
            : $this->name_en;
    }
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
