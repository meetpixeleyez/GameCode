<?php

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

Route::namespace('Api')->name('api.')->group(function () {
    Route::controller('PurchaseController')->prefix('verify-purchase-code')->group(function () {
        Route::post('/', 'verifyPurchasedCode')->name('purchase.code.verify');
    });

    Route::controller('AuthorController')->prefix('author')->group(function () {
        Route::get('/products/all', 'productsAll')->name('author.products.all');
        Route::get('/product/details', 'productsDetail')->name('author.products.detail');
    });
});
