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
use League\Fractal\Pagination\Cursor;
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
    public function index(Request $request){
        $currentCursor = $request->input('cursor', null);
        $previousCursor = $request->input('previous', null);
        $search_term = $request->input('search');
        $limit = $request->input('limit', 10);
        $users = User::where('email', 'LIKE', "%$search_term%")
            ->orWhere('first_name', 'LIKE', "%$search_term%")
            ->orWhere('name', 'LIKE', "%$search_term%");

        if ($currentCursor) {
            $users = $users->having('users.id', '>', $currentCursor)->take($limit)->get();
        } else {
            $users = $users->take($limit)->get();
        }
        if(!$users->count() > 0){
            return Response::json([
                    'success' => true,
                    'user' => []
                ]
                , 200);
        }
        $newCursor = $users->last()->id;
        $cursor = new Cursor($currentCursor, $previousCursor, $newCursor, $users->count());
        $users = new Collection($users, $this->_usertransformer);
        $this->_fractal->parseIncludes($request->get('include', ''));
        $users = $users->setCursor($cursor);
        $users = $this->_fractal->createData($users);
        return Response::json([
                'success' => true,
                'user' => $users->toArray()
            ]
            , 200);

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
    public function followUser($userid, Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $user2 = User::find($userid);
        $user->following()->save($user2);
        return Response::json([
            'success' => true,
            'message' => 'succesfully followed user',
            'user' => $user2
        ], 200);
    }
    public function unfollowUser($userid, Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $user2 = User::find($userid);
        $user->following()->detach($user2);
        return Response::json([
            'success' => true,
            'message' => 'succesfully unfollowed user',
            'user' => $user2
        ], 200);
    }

}
