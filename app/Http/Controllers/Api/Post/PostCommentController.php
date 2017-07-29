<?php

namespace App\Http\Controllers\Api\Post;

use App\Comment;
use App\Http\Controllers\ApiController;
use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostCommentController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Post $post
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Post $post)
    {

        $rules = [
            'description' => 'required',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $data['user_id'] = 1;
        $comment = $post->comments()->create($data);
        return $this->showOne($comment);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post,Comment $comment)
    {
        $comment->fill($request->intersect([
            'description'
        ]));
        if($comment->isClean()){
            return $this->errorResponse('You need to specify a different value to update',422);
        }
        $comment->update();
        return $this->showOne($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post,Comment $comment)
    {
        $comment->delete();
        return $this->showOne($comment);
    }
}
