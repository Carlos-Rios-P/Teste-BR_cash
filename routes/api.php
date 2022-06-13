<?php

use App\Http\Controllers\TransactionController;
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

Route::prefix('/transactions')->group( function (){
    Route::post('/', [TransactionController::class, 'store']);
    Route::post('/{id}/capture', [TransactionController::class, 'captureAmount']);
    Route::get('/index', [TransactionController::class, 'index']);
    Route::get('/show/{id}', [TransactionController::class, 'show']);
    Route::put('/update/{id}', [TransactionController::class, 'update']);
    Route::delete('/delete/{id}', [TransactionController::class, 'destroy']);
});


