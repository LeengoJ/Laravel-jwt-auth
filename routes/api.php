<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BeforeOrderController;



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
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::get('/searchUsers', [AuthController::class, 'searchUsers']);

});
Route::group([
    'middleware' => ['api','checkrole:admin'],
    'prefix' => 'products'
],function ($router) {
    Route::post('/multiple-image-upload', [ProductController::class, 'store']);
    Route::post('', [ProductController::class, 'create']);
    Route::post('/{id}', [ProductController::class, 'update']);
    Route::delete('{id}', [ProductController::class, 'delete']);
});
Route::group([
    'prefix' => 'products'
],function ($router) {
    Route::get('/{id}', [ProductController::class, 'getProduct']);
    Route::get('', [ProductController::class, 'getAllProducts']);
    Route::get('/productByName', [ProductController::class, 'searchByName']);
});

Route::group([
    'middleware' => ['api','checkrole:admin'],
    'prefix' => 'tables'
],function ($router) {
    Route::post('', [TableController::class, 'create']);
    Route::get('/{id}', [TableController::class, 'getTable']);
    Route::get('', [TableController::class, 'getAllTables']);
    Route::post('/updateTable/{id}', [TableController::class, 'update']);
    Route::delete('{id}', [TableController::class, 'delete']);
    Route::get('/searchByName', [TableController::class, 'searchByName']);
});
Route::group([
    'middleware' => ['api','checkrole:staff'],
    'prefix' => 'tables'
],function ($router) {
    Route::get('', [TableController::class, 'getAllTables']);
    Route::post('/updateStatusTables/{tableId}', [TableController::class, 'updateStatusTable']);
});
Route::group([
    'middleware' => ['api','checkrole:admin'],
    'prefix' => 'discounts'
],function ($router) {
    Route::post('', [DiscountController::class, 'create']);
    Route::get('/{id}', [DiscountController::class, 'getDiscount']);
    Route::get('/getAllDiscountsOfProduct/{productId}', [DiscountController::class, 'getAllDiscountsOfProduct']);
    Route::get('', [DiscountController::class, 'getAllDiscounts']);
    Route::post('/updateTable/{id}', [DiscountController::class, 'update']);
    Route::delete('/{id}', [DiscountController::class, 'delete']);
    Route::get('/getDiscountByCode', [DiscountController::class, 'getDiscountByCode']);
});
Route::group([
    'middleware' => ['api','checkrole:staff'],
    'prefix' => 'orders'
],function ($router) {
    Route::post('', [OrderController::class, 'createOrderByStaff']);
    Route::get('/{id}', [OrderController::class, 'GetOderById']);
    Route::get('', [OrderController::class, 'getAllOrders']);
    Route::post('/updateOrder/{id}', [OrderController::class, ' ']);
    Route::delete('/{id}', [OrderController::class, 'closeOrder']);
    Route::get('/findOrdersBySdt', [OrderController::class, 'findOrdersBySdt']);
    Route::post('/addProductIntoOrder/{orderId}', [OrderController::class, 'addProductIntoOrder']);
    Route::get('/showOrderDetailByID/{id}', [OrderController::class, 'showOrderDetailByID']);
    Route::get('/getOrderDetails/{id}', [OrderController::class, 'getOrderDetails']);
});
Route::group([
    'middleware' => ['api','checkrole:admin'],
    'prefix' => 'before_orders'
],function ($router) {
    Route::post('', [DiscountController::class, 'createBeforeOrder']);
    Route::get('/{id}', [DiscountController::class, 'GetOderById']);
    Route::get('/getBeforeOrderDetails/{productId}', [DiscountController::class, 'getAllDiscountsOfProduct']);
    Route::get('', [DiscountController::class, 'getAllBeforeOrders']);
    Route::get('/getBeforeOrderDetails', [DiscountController::class, 'getBeforeOrderDetails']);
    Route::get('/getBeforeOrdersOfUser/{id}', [DiscountController::class, 'getBeforeOrdersOfUser']);
    Route::post('/updateOrder/{id}', [DiscountController::class, 'getBeforeOrdersOfUser']);
    Route::post('/updateBeforeOrderStatus/{id}', [DiscountController::class, 'updateBeforeOrderStatus']);
    Route::post('/convertOrder/{id}', [DiscountController::class, 'convertOrder']);
    Route::delete('/{id}', [DiscountController::class, 'closeBeforeOrder']);
    Route::get('/findOrdersBySdt', [DiscountController::class, 'findOrdersBySdt']);
});
