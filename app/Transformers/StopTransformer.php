<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 25/10/17
 * Time: 2:02
 */
// app/Transformers/TripTransformer.php

namespace App\Transformers;
use App;
use App\stop;
use League\Fractal\TransformerAbstract;

class StopTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'media', 'likes'
    ];
    public function transform(stop $stop)
    {
        return [
            'id' => $stop->id,
            'name' => $stop->name,
            'description' => $stop->description ? $stop->description : '',
            'trip_id' => $stop->trip_id,
            'views' => $stop->views ? $stop->views : 0,
            'like_count' => $stop->likes->count(),
            'arrival_time' => $stop->arrival_time,
            'location' => $stop->location,

        ];
    }
    public function includeMedia(stop $stop)
    {
        if(!$stop->media){
            return null;
        }
        return $this->collection($stop->media, App::make(MediaTransformer::class));
    }
    public function includeLikes(stop $stop){
        if(!$stop->likes){
            return null;
        }
        return $this->collection($stop->likes, App::make(UserTransformer::class));
    }
}