<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\returnArgument;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'gender',
        'age',
        'species',
        'biography',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function attributes() {
        return $this->hasMany(CharacterAttribute::class);
    }

    public function skills(){
        return $this->hasMany(Skill::class);
    }

    public function getTotalSpentPoints(){
        return $this->attributes->sum('points') + $this->skills->sum('points');
    }

    public function getAvailablePoints(){
        return $this->total_points - $this->getTotalSpentPoints();
    }
}
