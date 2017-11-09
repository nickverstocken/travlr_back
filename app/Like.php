<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Like extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'likes';
    protected $fillable = [
        'user_id',
        'like_id',
        'like_type',
    ];

    public function media()
    {
        return $this->morphedByMany('App\Media', 'like');
    }
    public function stops()
    {
        return $this->morphedByMany('App\Stop', 'like');
    }

}
