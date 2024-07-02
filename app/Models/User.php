<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $table = 'users';
    protected $fillable = [
        'name', 'telegram_username', 'telegram_id', 'img', 'role', 'password', 'img', 'limits', 'score', 'step', 'channels',
        'groups', 'expires_at', 'created_at', 'updated_at', 'must_join', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token', 'token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'score' => 'integer',
        'verified' => 'bool',
        'must_join' => 'array',
        'channels' => 'array',
        'groups' => 'array',
        'allowed_games_limit' => 'integer',

    ];

    public function getExpiresAtAttribute($value)
    {
        if (!$value) return $value;
        return \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($value));
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MyResetPassword($token));
    }


    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function findForPassport($username)
    {
        $fieldType =/* filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' :*/
            'telegram_username';
//        dd(User::where($fieldType, $username)->first());
        return
            User::where($fieldType, $username)->first();
    }
}
