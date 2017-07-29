<?php

namespace App;

use App\Transformers\CategoryTransformer;
use App\Transformers\CommentTransformer;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    protected $fillable = [
        'description',
        'user_id',
        'post_id'
    ];

    public $transformer = CommentTransformer::class;
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
