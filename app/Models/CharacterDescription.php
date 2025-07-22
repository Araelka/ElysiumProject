<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterDescription extends Model
{
    use HasFactory;

    protected $table = 'character_description';

    protected $fillable = [
        'character_id',
        'biography',
        'description',
        'headcounts'
    ];

    public function character(){
        return $this->belongsTo(Character::class);
    }
}
