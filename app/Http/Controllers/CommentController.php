<?php

namespace App\Http\Controllers;

use App\stop;
use Illuminate\Http\Request;
use JWTAuth;
use App\Media;
use App\Comment;
use Response;
use Validator;
use App\Transformers\CommentTransformer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;

class CommentController extends Controller
{
    private $_fractal;
    private $_commentTransformer;

    function __construct(Manager $fractal, CommentTransformer $commentTransformer)
    {
        $this->_fractal = $fractal;
        $this->_fractal->setSerializer(new ArraySerializer());
        $this->_commentTransformer = $commentTransformer;
    }

    public function index(Request $request, $mediaid)
    {
        $user = JWTAuth::parseToken()->toUser();
        $media = Media::findOrFail($mediaid);
        $comments = new Collection($media->comments, $this->_commentTransformer);
        $this->_fractal->parseIncludes($request->get('include', 'user'));
        $comments = $this->_fractal->createData($comments);
        return Response::json([
                'comments' => $comments->toArray()
            ]
            , 200);
    }

    public function store(Request $request, $mediaid)
    {
        $user = JWTAuth::parseToken()->toUser();
        $media = Media::findOrFail($mediaid);
        $comment = new Comment();
        $rules = [
            'comment' => 'required',
        ];
        $validation = [
            'comment' => $request->get('comment'),
        ];
        $validator = Validator::make($validation, $rules);
        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['success' => false, 'error' => $error]);
        }
        $comment->comment = $request->get('comment');
        $comment->user_id = $user->id;
        $media->comments()->save($comment);
        $comment = new Item($comment, $this->_commentTransformer);
        $this->_fractal->parseIncludes('user');
        $comment = $this->_fractal->createData($comment);
        $comment = $comment->toArray();
        return Response::json([
            'success' => true,
            'message' => 'Comment Created Succesfully',
            'comment' => $comment
        ], 200);
    }
}
