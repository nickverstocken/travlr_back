<?php

namespace App\Http\Controllers;

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
        $trips = Trip::all()->where('user_id', $user->id);
        $trips = new Collection($trips, $this->_triptransformer);
        $this->_fractal->parseIncludes($request->get('include', ''));
        $trips = $this->_fractal->createData($trips);
        return Response::json(
            $trips->toArray()
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
        if($trip){
            $trip = new Item($trip, $this->_triptransformer);
            $this->_fractal->parseIncludes($request->get('include', ''));
            $trip = $this->_fractal->createData($trip);
            $trip = $trip->toArray();
        }
        if ($trip['user_id'] == $user['id']) {
            return Response::json([
                'trip' => $trip
            ], 200);
        }else{
            return Response::json([
                'error' => [
                    'message' => "You don't have the rights to view this trip"
                ]
            ], 403);
        }
    }
}
