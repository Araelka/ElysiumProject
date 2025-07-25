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
        return $this->belongsTo(Attribute::class);
    }

    public function character() {
        return $this->belongsTo(Character::class);
    }

    public function skills(){
        $skills = Skill::where('attribute_id', $this->attribute_id)->get();
        $characterSkills = [];
        foreach ($skills as $skill) {
            $characterSkills[] = CharacterSkill::where('skill_id', $skill->id)->where('character_id', $this->character_id)->get()->first();
        }

        return $characterSkills;
    }
}
