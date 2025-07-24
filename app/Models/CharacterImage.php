<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharacterImage extends Model
{
    protected $fillable = [
        'character_id',
        'path',
        'file_hash'
    ];

    public function character(){
        return $this->belongsTo(Article::class);
    }
}
