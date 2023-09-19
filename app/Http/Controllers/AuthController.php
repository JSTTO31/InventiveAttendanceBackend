<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    // public function register(Request $request){
    //     // $utils = new Utilities();
    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);
    //     $token = $user->createToken('example-token')->plainTextToken;

    //     return [
    //         'user' => $user,
    //         'token' => $token
    //     ];
    // }

    public function login(Request $request){
        $request->validate(['email' => ['required', 'exists:users,email'], 'password' => ['required']]);

        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return response()->json([
                'messages' => 'Credentials error!',
                'errors' => [
                    'password' => 'The password is incorrect!',
                ],
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('inventive-media-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response(null);
    }

    public function change_password(Request $request){
        $request->validate(['old_password' => ['required'], 'new_password' => ['required', 'different:old_password']]);

        if(!Hash::check($request->old_password , $request->user()->password)){
            return response()->json([
                'messages' => 'The old password not match!',
                'errors' => [
                    'old_password' => 'The old password not match!',
                ]
            ], 422);
        }

        $user = User::where('email', $request->user()->email)->update([
            'password' => Hash::make($request->new_password),
        ]);

        return $user;
    }

    public function password_reset(Request $request){
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink(['email' => $request->email]);
        return $status;
        return $status === Password::RESET_LINK_SENT;
    }

    public function reset(){

    }
}
