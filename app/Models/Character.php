<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\returnArgument;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'firstName',
        'secondName',
        'gender',
        'age',
        'height',
        'weight',
        'nationality',
        'residentialAddress',
        'activity',
        'personality',
        'comment',
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
        return $this->hasOne(CharacterDescription::class)->get()->first();
    }

    public function status(){
        return $this->belongsTo(CharacterStatus::class);
    }

    public function images(){
        return $this->hasMany(CharacterImage::class);
    }

    public function isPreparing(){
        return $this->status_id === 1;
    }

    public function isConsideration(){
        return $this->status_id === 2;
    }

    public function isApproved(){
        return $this->status_id === 3;
    }

    public function isRejected(){
        return $this->status_id === 4;
    }

    public function isArchive(){
        return $this->status_id === 5;
    }

    public function isDead(){
        return $this->status_id === 6;
    }

    public function getTotalSpentPoints(){
        return $this->attributes()->sum('points') + $this->skills()->sum('points');
    }

    public function getAvailablePoints(){
        return $this->available_points;
    }

    public function increaseAvailablePoints() {
        $this->timestamps = false;
        $this->increment('available_points');
        $this->timestamps = true;
    }

    public function decreaseAvailablePoints() {
        if ($this->getAvailablePoints() > 0) {
            $this->timestamps = false;
            $this->decrement('available_points');
            $this->timestamps = true;
        }
    }

    public function posts(){
        return $this->hasMany(Post::class);
    }
}
