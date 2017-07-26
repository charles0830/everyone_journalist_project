<?php

namespace App;

use App\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    public $transformer = CategoryTransformer::class;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $hidden = [
        'pivot'
    ];
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
