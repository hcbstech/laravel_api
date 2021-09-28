<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Meeting;
use App\Models\UserProfile;
use App\Models\UserGalleries;
use App\Models\Countries;
use App\Models\States;
use App\Models\Cities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use DB;
//FOLLOW THIS LINK FOR PAYTM https://developer.paytm.com/docs/checksum/?ref=other
use PaytmChecksum;

class TransactionController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function initialise(Request $request)
    {
        $id = Auth::user()->id;
        if(isset($request->amount) && isset($request->currency)) {
            $currency = "INR";
            if($request->amount > 0) {
                $amount = $request->amount;
                $currency = $request->currency;
                $mid = "vqeBTL09813255368037";
                $mkey = "#kILDngqVxOssqmC";
                $orderId = $request->order_id;
                
                require_once(base_path() . "/vendor/paytm/paytmchecksum/PaytmChecksum.php");
                $paytmParams = array();
                $paytmParams["body"] = array(
                    "requestType"   => "Payment",
                    "mid"           => $mid,
                    "websiteName"   => "MeetNPay",
                    "orderId"       => $orderId,
                    "callbackUrl"   => "http://lapi.hcbspro.com/api/callback",
                    "txnAmount"     => array(
                        "value"     => $amount,
                        "currency"  => $currency,
                    ),
                    "userInfo"      => array(
                        "custId"    => $id,
                    ),
                );

                /*
                * Generate checksum by parameters we have in body
                * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
                */
                $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), $mkey);
                $paytmParams["head"] = array("signature"=>$checksum);

                $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

                $token = $this->getToken($post_data,$mid,$orderId);
                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'data' => [
                        'transaction' => json_decode($token)
                    ]
                ]);
            }else {
                return response()->json([
                    'code' => 200,
                    'status' => false,
                    'data' => [
                        'transaction' => []
                    ],
                    'message' => 'Amount Should Be More Than 10.'
                ]);
            }
        }else {
             return response()->json([
                'code' => 200,
                'status' => false,
                'data' => [
                    'transaction' => []
                ],
                'message' => 'Amount & Currency Must Be Required.'
            ]);
        }
        
    }
    
    public function getToken($data,$mid,$orderId) {
        /* for Staging */
//        $url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=$mid&orderId=$orderId";
        /* for Production */
         $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=$mid&orderId=$orderId";
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,     
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
               'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
        
    }
    
    public function callback(Request $request) {
        echo"<pre>";
        print_r($request);
        exit();
    }
}
