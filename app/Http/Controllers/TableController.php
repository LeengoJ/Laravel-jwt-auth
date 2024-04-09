<?php
namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'floor' => 'required|integer',
            'status' => 'required|string',
            'tableName' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $table = Table::create($validator->validated());

        return response()->json([
            'message' => 'Table successfully created',
            'table' => $table
        ], 201);
    }

    public function read($id)
    {
        $table = Table::find($id);

        if(!$table) {
            return response()->json([
                'message' => 'Table not found',
            ], 404);
        }

        return response()->json($table);
    }

    public function update(Request $request, $id)
    {
        $table = Table::find($id);

        if(!$table) {
            return response()->json([
                'message' => 'Table not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
            'floor' => 'sometimes|integer',
            'status' => 'sometimes|string',
            'tableName' => 'sometimes|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $table->update($validator->validated());

        return response()->json([
            'message' => 'Table successfully updated',
            'table' => $table
        ]);
    }

    public function delete($id)
    {
        $table = Table::find($id);

        if(!$table) {
            return response()->json([
                'message' => 'Table not found',
            ], 404);
        }

        $table->delete();

        return response()->json([
            'message' => 'Table successfully deleted',
        ], 200);
    }
}
