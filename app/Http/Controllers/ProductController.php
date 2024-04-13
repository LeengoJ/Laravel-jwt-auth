<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ProductController extends Controller
{
    public function updateProductDetails(){

    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:products',
            'img' => 'sometimes|string',
            'sizes' => 'required',
        ]);

        if($validator->fails()){

            return json_encode(Response::error(Response::CVTM($validator)));

        }

        $product = Product::create($validator->validated());

        return response()->json([
            'message' => 'Product successfully created',
            'product' => $product
        ], 201);
    }

    public function getProduct($productId)
    {
        $product = Product::find($productId);

        if(!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json($product);
    }

    public function getAllProducts()
    {
        $products = Product::all();

        return response()->json($products);
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
            'sizes' => 'sometimes|string',
        ]);

        if($validator->fails()){
                        return json_encode(Response::error(Response::CVTM($validator)));

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

    public function searchByName(Request $request)
    {
        $name = $request->all()["name"];
        $product = Product::where('name', 'like', '%' . $name . '%')->get();

        if($product->isEmpty()) {
        return response()->json([
                'message' => 'No table found',
            ], 404);
        }

        return response()->json($product);
    }
}
