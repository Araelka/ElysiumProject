<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'character_id',
        'points'
    ];

    public function attribute() {
        return $this->baseSole(Attribute::class);
    }

    public function character() {
        return $this->belongsTo(Character::class);
    }
}
