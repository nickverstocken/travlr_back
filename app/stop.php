<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class stop extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'description',
        'trip_id',
        'location_id',
        'views',
        'likes',
        'arrival_time'
    ];

    public function trip(){
        return $this->belongsTo('App\trip', 'trip_id');
    }

    public function media(){
        return $this->hasMany('App\Media');
    }

    public function location(){
        return $this->belongsTo('App\location', 'location_id');
    }
    public function likes()
    {
        return $this->morphToMany('App\User', 'like')->where('likes.deleted_at',null);
    }


}
