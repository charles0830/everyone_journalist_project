<?php

namespace App\Transformers;

use App\Post;
use App\User;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    protected $availableIncludes = [
        'user',
    ];
    public function transform(Post $post)
    {

        $data  = $post->toArray();
        $information =  [
            //
            'id' => (int) $post->id,
            'title' => (string) $post->title,
            'details' => (string) $post->description,
            'cover_photo'=>(string) asset('img/post')."/".$post->cover_image,
            'user_id' => (int)$post->user_id,
            'creationDate' => (string)$post->created_at,
            'lastChange' => (string) $post->updated_at,
            'deletedDate' => isset($post->deleted_at)? (string) $post->deleted_at:null,
        ];

        if(isset($post->comments_count)){
            $information['comment_count']= $post->comments_count;
        }



        if(isset($data['user'])){
          //  dd($post->user);
            $userTransformer = new UserTransformer();
            $information['creator_info'] = $userTransformer->transform($post->user);
        }
        if(isset($data['categories'])){
            $categoryTransformer = new CategoryTransformer();
            $categories  = fractal($post->categories, $categoryTransformer);
            $categories = $categories->toArray();
            $information['categories'] = $categories['data'];
        }
        if( isset($data['comments'])){
            $commentTransformer = new CommentTransformer();
            $comments  = fractal($post->comments, $commentTransformer);
            $comments = $comments->toArray();
            $information['comments'] = $comments['data'];
        }
        return $information;
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

    public function includeUser(Post $post)
    {
        $author = $post->user;

        return $this->item($author, new UserTransformer);
    }
}
