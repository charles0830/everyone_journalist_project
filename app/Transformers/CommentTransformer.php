<?php

namespace App\Transformers;

use App\Category;
use App\Comment;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Comment $comment
     * @return array
     */
    public function transform(Comment $comment)
    {
        $data = $comment->toArray();
        $information =  [
            //
            'id' => (int) $comment->id,
            'details' => (string) $comment->description,
            'user_id' => (int)$comment->user_id,
            'post_id' => (int)$comment->post_id,
            'creationDate' => (string)$comment->created_at,
            'lastChange' => (string) $comment->updated_at,
            'deletedDate' => isset($comment->deleted_at)? (string) $comment->deleted_at:null,

        ];

        if(isset($data['user'])){
            //  dd($post->user);
            $userTransformer = new UserTransformer();
            $information['creator_info'] = $userTransformer->transform($comment->user);
        }
        return $information;
    }
    public static function  originalAttribute($index){
        $attributes = [
            'id' =>  'id',
            'details' =>  'description',
            'creationDate' =>  'created_at',
            'lastChange' =>  'updated_at',
            'deletedDate' => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
