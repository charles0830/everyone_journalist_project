<?php

namespace App\Transformers;

use App\Post;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Post $post)
    {
        return [
            //
            'id' => (int) $post->id,
            'title' => (string) $post->title,
            'details' => (string) $post->description,
            'cover_photo'=>(string) $post->cover_image,
            'user_id' => (int)$post->user_id,
            'creationDate' => (string)$post->created_at,
            'lastChange' => (string) $post->updated_at,
            'deletedDate' => isset($post->deleted_at)? (string) $post->deleted_at:null,

        ];
    }
    public static function  originalAttribute($index){
        $attributes = [
            'id' =>  'id',
            'title' =>  'title',
            'details' =>  'description',
            'creationDate' =>  'created_at',
            'lastChange' =>  'updated_at',
            'deletedDate' => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
