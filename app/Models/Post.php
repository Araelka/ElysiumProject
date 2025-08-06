<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable =[
        'character_id',
        'location_id',
        'content'
    ];

    public function character(){
        return $this->belongsTo(Character::class);
    }

    public function locations(){
        return $this->belongsTo(Location::class);
    }
}
