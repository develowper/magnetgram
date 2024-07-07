<?php

use App\Http\Helper;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

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

Route::get('/test', function () {

    foreach (\App\Models\Divar::get() as $item) {

//        if (Storage::exists("public/chats/$item->image.jpg")) {
//            Storage::move("public/chats/$item->image.jpg", "public/chats/$item->chat_id.jpg");
//            $item->image = $item->chat_id;
//        }

//            $c = \App\Models\Chat::where('chat_id', "$item->chat_id")->first();
//        if ($c) {
//            $item->image = $c->image;
//            $item->save();
//        }
    }

});
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
