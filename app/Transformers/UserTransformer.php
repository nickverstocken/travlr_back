<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 25/10/17
 * Time: 2:02
 */
// app/Transformers/TripTransformer.php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'last_name' => $user->name,
            'first_name' => $user->first_name,
            'email' => $user->email,
            'fb_id' => $user->fb_id,
            'fb_token' => $user->fb_token,
            'time_zone' => $user->time_zone,
            'profile_image' => $user->profile_image,
            'profile_image_thumb' => $user->profile_image,
            'profile_image_cover' => $user->profile_image,
            'creation_date' => $user->created_at->toDateTimeString(),
            'temperature_is_celsius' => $user->temperature_is_celsius,
            'unit_is_km' => $user->unit_is_km,
        ];
    }
}