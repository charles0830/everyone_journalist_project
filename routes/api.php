<?php

//use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

/*
 * Buyers
 */


/*
 * Categories
 */

/*
 * registration and login route
 */
Route::post('registration', 'Api\User\UserController@store')->name('user.registration');
Route::get('users/{user}/resend', 'Api\User\UserController@resend')->name('resend');
Route::name('verify')->get('users/verify/{token}', 'Api\User\UserController@verify');
Route::post('login', 'Api\User\userController@login')->name('oauth.login');
Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('users', 'Api\User\UserController', ['except' => 'store']);
});

Route::resource('posts', 'Api\Post\PostController');


Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('posts.comments', 'Api\Post\PostCommentController', ['only' => ['store', 'update','destroy']]);
});


Route::resource('categories', 'Api\Category\CategoryController');
Route::resource('categories.posts', 'Api\Category\CategoryPostController', ['only' => ['index']]);




//Route::post('user/refresh_token','User\userController@getRefreshToken')->name('oauth.refresh');
Route::post('user/logout', 'Api\User\userController@logout')->name('oauth.logout');
Route::post('user/refresh_token', 'Api\User\userController@getRefreshToken')->name('oauth.refresh');
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken')->name('oauth.token');


