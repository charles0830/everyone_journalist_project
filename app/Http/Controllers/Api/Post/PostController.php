<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Controllers\ApiController;
use App\Post;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::withCount('comments')->with('categories','user')->get();


        return $this->showAll($posts);


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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'description' => 'required|min:20',
            'cover_image' => 'required|image'
        ];
        $this->validate($request, $rules);

        $data = $request->all();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->cover_image->store('','post_images');
        }
        $data['user_id'] = 1;
        $post = Post::create($data);
        return $this->showOne($post);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {

        $post = $post->load('comments.user','user');
        return $this->showOne($post);
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
    public function update(Request $request, Post $post)
    {
        $rules = [
            'description' => 'min:20',
            'cover_image' => 'image'
        ];

        $this->validate($request, $rules);
        $post->fill($request->intersect([
            'title','description','cover_image'
        ]));

        if($request->hasFile('image')){
            Storage::delete($post->image,'post_images');
            $post->image = $request->cover_image->store('','post_images');
        }
        if($post->isClean()){
            return $this->errorResponse('You need to specify a different value to update',422);
        }
        $post->save();
        return $this->showOne($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return $this->showOne($post);
    }
}
