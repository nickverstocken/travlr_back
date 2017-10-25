<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class location extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'country_code',
        'name',
        'city',
        'province',
        'country',
        'time_zone',
        'lat',
        'lng'
    ];
    public function stop(){
        return $this->hasOne('App\stop');
    }
}
