<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostController;
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

Route::get('/getlatest', [PostController::class, 'getLatest']);
Route::get('/getrecent', [PostController::class, 'getRecent']);
Route::get('/getbytag/{tag}', [PostController::class, 'getByTag']);
Route::get('/gethome', [PostController::class, 'getHome']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});
