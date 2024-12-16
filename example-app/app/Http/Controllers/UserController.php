<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends Controller
{
    public function getAll()
    {
        return response(User::get()->toJson(JSON_PRETTY_PRINT));
    }

    public function get($id)
    {
        return response(User::findOrFail($id)->toJson(JSON_PRETTY_PRINT));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:45',
            'email' => 'required|email|unique:user|max:45',
            'password' => 'required|max:256'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 412);
        }
        $user = new User;
        $user->name = $request->name;
        $user->username = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        if($user->save()) {
            return response($user->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't create the new user!");
        }
    }

    public function update($id, Request $request)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|max:45',
            'username' => 'nullable|max:45|unique:user,username,' . $id,
            'phone_numbers' => 'nullable|max:45',
            'email' => 'nullable|email|unique:user,email,' . $id . '|max:45',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 412);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('username')) {
            $user->username = $request->username;
        }
        if ($request->has('phone_numbers')) {
            $user->phone_numbers = $request->phone_numbers;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($user->save()) {
            return response($user->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't update the user!");
        }
    }

    public function delete($id)
    {
        if(User::findOrFail($id)->delete()) {
            throw new BadRequestHttpException("Couldn't delete the user!");
        }
    }
}

