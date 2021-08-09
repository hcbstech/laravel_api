<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_type' => 'required',
            'phone_no' => 'required|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 401);
        }

        $user = new User([
            'phone_no' => $request->phone_no,
            'role_type' => $request->role_type,
            'name' => Str::random(8),
        ]);

        $user->save();

        return response()->json([
                'status' => true,
                'message' => 'Thanks for sign up.',
                'data' => $user
        ], 201);
    }
}
