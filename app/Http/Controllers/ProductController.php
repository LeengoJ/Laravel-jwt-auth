<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ProductController extends Controller
{
    //
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'img' => 'sometimes|string',
            'sizes' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $product = Product::create($validator->validated());

        return response()->json([
            'message' => 'Product successfully created',
            'product' => $product
        ], 201);
    }

    public function read($productId)
    {
        $product = Product::find($productId);

        if(!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json($product);
    }

    public function update(Request $request, $productId)
    {
        $product = Product::find($productId);

        if(!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
            'img' => 'sometimes|string',
            'sizes' => 'sometimes|array',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $product->update($validator->validated());

        return response()->json([
            'message' => 'Product successfully updated',
            'product' => $product
        ]);
    }

    public function delete($productId)
    {
        $product = Product::find($productId);

        if(!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product successfully deleted',
        ], 200);
    }
}
