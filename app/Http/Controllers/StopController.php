<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transformers\StopTransformer;
use App\trip;
use App\stop;
use App\location;
use App\Media;
use App\User;
use League\Fractal\Resource\Item;
use Mockery\Exception;
use Response;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use JWTAuth;
use Validator;
use App\ImageSave;
use DB;
class StopController extends Controller
{
    private $_fractal;
    private $_stopTransformer;
    function __construct(Manager $fractal, StopTransformer $stopTransformer)
    {
        $this->_fractal = $fractal;
        $this->_fractal->setSerializer(new ArraySerializer());
        $this->_stopTransformer = $stopTransformer;
    }
    public function store($tripid, Request $request)
    {

        $user = JWTAuth::parseToken()->toUser();
        $trip = Trip::findOrFail($tripid);
        if($trip && $trip->user_id == $user->id){
        $stop = new stop;
        $location = new location;
        $stop->name = $request->get('name');
        $rules = [
            'name' => 'required|max:255',
            'location' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'arrival_time' => 'required'
        ];
        $validation = [
            'name'=> $request->get('name'),
            'location'=> $request->get('location'),
            'lat'=> $request->get('lat'),
            'lng'=> $request->get('lng'),
            'arrival_time' => $request->get('arrival_time')
        ];
        $validator = Validator::make($validation, $rules);
        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['success' => false, 'error' => $error]);
        }
        $stop->name = $request->get('name');
        $location->name = $request->get('location');
        $location->lat = $request->get('lat');
        $location->lng = $request->get('lng');
        $stop->arrival_time = $request->get('arrival_time');
        if($request->get('description')){
            $stop->description = $request->get('description');
        }
        if($request->get('location')){
            $location->name = $request->get('location');
        }
        $location->save();
        $stop->location()->associate($location);
        $stop = $trip->stops()->save($stop);

        if($stop){
            $stop = new Item($stop, $this->_stopTransformer);
            $this->_fractal->parseIncludes('media');
            $stop = $this->_fractal->createData($stop);
            $stop = $stop->toArray();
        }
        return Response::json([
            'success' => true,
            'message' => 'Stop Created Succesfully',
            'stop' => $stop
        ], 200);
    }
    }
    public function saveImages(Request $request, $stopId)
    {
        $user = JWTAuth::parseToken()->toUser();
        $files = $request->file('images');
        $stop = stop::findOrFail($stopId);
        $media = null;
        $trip = $stop->trip()->first();
        if ($trip && $trip->user_id == $user->id) {
            $nbr = count($files) - 1;
            DB::beginTransaction();
            foreach (range(0, $nbr) as $index) {
                $extension = $files[$index]->getClientOriginalExtension();
                if (!($extension == 'jpg' || $extension == 'png')) {
                    DB::rollBack();
                    return Response::json([
                        'message' => 'Only upload jpg or png images please'
                    ], 422);
                } else {
                    $bytes = random_bytes(10);
                    $img_name = bin2hex($bytes);
                    $folder = 'u_' . $user->id . '/trips/'. $trip->id .'/stops/' . $stopId;
                    $save = new ImageSave(1000, 600, $folder, $img_name . '.' . $extension, $files[$index]);
                    $save_thumb = new ImageSave(100, 60, $folder, $img_name . '_thumb.' . $extension, $files[$index]);
                    $media = new Media();
                    $media->image = $save->saveImage();
                    $media->image_thumb = $save_thumb->saveImage();
                    $stop->media()->save($media);
                    $trip->cover_photo_path = $save->saveImage();
                }
            }
            DB::Commit();
            if($stop){
                $stop = new Item($stop, $this->_stopTransformer);
                $this->_fractal->parseIncludes('media');
                $stop = $this->_fractal->createData($stop);
                $stop = $stop->toArray();
            }
            return Response::json([
                'success' => true,
                'message' => 'Images Created Succesfully',
                'stop' => $stop
            ], 200);
        }
    }
}
