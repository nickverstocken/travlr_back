<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JWTAuth;
class Media extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $hidden = ['updated_at', 'created_at', 'deleted_at'];
    protected $fillable = [
        'stop_id',
        'caption',
        'image',
        'image_thumb'
    ];

    public function stop(){
        return $this->belongsTo('App\stop', 'stop_id');
    }
    public function likes()
    {
        return $this->morphToMany('App\User', 'like')->where('likes.deleted_at',null);;
    }
    public function getIsLikedAttribute()
    {
        $like = $this->likes()->whereUserId(JWTAuth::parseToken()->toUser()->id)->first();
        return (!is_null($like)) ? true : false;
    }
}
