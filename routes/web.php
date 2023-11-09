<?php

use App\Models\User;
use App\Objects\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * Route to generate a test curl request.
 */
Route::get('test', function () {
    // get first user or create if not exists
    $user = User::first();
    if ($user === null) {
        $user = User::factory()->create();
    }

    // generate a Mail object
    $mail = Mail::factory()->make();

    // print curl request
    echo '<pre>';
    echo 'curl -XPOST -H "Content-type: application/json" -d \'
    {
        "emails": [
            {
                "email": "'.$mail->email.'",
                "subject": "'.$mail->subject.'",
                "body": "'.$mail->body.'"
            }
        ]
    }\' \'http://localhost/api/'.$user->id.'/send?api_token='.$user->api_token.'\'';
    echo '</pre>';
});
