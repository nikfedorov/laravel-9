<?php

use App\Http\Controllers\EmailController;
use App\Http\Middleware\ValidateApiToken;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// send emails
Route::post('{user}/send', [EmailController::class, 'send'])
    ->name('api.send')
    ->middleware(ValidateApiToken::class);
