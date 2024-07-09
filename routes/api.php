<?php

use App\Http\Controllers\AdvController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\APIController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\PaymentController;
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
//Route::get('payment/bazaar/token', [PaymentController::class, 'getBazaarToken'])->name('v2.payment.bazaar.token');


//Route::get('payment/getFirstBazaarToken', [PaymentController::class, 'getFirstBazaarToken'])->name('payment.done');
Route::any('payment/done', [PaymentController::class, 'payDone'])->name('payment.done');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('payment/create', [PaymentController::class, 'create'])->name('payment.create');
    Route::get('payment/transactions', [PaymentController::class, 'transactions'])->name('payment.transaction.search');

    Route::post('addtovip', [AppController::class, 'addToVip']);
    Route::post('addtodivar', [AppController::class, 'addToDivar']);
    Route::get('getsettings', [AppController::class, 'getSettings']);
    Route::get('getdivar', [AppController::class, 'getDivar']);
    Route::get('user/info', [AppController::class, 'getUser']);
    Route::post('user/changepassword', [UserController::class, 'changePassword'])->name('user.password.change');
    Route::post('user/updateemail', [UserController::class, 'updateEmail'])->name('user.email.update');
    Route::post('user/updateavatar', [UserController::class, 'updateAvatar'])->name('user.avatar.update');
    Route::post('user/update', [UserController::class, 'update'])->name('user.update');

    Route::post('logout', [APIController::class, 'logout']);
    Route::post('checkuserjoined', [AppController::class, 'checkuserJoined']);
    Route::post('viewchat', [AppController::class, 'viewChat']);
    Route::post('newchat', [AppController::class, 'newChat']);
    Route::get('getuserchats', [AppController::class, 'getUserChats']);
    Route::post('refreshchat', [AppController::class, 'refreshChat']);
    Route::post('updatescore', [AppController::class, 'updateScore']);
    Route::post('penalty', [AppController::class, 'leftUsersPenalty']);
    Route::post('deletechat', [AppController::class, 'deleteChat']);

    Route::post('adv/click', [AdvController::class, 'click'])->name('api.adv.click');
    Route::get('adv/get', [AdvController::class, 'get'])->name('api.adv.get');

});
Route::post('/bot/getupdates', [BotController::class, 'getupdates']);
Route::post('/bot/sendmessage', [BotController::class, 'sendmessage']);
Route::get('/bot/getme', [BotController::class, 'myInfo']);


Route::middleware('throttle:sms_limit')->group(function () {
    Route::post('senderror', [APIController::class, 'sendError']);
    Route::post('login', [UserController::class, 'login']);

});
