<?php

namespace App\Http\Controllers;

use App\Transformers\TripTransformer;
use Illuminate\Http\Request;
use App\trip;
use App\Http\Transform;
use League\Fractal\Resource\Item;
use Response;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
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
        $trips = Trip::all();
        $trips = new Collection($trips, $this->_triptransformer);
        $this->_fractal->parseIncludes($request->get('include', ''));
        $trips = $this->_fractal->createData($trips);
        return Response::json(
            $trips->toArray()
        , 200);
    }
    public function show($id, Request $request){
        $trip = Trip::with(['user', 'stops' => function($q){
            $q->with('media');
        }])->select('*')->find($id);
        $trip = new Item($trip, $this->_triptransformer);
        $this->_fractal->parseIncludes($request->get('include', ''));
        $trip = $this->_fractal->createData($trip);
        $trip2 = Trip::with(['user', 'stops' => function($q){
            $q->with('media');
        }])->select('*')->find($id);
        if(!$trip){
            return Response::json([
                'error' => [
                    'message' => 'Trip does not exist'
                ]
            ], 404);
        }
        return Response::json(
            $trip->toArray()
        , 200);
    }
}
