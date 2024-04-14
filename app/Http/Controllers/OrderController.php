<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getAllOrders()
    {
        $orders = Order::all();

        // return response()->json($);
        return json_encode(Response::success($orders,"successfully"));

    }
    public function getPriceBySizeAndId($productId, $size){

        $product = Product::find($productId);

        $sizes = $product->sizes;
        $size_price_pairs = explode(';', $sizes);

        foreach($size_price_pairs as $pair) {

            list($current_size, $price) = explode(':', $pair);
            if($current_size == $size) {
                return $price;
            }
        }
        return null;
    }

    // public function createOrderByStaff(Request $request,$UserId)
    public function createOrderByStaff(Request $request)
    {
        $new_order= $request->all();
        $discountCode =$new_order['discountCode'];
        $string = $new_order['products'];
        //products: ["{id}:S.{number};M.{number}"]
        //$string = "id1:size1:numberOfSize1;id1:size2:numberOfSize2;id2:size3:numberOfSize3";

        // Phân tách các phần tử theo dấu ';'
        $items = explode(';', $string);

        $products;
        $totalPrice = 0;
        $discountPayment =0;
        foreach ($items as $item) {
            // Phân tách thành phần con theo dấu ':'
            $parts = explode(':', $item);

            $id = $parts[0];
            $size = $parts[1];
            $numberOfSize = $parts[2];


            $products[$id][$size] = $numberOfSize;
            $singlePrice = $this->getPriceBySizeAndId($id, $size); // this function gets product price by id and size
            // Get discounts for the current product
            $discounts = \DB::table('discount')
                ->where('productId', $id)
                ->where(function ($query) use ($discountCode) {
                    $query->where('code', $discountCode)
                        ->where('startTime', '<=', time())
                        ->where('endTime', '>=', time());
                })
                ->first();

            $discountAmount = 0;
            // $discountPayment += $singlePrice * $numberOfSize;
            if (!empty($discounts)) {
                $discountAmount = $singlePrice * $numberOfSize * $discounts->discountPercent / 100;

                $totalPrice += $singlePrice * $numberOfSize - $discountAmount;
            } else {
                $totalPrice += $singlePrice * $numberOfSize;
            }
        }

        $discountPayment = 0;
        // foreach($processed_products as $products) {
        //     $discountPayment += ($product['money'] - $product['price_after_discount']);
        // }
        $order = Order::create([
            'userId' => \Auth::id(),
            'time' =>  now(),
            'sdt' => $new_order['sdt'],
            'note' => $new_order['note'],
            'numberProduct' => count($products),
            'totalBill' => $totalPrice,
            'discountPayment' => $discountPayment,
            'status'=>'spending',
            'numberTable' => $new_order['numberTable'],
            'discountCode' => $new_order['discountCode']
        ]);

        $orderId = $order->orderId;
        foreach ($items as $item) {
            // Phân tách thành phần con theo dấu ':'
            $parts = explode(':', $item);

            $id = $parts[0];
            $size = $parts[1];
            $numberOfSize = $parts[2];

            $sizeReel = explode(":", $string)[0];
            $price = $this->getPriceBySizeAndId($id,$size) * intval($numberOfSize);

            $order_detail = OrderDetails::create([
                'size' => $sizeReel,
                'orderId' => $orderId,
                'productId' => $id,
                'price' => $price,
                'number' => $numberOfSize,
            ]);
        }

        return json_encode(Response::success($order,"successfully"));

    }

    public function GetOderById($orderId)
    {
        $order = Order::find($orderId);


        if (!$order) {
            return json_encode(Response::error('Order not found'));
        }
        return json_encode(Response::success($order,"successfully"));

    }

    public function updateOrderByStaff(Request $request, $orderId){
        $order = Order::find($orderId);
        if($order){
            $new_order= $request->all();
            $discountCode =$new_order['discountCode'];
            $string = $new_order['products'];

            $items = explode(';', $string);

            $products;
            $totalPrice = 0;
            $discountPayment =0;

            foreach ($items as $item) {

                $parts = explode(':', $item);

                $id = $parts[0];
                $size = $parts[1];
                $numberOfSize = $parts[2];


                $products[$id][$size] = $numberOfSize;
                $singlePrice = $this->getPriceBySizeAndId($id, $size);

                $discounts = \DB::table('discount')
                    ->where('productId', $id)
                    ->where(function ($query) use ($discountCode) {
                        $query->where('code', $discountCode)
                            ->where('startTime', '<=', time())
                            ->where('endTime', '>=', time());
                    })
                    ->first();

                $discountAmount = 0;

                if (!empty($discounts)) {
                    $discountAmount = $singlePrice * $numberOfSize * $discounts->discountPercent / 100;

                    $totalPrice += $singlePrice * $numberOfSize - $discountAmount;
                } else {
                    $totalPrice += $singlePrice * $numberOfSize;
                }
            }

            $discountPayment = 0;


            $order->update([
                'userId' => \Auth::id(),
                'time' =>  now(),
                'sdt' => $new_order['sdt'],
                'note' => $new_order['note'],
                'numberProduct' => count($products),
                'totalBill' => $totalPrice,
                'discountPayment' => $discountPayment,
                'status'=>'spending',
                'numberTable' => $new_order['numberTable'],
                'discountCode' => $new_order['discountCode'],
            ]);

            OrderDetails::where('orderId', $orderId)->delete();

            foreach ($items as $item) {
                $parts = explode(':', $item);

                $id = $parts[0];
                $size = $parts[1];
                $numberOfSize = $parts[2];

                
                
                // if(OrderDetails::where('orderId', $orderId)->where('productId', $id)->exists()){
                //     $order_detail = OrderDetails::where('orderId', $orderId)->where('productId', $id)->where('size',$size )->first();
                //     $price = $this->getPriceBySizeAndId($id,$size) * intval($numberOfSize);

                //     $order_detail->update([
                //         'size' => $size,
                //         'price' => $price,
                //         'number' => $numberOfSize,
                //     ]);

                // } else {
                    $price = $this->getPriceBySizeAndId($id,$size) * intval($numberOfSize);

                    OrderDetails::create([
                        'size'=> $size,
                        'orderId'=> $orderId,
                        'productId'=> $id,
                        'price'=> $price,
                        'number'=> $numberOfSize,
                    ]);
                // }
            }

            return json_encode(Response::success($order, "Order updated successfully"));

        } else{
            return json_encode(Response::error("Order with id " . $orderId . " not found"));
        }
    }
    public function closeOrder($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return json_encode(Response::error('Order not found'));
        }
        $order->delete();
            return json_encode(Response::success([],"Order successfully deleted"));
    }
    public function findOrdersBySdt($sdt)
    {
        $orders = Order::where('sdt', $sdt)->get();

        if (count($orders) === 0) {
            return response()->json(['message' => 'No orders found with this phone number'], 404);
        }

             return json_encode(Response::success($orders,"successfully"));

    }

    public function addProductIntoOrder(Request $request,$orderId)
    {
        $newOrderDetail = $request->all();
        $newOrderDetail["orderId"] = $orderId;
        $orderDetail = OrderDetail::create($newOrderDetail);
        return json_encode(Response::success([],"Order successfully deleted"));
    }

    public function showOrderDetailByID($id)
    {
        $orderDetail = OrderDetail::find($id);

        if(!$orderDetail) {
            return json_encode(Response::error('Order not found'));
        }
            return json_encode(Response::success($orderDetail,"successfully"));

    }

    public function getOrderDetails($orderId)
    {
        // Tìm orderDetails theo orderId
        $orderDetails = OrderDetails::where('orderId', $orderId)
        ->join('products', 'order_details.productId', '=', 'products.productId')
        ->get();

        // Nếu không tìm thấy orderDetails nào, trả về thông báo
        if (!$orderDetails) {
            return response()->json(['message' => 'No order details found for this order id'], 404);
        }

        // Nếu tìm thấy orderDetails, trả về dữ liệu
        return json_encode(Response::success($orderDetails,"successfully"));

    }
    public function updateOrderStatus($orderId, Request $request) {
        // Tìm order bằng orderId
        $order = Order::find($orderId);

        // Nếu không tìm thấy order, trả về lỗi
        if(!$order) {
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Order not found',
            // ], 404);
            return json_encode(Response::error('Order not found'));

        }

        // Nhận trạng thái mới từ request
        $status = $request->get('status');

        // Cập nhật trạng thái
        $order->status = $status;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated',
            'order' => $order
        ], 200);
    }
}
