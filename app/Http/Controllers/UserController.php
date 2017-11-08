<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transformers\UserTransformer;
use App\Http\Transform;
use League\Fractal\Resource\Item;
use Response;
use App\User;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use JWTAuth;
class UserController extends Controller
{
    private $_fractal;
    private $_usertransformer;
    function __construct(Manager $fractal, UserTransformer $userTransformer)
    {
        $this->_fractal = $fractal;
        $this->_fractal->setSerializer(new ArraySerializer());
        $this->_usertransformer = $userTransformer;
    }
    public function showCurrent(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $user = new Item($user, $this->_usertransformer);
        $this->_fractal->parseIncludes($request->get('include', ''));
        $user = $this->_fractal->createData($user);
        $user = $user->toArray();
        return Response::json([
            'user' => $user
        ], 200);
    }
    public function show($id, Request $request){
        $user = User::find($id);

        if(!$user){
            return Response::json([
                'error' => [
                    'message' => 'User does not exist'
                ]
            ], 404);
        }
        if($user){
            $user = new Item($user, $this->_usertransformer);
            $this->_fractal->parseIncludes($request->get('include', ''));
            $user = $this->_fractal->createData($user);
            $user = $user->toArray();
        }
        return Response::json([
            'data' => $user
        ], 200);
    }
    public function getFollowers($id, Request $request){
        $user = User::find($id);
        $followers = $user->followers()->get();
        $following = $user->following()->get();
        $count = count($followers);
        $followers = new Collection($followers, $this->_usertransformer);
        $following = new Collection($following, $this->_usertransformer);
        $this->_fractal->parseIncludes($request->get('include', ''));
        $this->_fractal->parseIncludes($request->get('include', ''));
        $followers = $this->_fractal->createData($followers);
        $following = $this->_fractal->createData($following);
        return Response::json([
            'success' => true,
            'followers' => $followers->toArray(),
            'count' => $count,
            'following' => $following->toArray()
        ], 200);
    }
}
