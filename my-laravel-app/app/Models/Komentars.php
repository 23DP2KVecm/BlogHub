<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Komentars extends Model
{
    protected $table = 'komentari';

    protected $fillable = ['post_id', 'user_id', 'saturs', 'apstiprints'];

    protected $casts = [
        'apstiprints' => 'boolean',
    ];

    public function raksts(): BelongsTo
    {
        return $this->belongsTo(Raksts::class, 'post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
