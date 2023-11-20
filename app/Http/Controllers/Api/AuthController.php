<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $registrationData = $request->all();

        $validate = validator::make($registrationData, [
            'name' => 'required|max:60',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required|min:8',
            'no_telp' => 'required|regex:/^08[0-9]{9,11}$/',
            'image' => 'required|mimes:jpg,jpeg,png|max:2048',
        ]);
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }
        $registrationData['status'] = 0;
        $registrationData['password'] = bcrypt($request->password);

        $user = User::create($registrationData);

        return response([
            'message' => 'Register Success',
            'user' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $loginData = $request->all();

        $validate = Validator::make($loginData, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]);

        if ($validate->failed()) {
            return response(['message' => $validate->errors()], 400);
        }

        if (!Auth::attempt($loginData)) {
            return response(['message' => 'Invalid Credential'], 401);
        }

        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $token = $user->createToken('Authentication Token')->accessToken;

        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]);
    }
}
