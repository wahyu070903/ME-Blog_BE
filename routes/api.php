<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::controller(PostController::class)->group(function(){
   Route::get('/getlatest', 'getLatest');
   Route::get('/getrecent', 'getRecent');
   Route::get('/getbytag', 'getByTag');
   Route::get('/gethome', 'getHome');
   Route::get('/paginate/{start}', 'paginate');
   Route::get('/count', 'count');
   Route::get('/getbyid/{id}', 'getById');
   Route::get('/deletebyid/{id}', 'deleteById');
   Route::post('/create-post','createPost');
   Route::get('/getnextprev/{current}','getNextandPrev');
   Route::post('/editpost/{id}', 'editPost');
});

Route::post('/upload', [EditorController::class, 'imageUpload']);
Route::delete('/image-delete', [EditorController::class, 'imageDelete']);

Route::controller(AuthController::class)->group(function(){
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    //  Route::get('/user', 'getUser')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});

