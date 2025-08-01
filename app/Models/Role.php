<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Role extends Model
{
    use HasFactory;

    public $timestamps = false; 

    protected $fillable = ['name'];

    // public function usres() {
    //     return $this->hasMany(User::class);
    // }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }
}
