<?php

namespace App\Http\Controllers;

use App\stop;
use App\User;
use Illuminate\Http\Request;
use App\Like;
use JWTAuth;
use Response;
class LikeController extends Controller
{
    public function likeMedia($id)
    {

        $this->handleLike('App\Media', $id);
        return Response::json([
            'success' => true,
            'message' => 'Photo Liked!'
        ], 200);
    }

    public function likeStop($id)
    {
        $stop = stop::find($id);
        $this->handleLike('App\stop', $id);
        $stop = $stop->likes;
        return Response::json([
            'success' => true,
            'message' => 'Stop Liked!',
            'like' => $stop
        ], 200);
    }
    public function handleLike($type, $id)
    {
        $user = JWTAuth::parseToken()->toUser();
        $existing_like = Like::withTrashed()->whereLikeType($type)->whereLikeId($id)->whereUserId($user->id)->first();

        if (is_null($existing_like)) {
            Like::create([
                'user_id'       => $user->id,
                'like_id'   => $id,
                'like_type' => $type,
            ]);
        } else {
            if (is_null($existing_like->deleted_at)) {
                $existing_like->delete();
            } else {
                $existing_like->restore();
            }
        }
    }
}
