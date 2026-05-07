<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Raksts extends Model
{
    protected $table = 'raksti';

    protected $fillable = [
        'user_id',
        'category_id',
        'virsraksts',
        'slug',
        'saturs',
        'ievads',
        'attels_url',
        'statuss',
        'skatijumi',
        'publicets_datums',
    ];

    protected $casts = [
        'publicets_datums' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Kategorija::class, 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Birka::class, 'raksts_birka', 'post_id', 'tag_id');
    }

    public function komentari(): HasMany
    {
        return $this->hasMany(Komentars::class, 'post_id');
    }

    public function reakcijas(): HasMany
    {
        return $this->hasMany(Reakcija::class, 'post_id');
    }
}
