<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;
class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'first_name',
        'email',
        'password',
        'fb_id',
        'fb_token',
        'time_zone',
        'country',
        'city',
        'unit_is_km',
        'temperature_is_celsius',
        'profile_image',
        'profile_image_cover',
        'profile_image_thumb',
        'role',
        'is_verified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function trips(){
        return $this->hasMany('App\trip');
    }
    public function comments(){
        return $this->hasMany('App\Comment');
    }
    public function followers()
    {
        return $this->belongsToMany(
            self::class,
            'follow',
            'followee_id',
            'follower_id'
        );
    }
    public function following()
    {
        return $this->belongsToMany(
            self::class,
            'follow',
            'follower_id',
            'followee_id'
        );
    }

}
