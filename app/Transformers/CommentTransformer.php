<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 10/11/17
 * Time: 3:24
 */

namespace App\Transformers;
use App;
use App\Comment;
use League\Fractal\TransformerAbstract;
class CommentTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'user'
    ];
    public function transform(Comment $comment)
    {
        return [
            'id' => $comment->id,
            'media_id' => $comment->media_id,
            'comment' => $comment->comment,
            'date' => $comment->created_at->toDateTimeString()
        ];
    }
    public function includeUser(Comment $comment)
    {
        if(!$comment->user){
            return null;
        }
        return $this->item($comment->user, App::make(UserTransformer::class));
    }
}