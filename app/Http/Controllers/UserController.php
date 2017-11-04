<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transformers\UserTransformer;
use App\Http\Transform;
use League\Fractal\Resource\Item;
use Response;
use App\User;
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
}
