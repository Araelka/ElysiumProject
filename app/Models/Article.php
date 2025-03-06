<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'theme_id',
        'title',
        'content'
    ];

    public function theme(){
        return $this->belongsTo(Theme::class);
    }

    public function images(){
        return $this->hasMany(ArticleImage::class);
    }
}
