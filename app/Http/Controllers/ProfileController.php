<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProfile()
    {
        $id = Auth::user()->id;
        $user = User::with('userProfile')->where('id',$id)->first();

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
                'message' => 'Invalid data. '
            ], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users,email,' . $request->id,
            'phone' => 'required|unique:users,phone,' . $request->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 401);
        }
        
        $user = User::find($request->id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_type' => $request->role_type,
        ]);

        UserProfile::where('user_id', $request->id)->update([
            "profession" => $request->profession,
            "company_name" => $request->company_name,
            "meeting_type" => $request->meeting_type
        ]);

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => "User profile updated.",
            'data' => [
                'user' => $user
            ]
        ]);
    }
}
