<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

Route::post('paymentNotification', function (Request $request) {
    if (empty($request->all())) {
        Log::info('Empty Payload');
    } else {
        Log::info('Request Payload: ' . json_encode($request->all()));
    }

    // Dump relevant parts of the request payload
    dump($request->all());

    // Access JSON payload
    $jsonPayload = $request->json()->all();
    dump($jsonPayload);

    // Access form data
    $formData = $request->input();
    dump($formData);

    $data = file_get_contents('php://input');
    if ($data) {
        $decodedData = json_decode($data, true);
        if ($decodedData !== null) {
            // Logging JSON payload
            Log::info('Request Payload (JSON): ' . json_encode($decodedData));
            // Additional echoing for demonstration purposes
            echo 'Request Payload (JSON):<br>';
            print_r($decodedData);
        } else {
            // Logging form data
            Log::info('Request Payload (Form Data): ' . json_encode($formData));
            // Additional echoing for demonstration purposes
            echo 'Request Payload (Form Data):<br>';
            print_r($formData);
        }
    } else {
        // Logging no request payload
        Log::info('No Request Payload');
        // Additional echoing for demonstration purposes
        echo 'No Request Payload';
    }
});

Route::get('/', function () {
    // return view('welcome');
    /*return redirect()->to('web-site/index.html');*/
    return redirect()->to('index.html');
});
Route::get('/docs', function () {
    return view('docs');
});

Route::get('/clear-all-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    return 'All cache cleared';
});
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ], function () {
    Auth::routes();

    Route::get('/l', [App\Http\Controllers\HomeController::class, 'index']);


    foreach (['/home', '/register/service-seeker'] as $route) {
        Route::get($route, function () {
//            return redirect()->to('index.html');
        });
    }


    Route::get('/l', [App\Http\Controllers\HomeController::class, 'index']);

});

