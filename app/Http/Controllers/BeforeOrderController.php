<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BeforeOrder;
use Illuminate\Support\Facades\Auth;

class BeforeOrderController extends Controller
{
    /**
     * Lưu beforeOrder mới tạo vào storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createBeforeOrder(Request $request)
    {
         // Phân tích products từ string sang mảng hoặc object tương ứng
        $tableNumber = $request->tableNumber;
        $isTakeAway = $request->isTakeAway;
        $note = $request->note;
        $discountCode = $request->discountCode;
        $products = $request->input('products', []); // các sản phẩm là một mảng chứa thông tin sản phẩm

        try {
            // Tạo một transaction để đảm bảo dữ liệu được thêm đúng
            DB::beginTransaction();

            // Tạo đơn hàng tạm
            $beforeOrder = BeforeOrder::create([
                'userId' => Auth::id(),
                'status' => 'pending',
                'tableNumber' => $tableNumber,
                'isTakeAway' => $isTakeAway,
                'note' => $note,
                'discountCode' => $discountCode
            ]);

            // Xử lấy sản phẩm để tạo chi tiết đơn đặt hàng
            foreach ($products as $product) {
                BeforeOrderDetail::create([
                    'size' => $product['size'],
                    'beforeOderId' => $beforeOrder->deforeOderId,
                    'productId' => $product['id'],
                    'price' => $product['price'],
                    'number' => $product['number'],
                ]);
            }

            // Đảm bảo cả hai yêu cầu trên đều thành công, nếu không, đảo ngược lại
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'success',
                'beforeOrder' => $beforeOrder
            ], 201);
        } catch (\Exception $e) {
            // rollback the transaction
            DB::rollBack();

            // handling the exception
            return response()->json([
                'success' => false,
                'message' => "Error: " . $e->getMessage(),
            ], 500);
        }
    }
    public function getAllBeforeOrders(Request $request) {
    // default values
    $page = $request->input('page', 0);
    $status = $request->input('status', 'open/close');

    $beforeOrders = BeforeOrder::where('status', $status)
      ->orderBy('created_at', 'desc')
      ->paginate(10, ['*'], 'page', $page);

    return response()->json(['success' => true, 'message' => 'success', 'beforeOrders' => $beforeOrders]);
  }

  public function getBeforeOrderDetails($id) {
    $beforeOrder = BeforeOrder::where('deforeOderId', $id)->first();

    if($beforeOrder){
      return response()->json(['success' => true, 'message' => 'success', 'beforeOrder' => $beforeOrder]);
    }else{
      return response()->json(['success' => false, 'message' => 'No order found']);
    }
  }

  public function getBeforeOrdersOfUser() {
    $userId = Auth::id();
    $beforeOrders = BeforeOrder::where('userId', $userId)->get();

    return response()->json(['success' => true, 'message' => 'success', 'beforeOrders' => $beforeOrders]);
  }

  public function updateBeforeOrderStatus($id, Request $request) {
    $status = $request->input('status', 'prepare/close');

    $beforeOrder = BeforeOrder::where('deforeOderId', $id)->first();
    if($beforeOrder){
      $beforeOrder->status = $status;
      $beforeOrder->save();

      return response()->json(['success' => true, 'message' => 'Order status updated']);
    }else{
      return response()->json(['success' => false, 'message' => 'No order found']);
    }
  }

  public function convertOrder($id) {
    // This function needs to be customized based on how you intend to implement the conversion from 'beforeOrder' to 'order'
    // Assuming you have an 'Order' model, your implementation may look something like this:

    $beforeOrder = BeforeOrder::where('deforeOderId', $id)->first();
    if($beforeOrder){
      $order = new Order;
      $order->time = $beforeOrder->time;
      $order->status = 'processing';  // or any appropriate status
      $order->userId = $beforeOrder->userId;
      // copy remaining necessary fields...

      $order->save();

      return response()->json(['success' => true, 'message' => 'success', 'order' => $order]);
    }else{
      return response()->json(['success' => false, 'message' => 'No order found to convert']);
    }
  }
}
