<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            return [
                'message' => 'Sorry, the credentials is incorrect!',
            ];
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('inventive-media-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function check_credentials(Request $request){
        $request->validate(['email' => ['required'], 'password' => ['required']]);

        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return [
                'Sorry, the credentials is incorrect!'
            ];
        }else{
            return [];
        }
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return response(null);
    }

    public function change_password(Request $request){
        $request->validate(['old_password' => ['required'], 'new_password' => ['required', 'different:old_password']]);

        return abort(403);
    }
}
