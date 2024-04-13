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
            return response()->json($validator->errors()->toJson(), 400);
        }

        $discount = Discount::create($validator->validated());

        return response()->json([
            'message' => 'Discount successfully created',
            'discount' => $discount
        ], 201);
    }

    public function getDiscount($discountId)
    {
        $discount = Discount::find($discountId);

        if(!$discount) {
            return response()->json([
                'message' => 'Discount not found',
            ], 404);
        }

        return response()->json($discount);
    }

    public function getAllDiscounts()
    {
        $discounts = Discount::all();

        return response()->json($discounts);
    }

    public function update(Request $request, $discountId)
    {
        $discount = Discount::find($discountId);

        if(!$discount) {
            return response()->json([
                'message' => 'Discount not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'startTime' => 'sometimes|date',
            'endTime' => 'sometimes|date|after:start_time',
            'name' => 'sometimes|string',
            'code' => 'sometimes|string|unique:discounts',
            'productId' => 'sometimes|integer|exists:products,id',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $discount->update($validator->validated());

        return response()->json([
            'message' => 'Discount successfully updated',
            'discount' => $discount
        ]);
    }

    public function delete($discountId)
    {
        $discount = Discount::find($discountId);

        if(!$discount) {
            return response()->json([
                'message' => 'Discount not found',
            ], 404);
        }

        $discount->delete();

        return response()->json([
            'message' => 'Discount successfully deleted',
        ], 200);
    }
    public function getAllDiscountsOfProduct($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        $discounts = \DB::table('discount')
            ->where('productId', $productId)
            ->get();

        return response()->json($discounts);
    }
    public function getDiscountByCode($code)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        $discounts = $product->discounts;

        return response()->json($discounts);
    }
}
