<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class AuthController extends Controller{
    // ユーザ登録する処理
    public function register(Request $request){
        //Requestから値を受けとる 
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        
        // モデルでユーザ情報をデータベースに登録
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
        
        
        $token = $user->createToken('auth_token')->plainTextToken;
        $user_id = $user->id;

        return view('users.added', ['user' => $user]);
        // return response()->json(['user_id' => $user_id], 200);

    }
    public function login(Request $request)    {
        if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
        'message' => 'Invalid login details'
                ], 401);
        }
    
        $user = User::where('email', $request['email'])->firstOrFail();
        
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user_id' =>  $user->id,
            'user_name' =>  $user->name,
        ]);
    } 
    
    public function add_user_form()
    {
        return view('add_user_form');
    }
}
