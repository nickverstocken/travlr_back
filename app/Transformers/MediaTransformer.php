<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 25/10/17
 * Time: 2:02
 */
// app/Transformers/TripTransformer.php

namespace App\Transformers;

use App\Media;
use League\Fractal\TransformerAbstract;

class MediaTransformer extends TransformerAbstract
{
    public function transform(Media $media)
    {
        return [
            'id' => $media->id,
            'stop_id' => $media->stop_id,
            'caption' => $media->caption,
            'image' => $media->image,
            'image_thumb' => $media->image_thumb
        ];
    }
}