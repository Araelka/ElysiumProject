<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use DB;
use function PHPUnit\Framework\returnArgument;

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
        'is_banned',
        'ban_reason'
    ];

    public function setLoginAttribute ($value) {
        $this->attributes['login'] = mb_strtolower(str_replace(' ', '', $value));
    }

    public function isAdmin()
    {
        return in_array(1, $this->getRoleIds());
    }

    public function isModerator()
    {
        return array_intersect([1, 2], $this->getRoleIds()) != [];
    }

    public function isEditor()
    {
        return array_intersect([1, 2, 3], $this->getRoleIds()) != [];
    }

    public function isGameMaster()
    {
        return array_intersect([1, 2, 4], $this->getRoleIds()) != [];
    }

     public function isQuestionnaireSpecialist()
    {
        return array_intersect([1, 2, 5], $this->getRoleIds()) != [];
    }

    public function isPlayer()
    {
        return array_intersect([1, 2, 3, 4, 5, 6], $this->getRoleIds()) != [];
    }

    public function isUser()
    {
        return in_array(7, $this->getRoleIds());
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

    // public function role(){
    //     return $this->belongsTo(Role::class);
    // }

    public function roles(): BelongsToMany {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public function getRoleIds(){
        return $this->roles()->pluck('id')->toArray();
    }

    public function character() {
        return $this->hasMany(Character::class);
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

    public function getCountvailableCharacters(){
        return $this->character()->where('status_id', '!=', 5)->where('status_id', '!=', 6)->count();
    }

}
