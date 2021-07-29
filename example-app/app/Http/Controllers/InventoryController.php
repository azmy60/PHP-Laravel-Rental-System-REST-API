<?php


namespace App\Http\Controllers;


use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InventoryController extends Controller
{
    public function getAll()
    {
        return response(Inventory::get()->toJson(JSON_PRETTY_PRINT));
    }

    public function get($id)
    {
        return response(Inventory::findOrFail($id)->toJson(JSON_PRETTY_PRINT));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inventoryNo' => 'required|unique:inventory|max:45',
            'description' => 'required|max:45',
            'count' => 'required|numeric|min:0',
            'condition' => 'required|numeric|min:1|max:5',
            'serialNo' => 'required|max:45',
            'lendability' => 'required|numeric|min:0|max:1'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 412);
        }
        $inventory = new Inventory;
        $inventory->inventoryNo = $request->inventoryNo;
        $inventory->description = $request->description;
        $inventory->count = $request->count;
        $inventory->condition = $request->condition;
        $inventory->serialNo = $request->serialNo;
        $inventory->lendability = $request->lendability;
        if ($inventory->save()) {
            return response($inventory->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't create the new inventory!");
        }
    }

    public function update($id, Request $request)
    {
        $inventory = Inventory::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'inventoryNo' => 'required|max:45|unique:inventory,inventoryNo,' . $id,
            'description' => 'required|max:45',
            'count' => 'required|numeric|min:0',
            'condition' => 'required|numeric|min:1|max:5',
            'serialNo' => 'required|max:45|unique:inventory,serialNo,' . $id,
            'lendability' => 'required|numeric|min:0|max:1'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 412);
        }
        $inventory->inventoryNo = $request->inventoryNo;
        $inventory->description = $request->description;
        $inventory->count = $request->count;
        $inventory->condition = $request->condition;
        $inventory->serialNo = $request->serialNo;
        $inventory->lendability = $request->lendability;
        if($inventory->save()){
            return response($inventory->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't update the inventory!");
        }
    }

    public function delete($id)
    {
        if (Inventory::findOrFail($id)->delete()) {
                throw new BadRequestHttpException("Couldn't delete the inventory!");
        }
    }
}
