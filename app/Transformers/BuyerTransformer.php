<?php

namespace App\Transformers;

use App\Buyer;
use League\Fractal\TransformerAbstract;

class BuyerTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Buyer $buyer)
    {
        return [
            //
            'id' => (int) $buyer->id,
            'name' => (string) $buyer->name,
            'email' => (string) $buyer->email,
            'isVerified' => (int) $buyer->verified,
            'creationDate' => $buyer->created_at,
            'lastChange' => $buyer->updated_at,
            'deletedDate' => isset($buyer->updated_at)? (string) $buyer->deleted_at:null,

        ];
    }

    public static function  originalAttribute($index){
        $attributes = [
            'id' =>  'id',
            'name' =>  'name',
            'email' =>  'email',
            'isVerified' =>  'verified',
            'creationDate' =>  'created_at',
            'lastChange' =>  'updated_at',
            'deletedDate' => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
