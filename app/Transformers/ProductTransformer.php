<?php

namespace App\Transformers;

use App\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Product $product
     * @return array
     */
    public function transform(Product $product)
    {
        return [

            'id' => (int) $product->id,
            'title' => (string) $product->name,
            'details' => (string) $product->description,
            'stock' => (int) $product->quantity,
            'situation' => (int) $product->status,
            'picture' => url("img/{$product->image}"),
            'seller' => (int) $product->seller_id,
            'creationDate' => $product->created_at,
            'lastChange' => $product->updated_at,
            'deletedDate' => isset($product->updated_at)? (string) $product->deleted_at:null,

        ];
    }

    public static function  originalAttribute($index){
        $attributes = [
            'id' =>  'id',
            'title' =>  'name',
            'details' =>  'description',
            'stock' =>  'quantity',
            'seller' =>  'seller_id',
            'creationDate' =>  'created_at',
            'lastChange' =>  'updated_at',
            'deletedDate' => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
