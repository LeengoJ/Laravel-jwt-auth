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

        // return response()->json([
        //     'message' => '',
        //     'product' => $product
        // ], 201);
        return json_encode(Response::success($product,"Product successfully created"));

    }

    public function getProduct($productId)
    {
        $product = Product::find($productId);

        if(!$product) {
            // return response()->json([
            //     'message' => ,
            // ], 404);
            return json_encode(Response::error('Product not found'));

        }

        // return response()->json($);
            return json_encode(Response::success($product,"Successfully"));

    }

    public function getAllProducts()
    {
        $products = Product::all();

        // return response()->json($);
            return json_encode(Response::success($products,"Successfully"));

    }

    public function update(Request $request, $productId)
    {
        $product = Product::find($productId);

        if(!$product) {
            return json_encode(Response::error('Product not found'));

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

        // return response()->json([
        //     'message' => '',
        //     'product' => $product
        // ]);
        return json_encode(Response::success($product,"Product successfully updated"));

    }

    public function delete($productId)
    {
        $product = Product::find($productId);

        if(!$product) {
            return json_encode(Response::error('Product not found'));

        }
        $product->delete();
        return json_encode(Response::success([],"Product successfully deleted"));

    }

    public function searchByName(Request $request)
    {
        $name = $request->all()["name"];
        $product = Product::where('name', 'like', '%' . $name . '%')->get();

        if($product->isEmpty()) {
            return json_encode(Response::error('Product not found'));

        }

        return json_encode(Response::success($product,"Product successfully updated"));

    }
}
