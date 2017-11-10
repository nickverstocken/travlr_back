<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'media_id',
        'comment'
    ];
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
    public function stops(){
        return $this->belongsTo('App\Media', 'media_id');
    }
}
