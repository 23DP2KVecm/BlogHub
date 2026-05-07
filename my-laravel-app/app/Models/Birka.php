<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Birka extends Model
{
    protected $table = 'birkas';

    protected $fillable = ['nosaukums', 'slug'];

    public function raksti(): BelongsToMany
    {
        return $this->belongsToMany(Raksts::class, 'raksts_birka', 'tag_id', 'post_id');
    }
}
