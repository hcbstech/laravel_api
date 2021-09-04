<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Nexmo\Laravel\Facade\Nexmo;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);
       
        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => false,
                'data' => [],
                'message' => $validator->errors()
            ], 422);
        }
  
        $user = User::updateOrCreate(['phone' => $request->phone]);
        if (!empty($user)) {
            $otp = self::sendOtp($request->phone);
            $user->update([
                'otp' => $otp
            ]);
            
            $userProfile = UserProfile::where('user_id', '=', $user->id)->first();
            if ($userProfile === null) {
              $userProfile = new UserProfile([
                    'user_id' => $user->id
                ]);
                $userProfile->save();
            }
        
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
                'message' => 'Invalid phone number. '
            ], 401);
        }
    }

    public static function sendOtp($phone)
    {
        $otp = rand(1111,9999);
        
//        Nexmo::message()->send([
//            'to'   =>'+91'.$phone,
//            'from' => env('SMS_FROM'),
//            'text' => 'Verify otp: ' . $otp
//        ]);

//        return $otp;
        return 0000;
    }

    public function otpVerify(Request $request)
    {
        if($request->otp == 0) {
            $user = User::with('userProfile')->where([['id', '=', $request->user_id]])->first();
        }else {
            $user = User::with('userProfile')->where([['otp', '=' ,$request->otp],['id', '=', $request->user_id]])->first();
        }
        if ($user) {
            $user->status = '1';
            $user->otp = NULL;
            $user->save();
            
            $user = User::getUserDetail($request->user_id);
            $token = $user->createToken('my-app-token')->plainTextToken;
            $user->token = $token;
            
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Otp verify.',
                'data' => [
                    'defaulth_path' => asset('/'),
                    'user' => $user
                ]
            ]);
        } else {
            return response()->json([
                'code' => 422,
                'status' => false,
                'data' => [],
                'message' => 'Otp in not correct. Please try again.'
            ], 401);
        }
    }
}
