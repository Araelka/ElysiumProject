<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'name',
        'description',
        'image_path'
    ];

    public function attribute() {
        return $this->belongsTo(Attribute::class);
    }

    public function characterSkills(){
        return $this->hasMany(CharacterSkill::class);
    }
}
