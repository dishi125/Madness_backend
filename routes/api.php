<?php

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/


Route::post('login', [\App\Http\Controllers\API\AuthController::class, 'login']);
Route::post('verify_otp',[\App\Http\Controllers\API\UserController::class,'verify_otp']);
Route::post('send_otp',[\App\Http\Controllers\API\UserController::class,'send_otp']);
Route::post('login', [\App\Http\Controllers\API\AuthController::class, 'login']);
Route::get('home',[\App\Http\Controllers\API\ProductController::class,'home']);
Route::post('product_details',[\App\Http\Controllers\API\ProductController::class,'product_details']);
Route::post('view_category',[\App\Http\Controllers\API\CategoryController::class,'view_category']);
Route::post('all_categories',[\App\Http\Controllers\API\CategoryController::class,'all_categories']);
Route::post('search',[\App\Http\Controllers\API\ProductController::class,'search']);
Route::get('collections',[\App\Http\Controllers\API\UserController::class,'collections']);
Route::post('view_products',[\App\Http\Controllers\API\ProductController::class,'view_products']);
Route::get('faq_list',[\App\Http\Controllers\API\FaqController::class,'faq_list']);
Route::post('filter_category',[\App\Http\Controllers\API\CategoryController::class,'filter_category']);
Route::post('filter_products',[\App\Http\Controllers\API\ProductController::class,'filter_products']);

Route::group(['middleware' => 'auth:api'], function(){
    Route::post('edit_profile',[\App\Http\Controllers\API\UserController::class,'edit_profile']);
    Route::post('view_profile',[\App\Http\Controllers\API\UserController::class,'view_profile']);

    Route::post('update_cart',[\App\Http\Controllers\API\CartController::class,'update_cart']);
    Route::post('remove_cart',[\App\Http\Controllers\API\CartController::class,'remove_cart']);
    Route::get('cartitem_list',[\App\Http\Controllers\API\CartController::class,'cartitem_list']);

    Route::post('update_wishlist',[\App\Http\Controllers\API\WishlistController::class,'update_wishlist']);
    Route::get('wishlistitem_list',[\App\Http\Controllers\API\WishlistController::class,'wishlistitem_list']);

    Route::post('update_address',[\App\Http\Controllers\API\CustomerAddressController::class,'update_address']);
    Route::post('remove_address',[\App\Http\Controllers\API\CustomerAddressController::class,'remove_address']);
    Route::post('address_list',[\App\Http\Controllers\API\CustomerAddressController::class,'address_list']);

    Route::post('submit_refcode',[\App\Http\Controllers\API\UserController::class,'submit_refcode']);

    Route::post('apply_coupon',[\App\Http\Controllers\API\OrderController::class,'apply_coupon']);

    Route::post('create_order',[\App\Http\Controllers\API\OrderController::class,'create_order']);
    Route::post('order_list',[\App\Http\Controllers\API\OrderController::class,'order_list']);
    Route::post('order_details',[\App\Http\Controllers\API\OrderController::class,'order_details']);
    Route::post('update_order_status',[\App\Http\Controllers\API\OrderController::class,'update_order_status']);

    Route::post('update_membership',[\App\Http\Controllers\API\UserController::class,'update_membership']);
    Route::post('update_token',[\App\Http\Controllers\API\UserController::class,'update_token']);

    Route::post('notifications',[\App\Http\Controllers\API\UserController::class,'notifications']);

    Route::post('give_suggestion',[\App\Http\Controllers\API\UserController::class,'give_suggestion']);
});

Route::post('register_user',[\App\Http\Controllers\API\UserController::class,'register_user']);

//for test
//Route::get("send_sms",[\App\Http\Controllers\API\AuthController::class,'send_sms']);

