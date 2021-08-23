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
            'meeting_type' => 'required',
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
  
        $user = User::where('phone', $request->phone)->first();
        if (!empty($user)) {
            $otp = self::sendOtp($request->phone);

            $user->update([
                'otp' => $otp
            ]);

            UserProfile::updateOrCreate([
                'user_id' => $user->id,
            ],[
                'meeting_type' => $request->meeting_type
            ]);
        
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
        
        Nexmo::message()->send([
            'to'   =>'+91'.$phone,
            'from' => env('SMS_FROM'),
            'text' => 'Verify otp: ' . $otp
        ]);

        return $otp;
    }

    public function otpVerify(Request $request)
    {
        $user = User::with('userProfile')->where([['otp', '=' ,$request->otp],['id', '=', $request->user_id]])->first();
        
        if ($user) {
            $is_profile = '0';
            
            if ($user->userProfile->profession != '' && $user->userProfile->company_name != '') {
                $is_profile = '1';
            }
            $user->status = '1';
            $user->otp = '';
            $user->save();
            
            $token = $user->createToken('my-app-token')->plainTextToken;
            $user->token = $token;
            $user->is_profile = $is_profile;

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Otp verify.',
                'data' => [
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
