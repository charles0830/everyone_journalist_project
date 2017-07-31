<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->middleware('auth:api');
        $user = User::all();
        return $this->showAll($user);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone_no'=>'required|min:10|numeric|unique:users',
            'image_thumb'=>'sometimes|required|image'
        ];
        $this->validate($request, $rules);
        $data = $request->all();

        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;
        if ($request->hasFile('image_thumb')) {
            $data['image_thumb'] = $request->image_thumb->store('');
        }else{
            $data['image_thumb'] = null;
        }
        $user = User::create($data);
        return $this->showOne($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $this->showOne($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'email' => 'email|unique:users,' . $user->id,
            'password' => 'min:6|confirmed'
        ];
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email') && $user->email != $request->email) {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }
        if (!$user->isDirty()) {
            return $this->errorResponse('you need to specify a diffenrt value to update code', 422);
        }
        $user->save();
        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        $this->showOne($user);
    }

    public function verify($token)
    {
        $user = User::where('verification_token', $token)->firstOrFail();
        $user->verified = User::VERIFIED_USER;
        $user->verification_token = null;
        $user->save();
        return $this->showMessage('The account has been verified successfully');
    }
    public function resend(User $user)
    {
        if ($user->isVerified()) {
            return $this->errorResponse("this user is already verified", 409);
        }
        $user->verification_token = User::generateVerificationCode();
        $user->save();
         Mail::to($user)->send(new UserCreated($user));
        return $this->showMessage("The verification email send");
    }


    public function login(Request $request)
    {
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];
        $this->validate($request, $rules);
        $client = new \GuzzleHttp\Client();
        $response = $client->post(route('oauth.token'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => env('client_id',3),
                'client_secret' => env('client_secret','E51pmXN0efl2soSQUJpmW4M6WVFaamDlCXQCifnU'),
                'username' => $request->username,
                'password' => $request->password,
            ],
            'http_errors' => false //add this to return errors in json
        ]);
        return $response;
        // return  json_decode((string) $response->getBody(), true);
    }
    public function getRefreshToken(Request $request)
    {
        $rules = [
            'token' => 'required',
        ];
        $this->validate($request, $rules);
        $client = new \GuzzleHttp\Client();
        $response = "";
        // try{
        $response = $client->post(route('oauth.token'), [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $request->token,
                'client_id' => env('client_id'),
                'client_secret' => env('client_secret'),
            ],
            'http_errors' => false //add this to return errors in json
        ]);
        return $response;
//        }catch (\Exception $e){
//
//            return \GuzzleHttp\json_decode(json_encode($e->getMessage()));
//            return $e->getMessage();
//            return json_encode(json_decode($e->getMessage()));
//        }
    }
    public function logout(Request $request)
    {
        $accessToken = Auth::user()->token();
        $refreshToken = DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update([
                'revoked' => true
            ]);
        $accessToken->revoke();
        return  $this->showMessage("you are logout successfully",200);
    }
}
