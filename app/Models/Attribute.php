<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    public function skills() {
        return $this->hasMany(Skill::class);
    }

    public function characterAttribetes(){
        return $this->hasMany(CharacterAttribute::class);
    }
}
