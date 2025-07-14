<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'gender',
        'age',
        'species',
        'biography'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function attribute() {
        return $this->hasMany(CharacterAttribute::class);
    }

    public function skills(){
        return $this->hasMany(Skill::class);
    }
}
