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
        $data['user_id'] = $request->user()->id;
        $comment = $post->comments()->create($data);



        $notifyUser= User::all()->except($request->user()->id)->pluck('device_token')->toArray();
        $notificationInformation = [
            'title'=> 'Hurray!! new comment created',
            'body' => $request->user()->name ." comment now",
            'type' =>'post'
        ];
        sendPushNotification($notifyUser,$notificationInformation);
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

        if($request->user()->id!=$comment->user_id){
            return $this->errorResponse("you are not authendicate to perform this operation",402);
        }
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
        if(app()->request->user()->id!=$comment->user_id){
            return $this->errorResponse("you are not authendicate to perform this operation",402);
        }
        $comment->delete();
        return $this->showOne($comment);
    }
}
