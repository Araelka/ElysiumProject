<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\returnArgument;

class Post extends Model
{
    protected $fillable =[
        'character_id',
        'location_id',
        'parent_post_id',
        'content'
    ];

    public function character(){
        return $this->belongsTo(Character::class);
    }

    public function locations(){
        return $this->belongsTo(Location::class);
    }

    public function parent(){
        return $this->belongsTo(Post::class, 'parent_post_id');
    }

    public function replies(){
        return $this->hasMany(Post::class, 'parent_post_id');
    }

    public function readers(){
        return $this->belongsToMany(User::class, 'post_reads', 'user_id');
    }

    public function isReadByUser($userId){
        return $this->hasOne(PostRead::class)->where('user_id', $userId);
    }

}
