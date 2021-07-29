<?php


namespace App\Http\Controllers;


use App\Models\Inventory;
use App\Models\Rental;
use http\Exception\BadRequestHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class RentalController extends Controller
{
    public function getAllRentalByInventoryId($id)
    {
        $inventory = Inventory::findOrFail($id);
        return response(Rental::where('inventoryId', $inventory->id)->where('returnDate', null)->get()->toJson(JSON_PRETTY_PRINT));
    }

    public function getAll()
    {
        return response(Rental::get()->toJson(JSON_PRETTY_PRINT));
    }

    public function get($id)
    {
        return response(Rental::findOrFail($id)->toJson(JSON_PRETTY_PRINT));
    }

    public function create(Request $request)
    {
        $inventory = Inventory::findOrFail($request->inventoryId);
        if($inventory->lendability != 1) {
            throw new PreconditionFailedHttpException("The inventory with the given id of " .$request->inventoryId. " is not lendable!");
        }
        if($inventory->count <= 0) {
            throw new PreconditionFailedHttpException("There is no more inventory to lend with the given id of " .$request->inventoryId. "!");
        }
        $validator = Validator::make($request->all(),[
            'inventoryId' => 'required|exists:inventory,id',
            'name' => 'required|max:45',
            'adress' => 'required|max:45',
            'email' => 'required|email|max:45',
            'deposit' => 'required|numeric|min:0',
            'phone' => 'required|max:45',
            'borrowDate' => 'required|date_format:Y-m-d|before_or_equal::now',
            'dueDate' => 'required|date_format:Y-m-d|after_or_equal::now',
            'returnDate' => 'nullable|date_format:Y-m-d|before_or_equal::now',
            'comment' => 'max:45',
            'lendingUser' => 'required|exists:user,id', //TODO User authorization
            'receivingUser' => 'nullable|exists:user,id' //TODO User authorization
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 412);
        }
        $inventory->count -= 1;
        if(!$inventory->save()) {
            throw new BadRequestHttpException("Couldn't update the inventory count!");
        }
        $rental = new Rental;
        $rental->inventoryId = $request->inventoryId;
        $rental->name = $request->name;
        $rental->adress = $request->adress;
        $rental->email = $request->email;
        $rental->deposit = $request->deposit;
        $rental->phone = $request->phone;
        $rental->borrowDate = $request->borrowDate;
        $rental->dueDate = $request->dueDate;
        $rental->returnDate = $request->returnDate;
        $rental->comment = $request->comment;
        $rental->lendingUser = 1; //TODO User authorization
        $rental->receivingUser = $request->receivingUser; //TODO User authorization
        if($rental->save()) {
            return response($rental->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't create the new rental!");
        }
    }

    public function update($id, Request $request)
    {
        $rental = Rental::findorFail($id);
        $validator = Validator::make($request->all(),[
            'inventoryId' => 'required|exists:inventory,id',
            'name' => 'required|max:45',
            'adress' => 'required|max:45',
            'email' => 'required|email|max:45',
            'deposit' => 'required|numeric|min:0',
            'phone' => 'required|max:45',
            'borrowDate' => 'required|date_format:Y-m-d|before_or_equal::now',
            'dueDate' => 'required|date_format:Y-m-d|after_or_equal::now',
            'returnDate' => 'nullable|date_format:Y-m-d|before_or_equal::now',
            'comment' => 'max:45',
            'lendingUser' => 'required|exists:user,id', //TODO User authorization
            'receivingUser' => 'nullable|exists:user,id' //TODO User authorization
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 412);
        }
        $rental->inventoryId = $request->inventoryId;
        $rental->name = $request->name;
        $rental->adress = $request->adress;
        $rental->email = $request->email;
        $rental->deposit = $request->deposit;
        $rental->phone = $request->phone;
        $rental->borrowDate = $request->borrowDate;
        $rental->dueDate = $request->dueDate;
        $rental->returnDate = $request->returnDate;
        $rental->comment = $request->comment;
        $rental->lendingUser = 1; //TODO User authorization
        $rental->receivingUser = $request->receivingUser; //TODO User authorization
        if($rental->save()) {
            return response($rental->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't save the updated rental!");
        }
    }

    public function delete($id)
    {
        $rental = Rental::findOrFail($id);
        $inventory = Inventory::findOrFail($rental->inventoryId);
        if(!$rental->delete()) {
            throw new BadRequestHttpException("Couldn't delete the rental count!");
        }
        $inventory->count += 1;
        if(!$inventory->save()) {
            throw new BadRequestHttpException("Couldn't update the inventory count!");
        }
    }
}
