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
        if (auth()->user()->username != 'admin') {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }

        $inventory = Inventory::findOrFail($id);
        return response(Rental::where('inventoryId', $inventory->id)->where('returnDate', null)->get()->toJson(JSON_PRETTY_PRINT));
    }

    public function getAll()
    {
        if (auth()->user()->username == 'admin') {
            return response(Rental::get()->toJson(JSON_PRETTY_PRINT));
        }

        return response(Rental::where('receivingUser', auth()->user()->id)->get()->toJson(JSON_PRETTY_PRINT));
    }

    public function get($id)
    {
        if (auth()->user()->username != 'admin') {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }

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
        // \Log::info($request->all());
        // exit;
        $validator = Validator::make($request->all(),[
            'inventoryId' => 'required|exists:inventory,id',
            'name' => 'required|max:45',
            'adress' => 'required|max:45',
            'email' => 'required|email|max:45',
            'deposit' => 'required|numeric|min:0',
            'phone' => 'required|max:45',
            'borrowDate' => 'required|date_format:Y-m-d|after_or_equal:today',
            'dueDate' => [
                'required',
                'date_format:Y-m-d',
                'after:borrowDate',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->has('borrowDate')) {
                        $borrowDate = \Carbon\Carbon::parse($request->borrowDate);
                        $dueDate = \Carbon\Carbon::parse($value);

                        if ($dueDate->diffInDays($borrowDate) > 31) {
                            $fail('The due date must not be more than one month after the borrow date.');
                        }
                    }
                },
            ],
            'comment' => 'max:45',
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
        $rental->comment = $request->comment;
        $rental->lendingUser = auth()->user()->id;
        $rental->receivingUser = auth()->user()->id;
        $rental->status = 'processing';
        if($rental->save()) {
            return response($rental->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't create the new rental!");
        }
    }

    public function update($id, Request $request)
    {
        $rental = Rental::findOrFail($id);

        if (auth()->user()->username != 'admin' && auth()->user()->id != $rental->receivingUser) {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }

        // Update validation rules
        $validator = Validator::make($request->all(), [
            'inventoryId' => 'nullable|exists:inventory,id',

            'name' => 'nullable|max:45',
            'adress' => 'nullable|max:45',
            'email' => 'nullable|email|max:45',

            'deposit' => 'nullable|numeric|min:0',
            'phone' => 'nullable|max:45',

            'borrowDate' => 'nullable|date_format:Y-m-d|after_or_equal:now',
            'dueDate' => 'nullable|date_format:Y-m-d|after:borrowDate',
            'returnDate' => 'nullable|date_format:Y-m-d',
            'comment' => 'nullable|max:45',

            'status' => 'nullable|in:processing,shipping,delivered,returning,returned',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 412);
        }

        // Update only the provided fields
        foreach ($request->all() as $key => $value) {
            if ($value !== null && $rental->isFillable($key)) {
                $rental->$key = $value;
            }
        }


        // Attempt to save changes
        if ($rental->save()) {
            return response($rental->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't save the updated rental!");
        }
    }

    public function delete($id)
    {
        if (auth()->user()->username != 'admin') {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }

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
