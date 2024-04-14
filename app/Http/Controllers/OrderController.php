<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Order_Details;
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
        $sizes = json_decode($product->sizes, true);
        $size_price_pairs = explode(';', $sizes);

        foreach($size_price_pairs as $pair) {
            list($current_size, $price) = explode(':', $pair);
            if($current_size == $size) {
                return $price;
            }
        }
        return null;
    }

    public function createOrderByStaff(Request $request,$UserId)
    {
        $new_order= $request->all();
        $string = $new_order['product'];
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
            $singlePrice = getPriceBySizeAndId($id, $size); // this function gets product price by id and size
            // Get discounts for the current product
            $discounts = \DB::table('discount')
                ->where('productId', $id)
                ->where(function ($query) use ($discountCode) {
                    $query->where('code', $discountCode)
                        ->where('startTime', '<=', now())
                        ->where('endTime', '>=', now());
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

        foreach($processed_products as $product) {
            $discountPayment += ($product['money'] - $product['price_after_discount']);
        }
        $order = Order::create([
            'userId' => $UserId,
            'time' => $new_order['time'],
            'sdt' => $new_order['sdt'],
            'note' => $new_order['note'],
            'numberProduct' => count($product),
            'totalBill' => $total_price,
            'discountPayment' => $discountPayment,
            'numberTable' => $new_order['numberTable'],
            'discountCode' => $new_order['discountCode']
        ]);

        $orderId = $Order->id;
        foreach ($items as $item) {
            // Phân tách thành phần con theo dấu ':'
            $parts = explode(':', $item);

            $id = $parts[0];
            $size = $parts[1];
            $numberOfSize = $parts[2];

            $sizeReel = explode(":", $string)[0];
            $price = explode(":", $string)[1]*$numberOfSize;

            $order_detail = Order_Details::create([
                'size' => $sizeReel,
                'orderId' => $orderId,
                'productId' => $id,
                'price' => $price,
                'number' => $numberOfSize,
            ]);
        }

                return json_encode(Response::success($orders,"successfully"));

    }

    public function GetOderById($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return json_encode(Response::error('Order not found'));
        }
        return json_encode(Response::success($orders,"successfully"));

    }

    public function updateOrder(Request $request, $orderId)
    {
        // Tìm order bằng id
        $order = Order::find($orderId);
        if(!$order) {
        return response()->json(['message' => 'Order not found'], 404);
        }

        $updated_order= $request->all();
        // Đảm bảo dữ liệu gửi lên hợp lệ.

        $string = $updated_order['products'];
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
                        ->where('startTime', '<=', now())
                        ->where('endTime', '>=', now());
                })
                ->first();

            $discountAmount = 0;
            $discountPayment += $singlePrice * $numberOfSize;
            if (!empty($discounts)) {
                $discountAmount = $singlePrice * $numberOfSize * $discounts->discountPercent / 100;

                $totalPrice += $singlePrice * $numberOfSize - $discountAmount;
            } else {
                $totalPrice += $singlePrice * $numberOfSize;
            }
        }

        $discountPayment = 0;

        foreach($processed_products as $product) {
            $discountPayment += ($product['money'] - $product['price_after_discount']);
        }
        // Tiến hành cập nhật order.
        $order->sdt = $updated_order['sdt'];
        $order->note = $updated_order['note'];
        $order->numberProduct = count($products); // Changed from $product to $products
        $order->totalBill = $totalPrice;
        $order->discountPayment = $discountPayment;
        $order->numberTable = $updated_order['numberTable'];
        $order->discountCode = $updated_order['discountCode'];

        // Lưu thay đổi
        $order->save();

        // Update order_details
        Order_Details::where('orderId', $orderId)->delete(); // assuming you want to delete old order details
        foreach ($items as $item) {
            $parts = explode(':', $item);

            $id = $parts[0];
            $size = $parts[1];
            $numberOfSize = $parts[2];

            $order_detail = Order_Details::create([
                'size' => $size,
                'orderId' => $orderId,
                'productId' => $id,
                'price' => $singlePrice *$numberOfSize,
                'number' => $numberOfSize,
            ]);
        }

        // Trả về phản hồi thành công
                return json_encode(Response::success($orders,"successfully"));

    }

    public function closeOrder($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            // return response()->json([
            //     'message' => 'Order not found'
            // ], 404);
            return json_encode(Response::error('Order not found'));

        }
        $order->delete();

        // return response()->json([
        //     'message' => ''
        // ], 200);
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
        $orderDetails = Order_Details::where('orderId', $orderId)->get();

        // Nếu không tìm thấy orderDetails nào, trả về thông báo
        if (!$orderDetails) {
            return response()->json(['message' => 'No order details found for this order id'], 404);
        }

        // Nếu tìm thấy orderDetails, trả về dữ liệu
                return json_encode(Response::success($orderDetails,"successfully"));

    }
}
