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
        'firstName',
        'secondName',
        'gender',
        'age',
        'nationality',
        'residentialAddress',
        'activity',
        'personality',
        'status_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function attributes() {
        return $this->hasMany(CharacterAttribute::class);
    }

    public function skills(){
        return $this->hasMany(CharacterSkill::class);
    }
    
    public function description() {
        return $this->belongsTo(CharacterDescription::class);
    }

    public function status(){
        return $this->belongsTo(CharacterStatus::class);
    }

    public function preparing(){
        return $this->character_status_id === 1;
    }

    public function consideration(){
        return $this->character_status_id === 2;
    }

    public function approved(){
        return $this->character_status_id === 3;
    }

    public function rejected(){
        return $this->character_status_id === 4;
    }

    public function getTotalSpentPoints(){
        return $this->attributes()->sum('points') + $this->skills()->sum('points');
    }

    public function getAvailablePoints(){
        return $this->available_points;
    }
}
