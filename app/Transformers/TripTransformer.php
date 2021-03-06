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
use App\trip;
use League\Fractal\TransformerAbstract;

class TripTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'user', 'stops'
    ];
    public function transform(trip $trip)
    {

        if(!isset($trip->likes)){
            $trip->likes = 0;
        }
        if(!isset($trip->cover_photo_path)){
            $trip->cover_photo_path = '/assets/images/defaul_trip_cover.jpg';
        }
        if(!isset($trip->total_km)){
            $trip->total_km = 0;
        }
        if(!isset($trip->privacy)){
            $trip->privacy = 'followers';
        }
        return [
            'id' => $trip->id,
            'name' => $trip->name,
            'start_date' => $trip->start_date,
            'end_date'=> $trip->end_date,
            'total_km' => $trip->total_km,
            'cover_photo_path' => $trip->cover_photo_path,
            'creation_time' => $trip->created_at->toDateTimeString(),
            'last_modified' => $trip->updated_at->toDateTimeString(),
            'likes' => $trip->likes,
            'privacy' => $trip->privacy,
            'user_id' => $trip->user_id
        ];
    }
    public function includeUser(trip $trip)
    {
        if(!$trip->user){
            return null;
        }
        return $this->item($trip->user, App::make(UserTransformer::class));
    }
    public function includeStops(trip $trip)
    {
        if(!$trip->stops){
            return null;
        }
        return $this->collection($trip->stops, App::make(StopTransformer::class));
    }
}