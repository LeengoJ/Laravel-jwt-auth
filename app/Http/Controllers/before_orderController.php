<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\before_order;
use App\Models\order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\before_order_details;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class before_orderController extends Controller
{
    /**
     * Lưu before_order mới tạo vào storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
    public function createbefore_order(Request $request)
    {
        $new_order = $request->all();
        $discountCode = $new_order['discountCode'];
        $string = $new_order['products'];

        $items = explode(';', $string);

        $totalPrice = 0;
        $discountPayment = 0;
        foreach ($items as $item) {
            $parts = explode(':', $item);

            $id = $parts[0];
            $size = $parts[1];
            $numberOfSize = $parts[2];

            $singlePrice = $this->getPriceBySizeAndId($id, $size);

            $discounts = \DB::table('discounts')
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

        $before_order = before_order::create([
            'userId' => \Auth::id(),
            'time' =>  time(),
            'status' => "waiting",     // assuming 0 is the initial status
            'tableNumber' => $new_order['tableNumber'],
            'isTakeAway' => $new_order['isTakeAway'],
            'note' => $new_order['note'],
            'discountCode' => $new_order['discountCode'],
            // 'totalBill' => $totalPrice,
            // 'discountPayment' => $discountPayment,
        ]);
        $before_order_id = $before_order->beforeOrderId;
        foreach ($items as $item) {
            $parts = explode(':', $item);

            $id = $parts[0];
            $size = $parts[1];
            $numberOfSize = $parts[2];

            $price = $this->getPriceBySizeAndId($id,$size);

            $order_detail = before_order_details::create([
                'size' => $size,
                'beforeOrderId' => $before_order_id,
                'productId' => $id,
                'price' => $price,
                'number' => $numberOfSize,
            ]);
        }
        return json_encode(Response::success($before_order, "Before Order created successfully"));
    }

    public function getAllBeforeOrdersWithPage(Request $request) {
        // default values
        $page = $request->input('page', 0);
        $status = $request->input('status', 'open/close');

        $before_orders = before_order::where('status', $status)
        ->orderBy('created_at', 'desc')
        ->paginate(10, ['*'], 'page', $page);

        // return response()->json(['success' => true, 'message' => 'success', 'before_orders' => $before_orders]);
        return json_encode(Response::success($before_orders,"Thanh cong"));

    }
    public function getAllBeforeOrders(Request $request) {
        $before_orders = before_order::join('users', 'before_orders.userId', '=', 'users.id')->get();
        // return response()->json(['success' => true, 'message' => 'success', 'before_orders' => $before_orders]);
        return json_encode(Response::success($before_orders,"Thanh cong"));
    }

    public function getBeforeOrderDetailsItems($beforeOrderId)
    {
        // Tìm orderDetails theo orderId
        $orderDetails = before_order_details::where('beforeOrderId', $beforeOrderId)
        ->join('products', 'before_order_details.productId', '=', 'products.productId')
        ->get();

        // Nếu không tìm thấy orderDetails nào, trả về thông báo
        if (!$orderDetails) {
            return response()->json(['message' => 'No order details found for this order id'], 404);
        }

        // Nếu tìm thấy orderDetails, trả về dữ liệu
        return json_encode(Response::success($orderDetails,"successfully"));

    }

    public function getbefore_orderDetails($beforeOrderId) {
        // $before_order = before_order::find($deforeOderId);
        $before_order = before_order::where('beforeOrderId', $beforeOrderId)->first();

        if($before_order){
            // return response()->json(['success' => true, 'message' => 'success', 'before_order' => $before_order]);
            return json_encode(Response::success($before_order,"Thanh cong"));

        }else{
            // return response()->json(['success' => false, 'message' => 'No order found']);
            return json_encode(Response::error("No order found"));
        }
    }

    public function updatebefore_order(Request $request, $id)
    {
        $updated_order = $request->all();
        $discountCode = $updated_order['discountCode'];
        $string = $updated_order['products'];

        $items = explode(';', $string);

        $totalPrice = 0;
        foreach ($items as $item) {
            $parts = explode(':', $item);

            $productId = $parts[0];
            $size = $parts[1];
            $numberOfSize = $parts[2];

            $singlePrice = $this->getPriceBySizeAndId($productId, $size);

            $discounts = \DB::table('discount')
                    ->where('productId', $productId)
                    ->where(function ($query) use ($discountCode) {
                        $query->where('code', $discountCode)
                            ->where('startTime', '<=', time())
                            ->where('endTime', '>=', time());
                    })
                    ->first();

            if (!empty($discounts)) {
                $discountAmount = $singlePrice * $numberOfSize * $discounts->discountPercent / 100;
                $totalPrice += $singlePrice * $numberOfSize - $discountAmount;
            } else {
                $totalPrice += $singlePrice * $numberOfSize;
            }
        }

        $before_order = before_order::find($id);

        $before_order->update([
            'userId' => \Auth::id(),
            'time' =>  now(),
            'status' => $updated_order['status'],
            'tableNumber' => $updated_order['tableNumber'],
            'isTakeAway' => $updated_order['isTakeAway'],
            'note' => $updated_order['note'],
            'discountCode' => $updated_order['discountCode'],
            'totalBill' => $totalPrice,
        ]);

        before_order_details::where('beforeOderId', $id)->delete();

        foreach ($items as $item) {
            $parts = explode(':', $item);

            $productId = $parts[0];
            $size = $parts[1];
            $numberOfSize = $parts[2];

            $price = $this->getPriceBySizeAndId($productId,$size) * intval($numberOfSize);

            $order_detail = before_order_details::create([
                'size' => $size,
                'beforeOderId' => $before_order->id,
                'productId' => $productId,
                'price' => $price,
                'number' => $numberOfSize,
            ]);
        }

        return json_encode(Response::success($before_order, "Before Order updated successfully"));
    }
    public function getbefore_ordersOfUser() {
        $userId = Auth::id();
        $before_orders = before_order::where('userId', $userId)->get();

        // return response()->json(['success' => true, 'message' => 'success', 'before_orders' => $before_orders]);
        return json_encode(Response::success($before_orders,"Thanh cong"));

    }

    public function updatebefore_orderStatus(Request $request,$beforeOrderId) {
        $status = $request->input('status', 'waiting/handled');

        $before_order = before_order::where('beforeOrderId', $beforeOrderId)->first();
        if($before_order){
            $before_order->status = $status;
            $before_order->save();

            return json_encode(Response::success($before_order, "Order status updated"));
        }else{
            return json_encode(Response::error("No order found"));
        }
    }

    public function convertOrder($id) {
        // This function needs to be customized based on how you intend to implement the conversion from 'before_order' to 'order'
        // Assuming you have an 'Order' model, your implementation may look something like this:

        $before_order = before_order::where('beforeOrderId', $id)
        ->join('users', 'before_orders.userId', '=', 'users.id')
        ->first();

        if(!$before_order){
            return json_encode(Response::error("No order found to convert"));
        }

        $before_order_details = before_order_details::where('beforeOrderId', $id)->get();

        $totalPrice = 0;
        $discountPayment = 0;
        $numberProduct = 0;
        $discountCode = $before_order->discountCode;
        foreach ($before_order_details as $item) {
            $productId = $item->productId;
            // $size = $item->size;
            $numberOfSize = $item->number;
            $numberProduct+=$numberOfSize;
            $price = $item->price;

            $discounts = \DB::table('discounts')
                ->where('productId', $productId)
                ->where(function ($query) use ($discountCode,$before_order) {
                    $query->where('code', $discountCode)
                        ->where('startTime', '<=', $before_order->time)
                        ->where('endTime', '>=', $before_order->time);
                })
                ->first();

            $discountAmount = 0;
            if (!empty($discounts)) {
                $discountAmount = $price * $numberOfSize * $discounts->discountPercent / 100;
                $discountPayment+=$discountAmount;
                $totalPrice += $price * $numberOfSize - $discountAmount;
            } else {
                $totalPrice += $price * $numberOfSize;
            }
        }

        $order = Order::create([
            'userId' => \Auth::id(),
            'time' => $before_order->time ,
            'sdt' => $before_order->sdt,
            'note' => $before_order->note,
            'numberProduct' => $numberProduct,
            'totalBill' => $totalPrice,
            'discountPayment' => $discountPayment,
            'status'=>'waiting',
            'numberTable' => $before_order->numberTable==null?0:$before_order->numberTable,
            'isTakeAway' => $before_order->isTakeAway,
            'discountCode' => $before_order->$discountCode
        ]);

        $orderId = $order->orderId;
        foreach ($before_order_details as $item) {
            // Phân tách thành phần con theo dấu ':'
            $productId = $item->productId;
            $size = $item->size;
            $numberOfSize = $item->number;
            $price = $item->price;

            $order_detail = OrderDetails::create([
                'size' => $size,
                'orderId' => $orderId,
                'productId' => $productId,
                'price' => $price,
                'number' => $numberOfSize,
            ]);
        }

        return json_encode(Response::success($order,"success"));
    }
    public function closebefore_order($id)
    {
        $before_order = before_order::where('beforeOderId', $id)->first();

        if(!$before_order) {
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Order not found',
            // ], 404);
            return json_encode(Response::error("Order not found"));

        }
        if($before_order->status == 'close') {
            return json_encode(Response::error("Order is already closed"));

        }

        $before_order->status = 'closed';
        $before_order->save();

        // return response()->json([
        //     'success' => true,
        //     'message' => '',
        // ], 200);
        return json_encode(Response::success($before_order,"Order status successfully updated to closed"));

    }
}
