<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterStatus extends Model
{
    use HasFactory;

    public $timestamps = false; 

    protected $fillable = [
        'name'
    ];

    public function characters(){
        return $this->hasMany(Character::class);
    }
}
