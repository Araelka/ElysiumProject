<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable =[
        'user_id',
        'theme_id',
        'content'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function locations(){
        return $this->belongsTo(Location::class);
    }
}
