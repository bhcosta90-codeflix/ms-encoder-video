<?php

use App\Http\Controllers\Controller;
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

Route::get('/', [Controller::class, 'get']);

Route::get('/download', function(){
    dispatch(new \App\Jobs\VideoDownload(request()->file));
});

Route::get('/fragment', function(){
    dispatch(new \App\Jobs\VideoFragment(request()->file));
});

Route::get('/converter', function(){
    dispatch(new \App\Jobs\VideoConverter(request()->file));
});

Route::get('/upload', function(){
    dispatch(new \App\Jobs\VideoUpload(request()->file));
});
