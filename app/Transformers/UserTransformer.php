<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        return [
            //
            'id' => (int) $user->id,
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'phone_no' => (string) $user->phone_no,
            'isVerified' => (int) $user->verified,
            'isAdmin' => ($user->admin==='true'),
            'large_cover'=>route('imagecache',['large',$user->image_thumb]),
            'medium_cover'=>route('imagecache',['medium',$user->image_thumb]),
            'small_cover'=>route('imagecache',['small',$user->image_thumb]),
            'cover_photo'=>$user->image_thumb,
            'creationDate' => (string) $user->created_at,
            'lastChange' => (string) $user->updated_at,
            'deletedDate' => isset($user->deleted_at)? (string) $user->deleted_at:null,

        ];
    }

    public static function  originalAttribute($index){
        $attributes = [
            'id' =>  'id',
            'name' =>  'name',
            'email' =>  'email',
            'isVerified' =>  'verified',
            'isAdmin' => 'admin',
            'creationDate' =>  'created_at',
            'lastChange' =>  'updated_at',
            'deletedDate' => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
