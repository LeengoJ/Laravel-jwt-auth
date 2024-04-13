<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Table;

class TableController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'floor' => 'required|integer',
            'status' => 'required|string',
            'tableNumber' => 'required|unique:tables',
        ]);

        if($validator->fails()){
                        return json_encode(Response::error(Response::CVTM($validator)));

        }

        $table = Table::create($validator->validated());

        return response()->json([
            'message' => 'Table successfully created',
            'table' => $table
        ], 201);
    }

    public function getTable($tableId)
    {
        $table = Table::find($tableId);

        if(!$table) {
            return response()->json([
                'message' => 'Table not found',
            ], 404);
        }

        return response()->json($table);
    }

    public function getAllTables()
    {
        $tables = Table::all();

        return response()->json($tables);
    }

    public function update(Request $request, $tableId)
    {
        $table = Table::find($tableId);

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
                        return json_encode(Response::error(Response::CVTM($validator)));

        }

        $table->update($validator->validated());

        return response()->json([
            'message' => 'Table successfully updated',
            'table' => $table
        ]);
    }

    public function delete($tableId)
    {
        $table = Table::find($tableId);

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

    public function searchByName(Request $request)
    {
        $name = $request->all()["name"];
        $table = Table::where('name', 'like', '%' . $name . '%')->get();

        if($table->isEmpty()) {
        return response()->json([
                'message' => 'No table found',
            ], 404);
        }

        return response()->json($table);
    }
    public function updateStatusTable(Request $request, $tableId)
    {
        $table = Table::find($tableId);

        if(!$table) {
                return response()->json([
                    'message' => 'Table not found',
                ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
        ]);

        if($validator->fails()){
            return json_encode(Response::error(Response::CVTM($validator)));
        }

        $table->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Table status successfully updated',
            'table' => $table
        ]);
    }
}
