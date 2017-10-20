<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Controllers\ApiController;
use App\Post;
use App\User;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class PostController extends ApiController
{
    /**
     * PostController constructor.
     */
    public function __construct()
    {

        $this->middleware('auth:api', ['except' => ['index','show']]);
        // $this->middleware(['CheckUserOwnRequest'], ['only' => ['update','destroy']]);
    }

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

        $file = $request->file('cover_image');
        $image = Image::make($file);
        $image->encode('jpg',50);


        $fileName = uniqid('img_').".jpg";
       // $image->c
        $image->save(public_path('img/'.$fileName));
//        $image->resize(300, 200, function ($constraint) {
//            $constraint->aspectRatio();
//        });
//        // save resized
//        $image->save(public_path('img/'."resize.png"));
        $data['cover_image'] = $fileName;


        $data['user_id'] = $request->user()->id;
        $post = Post::create($data);
        $notifyUser= User::all()->except($request->user()->id)->pluck('device_token')->toArray();
        $notificationInformation = [
            'title'=> 'Hurray!! new post created',
            'body' => $post->title." created by ".$request->user()->name,
            'type' =>'post'
        ];
        sendPushNotification($notifyUser,$notificationInformation);
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
        if($request->user()->id != $post->user_id){
            return $this->errorResponse("you are not authendicate to perform this operation",402);
        }
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
        if(app()->request->user()->id != $post->user_id){
            return $this->errorResponse("you are not authendicate to perform this operation",402);
        }
        $post->delete();
        return $this->showOne($post);
    }
}
