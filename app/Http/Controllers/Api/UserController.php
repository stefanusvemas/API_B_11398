<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return response([
            'message' => 'Retrieve All Users Success',
            'data' => $users
        ], 200);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }

        return response([
            'message' => 'Retrieve User Success',
            'data' => $user
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:60',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required|min:8',
            'no_telp' => 'required|regex:/^08[0-9]{9,11}$/',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $user->update($request->all());

        return response([
            'message' => 'Update User Success',
            'data' => $user
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response([
            'message' => 'Delete User Success',
            'data' => $user
        ], 200);
    }
}
