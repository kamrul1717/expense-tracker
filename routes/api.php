
<?php
use Illuminate\Support\Facades\Route;
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('transactions', 'API\TransactionController');
    Route::get('/summary', 'API\SummaryController@index');
});