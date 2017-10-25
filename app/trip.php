<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class trip extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_id',
        'name',
        'start_date',
        'total_km',
        'cover_photo_path',
        'likes'
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
    public function stops(){
        return $this->hasMany('App\stop');
    }
}
