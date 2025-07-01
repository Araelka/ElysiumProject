<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeImage extends Model
{
    protected $table = 'theme_images';

    protected $fillable =[
        'theme_id',
        'path',
        'description'
    ];

    public function theme(){
        return $this->belongsTo(Theme::class);
    }
}
