<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Countries;
use App\Models\States;
use App\Models\Cities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProfile($id = NULL)
    {
        $user = User::getUserDetail($id);
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
        $input = $request->all();
        $id = Auth::user()->id;
        if(isset($input['is_profile'])) {
            User::where('id',$id)->update(['is_profile'=>$input['is_profile']]);
        }
        unset($input["is_profile"]);
        UserProfile::where('user_id', $id )->update($input);
        $user = User::getUserDetail($id);
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => "User profile updated.",
            'data' => [
                'user' => $user
            ]
        ]);
    }
    public function getCountries(Request $request)
    {
        $countries = Countries::all();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => "Countries list.",
            'data' => $countries
        ]);
    }
    public function getStates(Request $request)
    {
        $states = States::where('country_id','=',$request->country_id)->get();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => "States list.",
            'data' => $states
        ]);
    }
    public function getCities(Request $request)
    {
        $cities = Cities::where('state_id','=',$request->state_id)->get();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => "Cities list.",
            'data' => $cities
        ]);
    }
}
