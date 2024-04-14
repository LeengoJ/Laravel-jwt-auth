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
        return json_encode(Response::success($table,"Table successfully created"));

    }

    public function getTable($tableId)
    {
        $table = Table::find($tableId);

        if(!$table) {
            return json_encode(Response::error('Table not found'));

        }

            return json_encode(Response::success($table,"Success"));

    }

    public function getAllTables()
    {
        $tables = Table::all();

        return json_encode(Response::success($tables,""));

    }

    public function update(Request $request, $tableId)
    {
        $table = Table::find($tableId);

        if(!$table) {
            return json_encode(Response::error('Table not found'));
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
            'floor' => 'sometimes|integer',
            'tableNumber' => 'sometimes|string',
        ]);

        if($validator->fails()){
            return json_encode(Response::error(Response::CVTM($validator)));

        }
        $table->update($validator->validated());
        return json_encode(Response::success($table,"Table successfully updated"));

    }

    public function delete($tableId)
    {
        $table = Table::find($tableId);

        if(!$table) {
            return json_encode(Response::error('Table not found'));

        }

        $table->delete();
            return json_encode(Response::success([],"Table successfully deleted"));

    }

    public function searchByName(Request $request)
    {
        $name = $request->all()["name"];
        $table = Table::where('name', 'like', '%' . $name . '%')->get();

        if($table->isEmpty()) {
            return json_encode(Response::error('Table not found'));

        }
            return json_encode(Response::success($table,""));

    }
    public function updateStatusTable(Request $request, $tableId)
    {
        $table = Table::find($tableId);

        if(!$table) {
            return json_encode(Response::error('Table not found'));

        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
        ]);

        if($validator->fails()){
            return json_encode(Response::error(Response::CVTM($validator)));
        }

        $table->update(['status' => $request->status]);
            return json_encode(Response::success($table,"Table status successfully updated"));

    }
}
