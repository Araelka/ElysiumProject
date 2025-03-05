<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'login',
        'email',
        'password',
        'role_id',
        'is_banned',
        'ban_reason'
    ];

    public function setLoginAttribute ($value) {
        $this->attributes['login'] = mb_strtolower(str_replace(' ', '', $value));
    }

    public function isAdmin()
    {
        return $this->role_id === 1;
    }

    public function isEditor()
    {
        return in_array($this->role_id, [1, 2]);
    }

    public function isPlayer()
    {
        return in_array($this->role_id, [1, 2, 3]);
    }

    public function isUser()
    {
        return $this->role_id === 4;
    }

    public function ban(string $reason='Нарушение правил сообщества'){
        $this->update([
            'is_banned' => true,
            'ban_reason' => $reason
        ]);
    }

    public function unban(){
        $this->update([
            'is_banned' => false,
            'ban_reason' => null
        ]);
    }

    public function terminateSessions(){
        DB::table('sessions')->where('user_id', $this->id)->delete();
    }

    public function role(){
        return $this->belongsTo(Role::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
