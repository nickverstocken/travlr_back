<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 25/10/17
 * Time: 2:02
 */
// app/Transformers/TripTransformer.php

namespace App\Transformers;

use App\stop;
use League\Fractal\TransformerAbstract;

class StopTransformer extends TransformerAbstract
{
    public function transform(stop $stop)
    {
        return [
            'id' => $stop->id,
            'name' => $stop->name,
            'descriptio' => $stop->description,
            'trip_id' => $stop->trip_id,
            'views' => $stop->views,
            'likes' => $stop->likes,
            'arrival_time' => $stop->arrival_time,
            'location' => $stop->location,
            'media' => $stop->media
        ];
    }
}