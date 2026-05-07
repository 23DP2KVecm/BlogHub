<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategorija extends Model
{
    protected $table = 'kategorijas';

    protected $fillable = ['nosaukums', 'slug', 'apraksts', 'krasa'];

    public function raksti(): HasMany
    {
        return $this->hasMany(Raksts::class, 'category_id');
    }
}
