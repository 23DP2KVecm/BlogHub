<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reakcija extends Model
{
    protected $table = 'reakcijas';

    protected $fillable = ['post_id', 'user_id', 'veids'];

    public function raksts(): BelongsTo
    {
        return $this->belongsTo(Raksts::class, 'post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
