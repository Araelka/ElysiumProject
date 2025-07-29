<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'character_id',
        'skill_id',
        'points'
    ];

    public function character() {
        return $this->belongsTo(Character::class);
    }

    public function skill() {
        return $this->belongsTo(Skill::class);
    }

    public function getMaxLevelAttribute(){
        $attributLevel = $this->character->attributes()
            ->where('attribute_id', $this->skill->attribute_id)
            ->value('level');

        return 2 * $attributLevel;
    }

    public function getMaxPoints(){
        $maxPoints = $this->character->attributes()
            ->where('attribute_id', $this->skill->attribute_id)
            ->value('level');

        return $maxPoints;
    }

    public function getLevelSkill(){
        $attributLevel = $this->character->attributes()
            ->where('attribute_id', $this->skill->attribute_id)
            ->value('level');

        return min($attributLevel + $this->points, 2 * $attributLevel);
    }
}
