<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    public $timestamps = false; 

    protected $fillable = [
        'name',
        'visibility'
    ];

    public function article (){
        return $this->hasOne(Article::class);
    }

    public function images(){
        return $this->hasMany(ThemeImage::class);
    }
}
