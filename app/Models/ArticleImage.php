<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleImage extends Model
{
    protected $table = 'article_images';
    
    protected $fillable =[
        'article_id',
        'path',
        'file_hash'
    ];

    public function article(){
        return $this->belongsTo(Article::class);
    }
}
