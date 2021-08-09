<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'role_type' => 'required',
            'phone_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => false,
                'data' => [],
                'message' => $validator->errors()
            ], 422);
        }
  
        $user = User::where('phone_no', $request->phone_no)->first();
        if (!empty($user)) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => [
                    'user' => $user
                ]
            ]);
        } else {
            return response()->json([
                'code' => 422,
                'status' => false,
                'data' => [],
                'message' => 'Invalid Credential. '
            ], 401);
        }
    }
}
