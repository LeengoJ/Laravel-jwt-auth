<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\before_orderController;



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
    'middleware' => ['api','checkrole:admin'],
    'prefix' => 'users'
], function ($router) {
    Route::get('', [AuthController::class, 'getAllUsers']);
    // Route::post('/getStaff', [AuthController::class, 'getStaff']);
    Route::post('/changeRole/{id}', [AuthController::class, 'updateRole']);
    Route::post('/changeBan/{id}', [AuthController::class, 'changeBan']);

});
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
    Route::post('/checkLogin', [AuthController::class, 'checkLogin']);

});
Route::group([
    'middleware' => ['api','checkrole:admin'],
    'prefix' => 'products'
],function ($router) {
    // Route::post('/multiple-image-upload', [ProductController::class, 'store']);
    Route::post('', [ProductController::class, 'create']);
    Route::post('/{id}', [ProductController::class, 'update']);
    Route::delete('{id}', [ProductController::class, 'delete']);
    Route::get('/{id}', [ProductController::class, 'getProduct']);
});
Route::group([
    'prefix' => 'products'
],function ($router) {
    Route::get('', [ProductController::class, 'getAllProducts']);
    // Route::get('/productByName', [ProductController::class, 'searchByName']);
});

Route::group([
    'middleware' => ['api'],
    'prefix' => 'tables'
],function ($router) {
    Route::post('/', [TableController::class, 'create'])->middleware("checkrole:admin");
    Route::get('/{id}', [TableController::class, 'getTable'])->middleware("checkrole:admin");
    Route::get('/', [TableController::class, 'getAllTables']);
    Route::post('/updateTable/{id}', [TableController::class, 'update'])->middleware("checkrole:admin");
    Route::delete('{id}', [TableController::class, 'delete'])->middleware("checkrole:admin");
    Route::get('/searchByName', [TableController::class, 'searchByName']);
    Route::post('/updateStatusTables/{tableId}', [TableController::class, 'updateStatusTable'])->middleware("checkrole:staff");
});
// Route::group([
//     'middleware' => ['api','checkrole:staff'],
//     'prefix' => 'tables'
// ],function ($router) {
// });
Route::group([
    'middleware' => ['api','checkrole:admin'],
    'prefix' => 'discounts'
],function ($router) {
    Route::post('', [DiscountController::class, 'create']);
    Route::get('/{id}', [DiscountController::class, 'getDiscount']);
    Route::get('/getAllDiscountsOfProduct/{productId}', [DiscountController::class, 'getAllDiscountsOfProduct']);
    Route::get('', [DiscountController::class, 'getAllDiscounts']);
    Route::post('/updateDiscount/{discountId}', [DiscountController::class, 'update']);
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
    Route::post('/updateOrder/{orderId}', [OrderController::class, 'updateOrderByStaff']);
    Route::post('/updateOrderStatus/{orderId}', [OrderController::class, 'updateOrderStatus']);
    Route::delete('/{id}', [OrderController::class, 'closeOrder']);
    Route::get('/findOrdersBySdt', [OrderController::class, 'findOrdersBySdt']);
    Route::post('/addProductIntoOrder/{orderId}', [OrderController::class, 'addProductIntoOrder']);
    Route::get('/showOrderDetailByID/{id}', [OrderController::class, 'showOrderDetailByID']);
    Route::get('/getOrderDetails/{id}', [OrderController::class, 'getOrderDetails']);
});
Route::group([
    'middleware' => ['api','checkrole:user'],
    'prefix' => 'before_orders'
],function ($router) {
    Route::post('', [before_orderController::class, 'createbefore_order']);
    Route::get('/{id}', [before_orderController::class, 'getbefore_orderDetails']);
    Route::get('', [before_orderController::class, 'getAllBeforeOrders']);
    Route::post('/updateOrder/{id}', [before_orderController::class, 'updatebefore_order']);
    Route::post('/updateBeforeOrderStatus/{id}', [before_orderController::class, 'updatebefore_orderStatus']);
    Route::post('/convertOrder/{id}', [before_orderController::class, 'convertOrder']);
    Route::delete('/{id}', [before_orderController::class, 'closebefore_order']);
    Route::get('/getBeforeOrdersOfUser', [before_orderController::class, 'getbefore_ordersOfUser']);
});
