<?php

namespace App\Http\Controllers\Api;

use App\Models\Users\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rules;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response([
                'message' => 'Tài khoản không tồn tại'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Mật khẩu không đúng vui lòng thử lại'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $access_token = $user->createToken('authToken');
        return response([
            'user' => $user->toArray(),
            'access_token' => $access_token->plainTextToken
        ], Response::HTTP_OK);

        return response([
            'message' => 'This User does not exist'
        ], Response::HTTP_UNAUTHORIZED);
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);


        return response($user, Response::HTTP_CREATED);
    }

    public function profile()
    {
        $user = auth()->user();
        if ($user) {
            return response([
                'user' => $user,
            ], Response::HTTP_OK);
        } else {
            return response([], Response::HTTP_NOT_FOUND);
        }
    }

    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();

        // $tokenid = \Str::before(request()->bearertoken(), '|');
        // auth()->user()->tokens()->where('id', $tokenid )->delete();
        //     return response([
        //     ], Response::HTTP_OK);
    }
}
