<?php

use Illuminate\Http\Request;

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
Route::resource('buyers','Buyer\BuyerController');

/*
 * Categories
 */
Route::resource('categories','Category\CategoryController');

Route::resource('categories.products','Category\CategoryProductController',['only'=>['index']]);
Route::resource('categories.sellers','Category\CategorySellerController',['only'=>['index']]);
Route::resource('categories.transactions','Category\CategoryTransactionController',['only'=>['index']]);
Route::resource('categories.buyers','Category\CategoryBuyerController',['only'=>['index']]);
/*
 * Products
 */
Route::resource('products','Product\ProductController');
Route::resource('products.transactions','Product\ProductTransactionController',['only'=>['index']]);
Route::resource('products.buyers','Product\ProductBuyerController',['only'=>['index']]);
Route::resource('products.categories','Product\ProductCategoryController',['except'=>['create','show','edit']]);
Route::resource('products.buyers.transactions','Product\ProductBuyerTransactionController',['only'=>['store']]);
/*
 * Transections
 */
Route::resource('transactions','Transaction\TransactionController');


/*
 * Transections
 */
Route::resource('transactions.categories','Transaction\TransactionCategoryController',['only'=>['index']]);
Route::resource('transactions.sellers','Transaction\TransactionSellerController',['only'=>['index']]);


Route::resource('buyers.transactions','Buyer\BuyerTransactionController',['only'=>['index']]);
Route::resource('buyers.products','Buyer\BuyerProductController',['only'=>['index']]);
Route::resource('buyers.sellers','Buyer\BuyerSellerController',['only'=>['index']]);
Route::resource('buyers.categories','Buyer\BuyerCategoryController',['only'=>['index']]);





Route::resource('sellers','Seller\SellerController');
Route::resource('sellers.transactions','Seller\SellerTransactionController',['only'=>['index']]);
Route::resource('sellers.categories','Seller\SellerCategoryController',['only'=>['index']]);
Route::resource('sellers.buyers','Seller\SellerBuyerController',['only'=>['index']]);
Route::resource('sellers.products','Seller\SellerProductController',['except'=>['create','show','edit']]);
/*
 * Users
 */
Route::resource('users','User\UserController');
Route::name('verify')->get('users/verify/{token}','User\UserController@verify');
Route::name('resend')->get('users/{user}/resend','User\UserController@resend');

Route::post('oauth/token','\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken')->name('oauth.token');
Route::post('user/login','User\userController@login')->name('oauth.login');
Route::post('user/refresh_token','User\userController@getRefreshToken')->name('oauth.refresh');
Route::post('user/logout','User\userController@logout')->name('oauth.logout');
