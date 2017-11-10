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
use Validator;
use App\ImageSave;
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
    public function update(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $rules = [
            'first_name' => 'required|max:255',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
        ];
        $validation = [
            'first_name'=> $request->get('first_name'),
            'name'=> $request->get('name'),
            'email'=> $request->get('email'),
        ];
        $validator = Validator::make($validation, $rules);
        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['success' => false, 'error' => $error]);
        }
        $user->first_name = $request->get('first_name');
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->time_zone = $request->get('time_zone', null);
        $user->city = $request->get('city', null);
        $user->country = $request->get('country', null);
        if($request->file('profile_image')){
            $image = $request->file('profile_image');
            $extension = $image->getClientOriginalExtension();
            if(!($extension == 'jpg' || $extension == 'png')) {
                return Response::json([
                    'message' => 'Only upload jpg or png images please'
                ], 422);
            }else{

                $bytes = random_bytes(10);
                $img_name = bin2hex($bytes);
                try{
                    $folder = 'u_' . $user->id . '/profile';
                    $save = new ImageSave(400,400, $folder, $img_name . '.' . $extension, $image );
                    $save_thumb = new ImageSave(90,90, $folder, $img_name . '_thumb.' . $extension, $image );
                    $save->clearFolder($folder);
                    $profile_image = $save->saveImage();
                    $profile_thumb = $save_thumb->saveImage();
                    $user->profile_image = $profile_image;
                    $user->profile_image_thumb = $profile_thumb;
                }catch (Exception $e) {
                    return Response::json([
                        'message' => 'something went wrong while trying to upload your picture try again please'
                    ], 422);
                }
            }
        }
        $user->save();
        $user = new Item($user, $this->_usertransformer);
        $this->_fractal->parseIncludes($request->get('include', ''));
        $user = $this->_fractal->createData($user);
        $user = $user->toArray();
        return Response::json([
            'success' => true,
            'message' => 'User edited Succesfully',
            'user' => $user
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
