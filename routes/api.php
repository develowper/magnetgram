<?php

use App\Http\Controllers\APIController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\BotController;
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

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('addtodivar', [AppController::class, 'addToDivar']);
    Route::get('getsettings', [AppController::class, 'getSettings']);
    Route::get('getdivar', [AppController::class, 'getDivar']);
    Route::get('getuser', [AppController::class, 'getUser']);
    Route::post('logout', [APIController::class, 'logout']);
    Route::post('checkuserjoined', [AppController::class, 'checkuserJoined']);
    Route::post('viewchat', [AppController::class, 'viewChat']);
    Route::post('newchat', [AppController::class, 'newChat']);
    Route::get('getuserchats', [AppController::class, 'getUserChats']);
    Route::post('refreshchat', [AppController::class, 'refreshChat']);
    Route::post('updatescore', [AppController::class, 'updateScore']);
    Route::post('penalty', [AppController::class, 'leftUsersPenalty']);
    Route::post('deletechat', [AppController::class, 'deleteChat']);

});
Route::post('/bot/getupdates', [BotController::class, 'getupdates']);
Route::post('/bot/sendmessage', [BotController::class, 'sendmessage']);
Route::get('/bot/getme', [BotController::class, 'myInfo']);
