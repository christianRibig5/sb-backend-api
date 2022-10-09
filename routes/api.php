<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//********User Registration and Authentication*********
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

// ***********User Data Manipulation********
Route::get('/users',[UserController::class,'getAllUsers'])->middleware('auth:api');
Route::get('/user/{id}',[UserController::class,'getUserByID'])->middleware('auth:api');
Route::post('/update/user/{id}',[UserController::class,'updateUserByID'])
->middleware('auth:api');
Route::post('/update/avatar/{id}',[UserController::class,'updateAvatarByID'])
->middleware('auth:api');

// ******* Makaing post and post route****

Route::get('/posts',[PostController::class,'getAllPostFeeds'])->middleware('auth:api');
Route::post('/make/post',[PostController::class,'makePost'])->middleware('auth:api');
Route::post('/update/post/{id}',[PostController::class,'updatePostByID'])
->middleware('auth:api');
Route::post('/update/user/post/{user_id}',[PostController::class,'updatePostByUserID'])->middleware('auth:api');
Route::delete('/post/{id}',[PostController::class,'destroy'])->middleware('auth:api');
Route::get('/post/{id}',[PostController::class,'getPostByID'])->middleware('auth:api');

//********** Comment routes*/

Route::get('/comments',[CommentController::class,'getAllComments'])->middleware('auth:api');
Route::post('/make/comment',[CommentController::class,'makeComment'])->middleware('auth:api');



