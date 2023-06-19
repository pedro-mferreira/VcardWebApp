<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VcardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Payment_TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DefaultCategoryController;
use App\Http\Controllers\NotificationController;
use App\Models\Administrator;
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

Route::post('/{vcard}/notifications', [NotificationController::class, 'store']);
Route::get('/vcards/{vcard}/notifications', [NotificationController::class, 'getVcardNotifications']);
Route::put('/vcards/{vcard}/notifications', [NotificationController::class, 'update']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users/me', [UserController::class, 'show_me']);
    //TRANSACTIONS
    Route::get('/transactionsStatistics', [TransactionController::class, 'getStatistics']);
    Route::get('/vcards/{vcard}/transactions/{transaction}', [TransactionController::class, 'getOne']);
    Route::get('/vcards/{vcard}/transactions', [TransactionController::class, 'getVcardTransactions']);
    Route::post('/vcards/{vcard}/transactions', [TransactionController::class, 'create']);
    Route::put('/vcards/{vcard}/transactions/{transaction}', [TransactionController::class, 'update']);
    //VCARDS
    Route::get('/vcards', [VcardController::class, 'getAll']);
    Route::get('/vcards/{vcard}', [VcardController::class, 'getOne']);
    Route::put('/vcards/{vcard}', [VcardController::class, 'update']);
    Route::put('/vcards/{vcard}/piggyBank', [VcardController::class, 'updatePiggyBank']);
    Route::put('/vcards/{vcard}/activactionPiggyBank', [VcardController::class, 'updateActivactionPiggyBank']);
    Route::delete('/vcards/{vcard}', [VcardController::class, 'delete']);
    //Categories
    Route::get('/categories', [CategoryController::class, 'getAll']);
    Route::get('/vcards/{vcard}/categories', [CategoryController::class, 'getVcardCategories']);
    Route::get('/categories/{category}', [CategoryController::class, 'getOne']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/vcards/{vcard}/transactions/{transaction}/category', [CategoryController::class, 'getTransactionCategory']);
    //Administrators
    Route::get('/administrators', [AdministratorController::class, 'getAll']);
    Route::get('/administrators/{administrator}', [AdministratorController::class, 'getOne']);
    Route::put('/administrators/{administrator}', [AdministratorController::class, 'update']);
    Route::put('/administrators/{administrator}/password', [AdministratorController::class, 'updatePassword']);
    Route::put('/administrators/vcards/{vcard}', [AdministratorController::class, 'updateVcard']);
    Route::post('/administrators', [AdministratorController::class, 'store']);
    Route::delete('/administrators/{administrator}', [AdministratorController::class, 'destroy']);
    //Payment_types
    Route::get('/payment-types', [Payment_TypeController::class, 'getAll']);
    //DefaultCategories
    Route::get('/defaultCategories', [DefaultCategoryController::class, 'getAll']);
    Route::post('/defaultCategories', [DefaultCategoryController::class, 'store']);
    Route::get('/defaultCategories/{category}', [DefaultCategoryController::class, 'getOne']);
    Route::put('/defaultCategories/{category}', [DefaultCategoryController::class, 'update']);
    Route::delete('/defaultCategories/{category}', [DefaultCategoryController::class, 'destroy']);
});


//Not Authenticate
Route::post('/vcards', [VcardController::class, 'create']);
Route::post('/vcards/{vcard}/updatePhoto', [VcardController::class, 'updatePhoto']);











