<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        if(!$request->hasFile('fileName')) {
            return json_encode(Response::error('upload_file_not_found'));
        }

        $allowedfileExtension=['jpeg','jpg','png','btm'];
        $file = $request->file('fileName');
        $errors = [];

        $fileName=null;

        // foreach ($files as $file) {

            $extension = $file->getClientOriginalExtension();

            $check = in_array(strtolower($extension),$allowedfileExtension);

            if($check) {
                $fileName = time().'_'.$file->getClientOriginalName();
                $filePath = $file->storeAs('public/images', $fileName);

                // $fileName = $file->getClientOriginalName();
                // $fileName = $request->get('name') . '.' . $request->file('photo')->extension();        
                // $request->file('fileName')->storeAs('public/images', $fileName);
                // foreach($request->fileName as $mediaFiles) {
                    // $path = $file->store('public/images');
                    // $fileName = $file->getClientOriginalName();
                // }
                return json_encode(Response::success($fileName,"file_uploaded"));
            } else {
                return json_encode(Response::error('invalid_file_format'));
            }
        // }
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:products',
            'description' => 'required|string',
            'sizes' => 'required',
        ]);

        if($validator->fails()){
            return json_encode(Response::error(Response::CVTM($validator)));
        }

        $fileName="";

        if($request->hasFile('img')) {
            $file = $request->file('img');
            $extension = $file->getClientOriginalExtension();
            $allowedfileExtension=['jpeg','jpg','png','btm'];
            $check = in_array(strtolower($extension),$allowedfileExtension);
            if($check) {
                $fileName = time().'_'.$file->getClientOriginalName();
                $filePath = $file->storeAs('public/images', $fileName);
            } else {
                return json_encode(Response::error('Ảnh chỉ được png, jpg, jpeg, btm'));
            }
        }

        $productData=[
            'name' => $request->get('name'),
            'img' => $fileName,
            'sizes' => $request->get('sizes'),
            'description' => $request->get('description'),
        ];

        $product = Product::create($productData);

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
            'sizes' => 'sometimes|string',
            'description' => 'required|string'
        ]);

        if($validator->fails()){
            return json_encode(Response::error(Response::CVTM($validator)));
        }

        $fileName="";

        if($request->hasFile('img')) {
            $file = $request->file('img');
            $extension = $file->getClientOriginalExtension();
            $allowedfileExtension=['jpeg','jpg','png','btm'];
            $check = in_array(strtolower($extension),$allowedfileExtension);
            if($check) {
                $fileName = time().'_'.$file->getClientOriginalName();
                $filePath = $file->storeAs('public/images', $fileName);
            } else {
                return json_encode(Response::error('Ảnh chỉ được png, jpg, jpeg, btm'));
            }
        }

        $productData=[
            'name' => $request->get('name'),
            'sizes' => $request->get('sizes'),
            'description' => $request->get('description')
        ];
        if($fileName!==""){
            $productData["img"] = $fileName;
        }

        $product->update($productData);

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
