<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Discount;

class DiscountController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'startTime' => 'required|date',
            'endTime' => 'required|date|after:start_time',
            'name' => 'required|string',
            'code' => 'required|string|unique:discounts',
            'discountPercent' => 'required|Integer',
            'productId' => 'required|integer|exists:products,id',
        ]);

        if($validator->fails()){
            return json_encode(Response::error(Response::CVTM($validator)));
        }

        $discount = Discount::create($validator->validated());

        // return response()->json([
        //     'message' => '',
        //     'discount' => $
        // ], 201);
        return json_encode(Response::success($discount,"Discount successfully created"));

    }

    public function getDiscount($discountId)
    {
        $discount = Discount::find($discountId);

        if(!$discount) {
            return json_encode(Response::error('Discount not found'));

        return json_encode(Response::success($discount,"Thong tin giam gia"));
        }
        return json_encode(Response::success($discount,"Thong tin giam gia"));

    }

    public function getAllDiscounts()
    {
        $discounts = Discount::all();

        return json_encode(Response::success($discounts,"Thong tin giam gia"));

    }

    public function update(Request $request, $discountId)
    {
        $discount = Discount::find($discountId);

        if(!$discount) {
            // return response()->json([
            //     'message' => ,
            // ], 404);
            return json_encode(Response::error('Discount not found'));

        }

        $validator = Validator::make($request->all(), [
            'startTime' => 'sometimes|time',
            'endTime' => 'sometimes|time|after:start_time',
            'name' => 'sometimes|string',
            'code' => 'sometimes|string|unique:discounts',
            'productId' => 'sometimes|integer|exists:products,id',
        ]);

        if($validator->fails()){
            return json_encode(Response::error(Response::CVTM($validator)));
        }

        $discount->update($validator->validated());

        // return response()->json([
        //     'message' => ,
        //     'discount' => $discount
        // ]);
        return json_encode(Response::success($discount,"Discount successfully updated"));

    }

    public function delete($discountId)
    {
        $discount = Discount::find($discountId);

        if(!$discount) {
            return json_encode(Response::error('Discount not found'));

        }

        $discount->delete();

        // return response()->json([
        //     'message' => ,
        // ], 200);
        return json_encode(Response::success($discount,'Discount successfully deleted'));

    }
    public function getAllDiscountsOfProduct($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return json_encode(Response::error('Discount not found'));

        }

        $discounts = \DB::table('discount')
            ->where('productId', $productId)
            ->get();

        // return response()->json($discounts);
        return json_encode(Response::success($discounts,"successfully"));

    }
    public function getDiscountByCode($code)
    {
        $product = Product::find($productId);

        if (!$product) {
            return json_encode(Response::error('Discount not found'));
        }
        $discounts = $product->discounts;

        // return response()->json($discounts);
        return json_encode(Response::success($discounts,"successfully"));

    }
}
