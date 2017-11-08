<?php

namespace App\Http\Controllers;

use App\ImageSave;
use App\Transformers\TripTransformer;
use App\User;
use Illuminate\Http\Request;
use App\trip;
use App\Http\Transform;
use League\Fractal\Resource\Item;
use Response;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use JWTAuth;
use Validator;
class TripsController extends Controller
{
    private $_fractal;
    private $_triptransformer;
    function __construct(Manager $fractal, TripTransformer $triptransformer)
    {

        $this->_fractal = $fractal;
        $this->_fractal->setSerializer(new ArraySerializer());
        $this->_triptransformer = $triptransformer;
    }

    public function index(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $trips = Trip::where('user_id', $user->id)->orderBy('start_date', 'desc')->get();
        $trips = new Collection($trips, $this->_triptransformer);
        $this->_fractal->parseIncludes($request->get('include', ''));
        $trips = $this->_fractal->createData($trips);
        return Response::json([
            'trips' => $trips->toArray()
            ]
        , 200);
    }
    public function show($id, Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $trip = Trip::find($id);
        if(!$trip){
            return Response::json([
                'error' => [
                    'message' => 'Trip does not exist'
                ]
            ], 404);
        }
        if($trip && $trip->user_id == $user->id || $trip->privacy == 'public' || $trip->privacy == 'followers'){
            $trip = new Item($trip, $this->_triptransformer);
            $this->_fractal->parseIncludes($request->get('include', ''));
            $trip = $this->_fractal->createData($trip);
            $trip = $trip->toArray();

            return Response::json([
                'succes' => true,
                'trip' => $trip
            ], 200);
        }else{
            return Response::json([
                'success' => false,
                'error' => [
                    'message' => "You don't have the rights to view this trip"
                ]
            ], 404);
        }
    }
    public function store(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $trip = new trip();
        $rules = [
            'name' => 'required|max:255',
            'start_date' => 'required|max:255|unique:trips',
        ];
        $validation = [
            'name'=> $request->get('name'),
            'start_date'=> $request->get('start_date'),
        ];
        $validator = Validator::make($validation, $rules);
        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['success' => false, 'error' => $error]);
        }
        $trip->name = $request->get('name');
        $trip->start_date = $request->get('start_date');
        if($request->get('end_date')){
            $trip->end_date = $request->get('end_date');
        }
        if($request->file('image_cover')) {
            $image = $request->file('image_cover');
            $extension = $image->getClientOriginalExtension();
            if (!($extension == 'jpg' || $extension == 'png')) {
                return Response::json([
                    'message' => 'Only upload jpg or png images please'
                ], 422);
            } else {
                $bytes = random_bytes(10);
                $img_name = bin2hex($bytes);
                $folder = 'u_' . $user->id . '/trips/'. $trip->id .'/coverimage';

                $save = new ImageSave(600, 400, $folder , $img_name . '.' . $extension, $image);
                $save->clearFolder($folder);
                $coverphoto = $save->saveImage();
                $trip->cover_photo_path = $coverphoto;
            }
        }
        $trip = $user->trips()->save($trip);
        if($trip){
            $trip = new Item($trip, $this->_triptransformer);
            $trip = $this->_fractal->createData($trip);
            $trip = $trip->toArray();
        }
        return Response::json([
            'success' => true,
            'message' => 'Trip Created Succesfully',
            'trip' => $trip
        ], 200);
    }
    public function destroy($id){
        $user = JWTAuth::parseToken()->toUser();
        $trip = trip::find($id);
       if($trip->user_id == $user->id){
           $trip->delete();
           return Response::json([
               'success' => true,
               'message' => 'Trip Deleted Succesfully'
           ], 200);
       }
    }
    public function update(Request $request, $id){
        $user = JWTAuth::parseToken()->toUser();
        $trip = trip::find($id);
        if(!$trip){
            return Response::json([
                'error' => [
                    'message' => 'Trip does not exist'
                ]
            ], 404);
        }
        if($trip->user_id == $user->id){
            if ($request->get('name')) {
                $trip->name = $request->get('name');
            }
            if ($request->filled('start_date'))
                $trip->start_date = $request->get('start_date');
            }
            if ($request->filled('end_date')) {
                $trip->end_date = $request->get('end_date');
            }
            if ($request->filled('privacy')) {
                $trip->privacy = $request->get('privacy');
            }
            $trip->save();
            $trip = new Item($trip, $this->_triptransformer);
            $this->_fractal->parseIncludes($request->get('include', ''));
            $trip = $this->_fractal->createData($trip);
            $trip = $trip->toArray();
            return Response::json([
                'success' => true,
                'message' => 'Trip edited Succesfully',
                'trip' => $trip
            ], 200);
        }

    public function updateCoverImage(Request $request, $id)
    {
       // cover_photo_path
        $user = JWTAuth::parseToken()->toUser();
        $trip = trip::find($id);
        if(!$trip){
            return Response::json([
                'error' => [
                    'message' => 'Trip does not exist'
                ]
            ], 404);
        }
        if ($request->file('cover_photo')) {
            $image = $request->file('cover_photo');
            $extension = $image->getClientOriginalExtension();

            if (!($extension == 'jpg' || $extension == 'png')) {
                return Response::json([
                    'message' => 'Only upload jpg or png images please'
                ], 422);
            } else {
                $bytes = random_bytes(10);
                $img_name = bin2hex($bytes);
                $folder = 'u_' . $user->id . '/trips/'. $trip->id .'/coverimage';

                $save = new ImageSave(600, 400, $folder , $img_name . '.' . $extension, $image);
                $save->clearFolder($folder);
                $coverphoto = $save->saveImage();
                $trip->cover_photo_path = $coverphoto;
                $trip->save();
                $trip = new Item($trip, $this->_triptransformer);
                $this->_fractal->parseIncludes($request->get('include', ''));
                $trip = $this->_fractal->createData($trip);
                $trip = $trip->toArray();
                return Response::json([
                    'success' => true,
                    'message' => 'Tripcover edited Succesfully',
                    'trip' => $trip
                ], 200);
            }
        }
    }
}
