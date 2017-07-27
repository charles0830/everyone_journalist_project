<?php

namespace App;

use App\Transformers\UserTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    protected $table = 'users';
    use Notifiable,SoftDeletes,HasApiTokens;

    const VERIFIED_USER = 1;
    CONST UNVERIFIED_USER = 0;
    const ADMIN_USER = 'true';
    const REGULAR_USER = 'false';


    public $transformer = UserTransformer::class;

    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verified',
        'verification_token',
        'admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function getNameAttribute($value)
    {
        return ucwords($value);
    }


    public function isVerified()
    {
        return $this->verified == USER::VERIFIED_USER;
    }

    public static function generateVerificationCode()
    {
        return str_random(40);
    }

    public function isAdmin()
    {
        return $this->admin == User::ADMIN_USER;
    }
}
