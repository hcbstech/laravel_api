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

class MeetingController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = Auth::user()->id;
        $meetings = Meeting::where('user_id',$id)
                ->Where('receiver_id',$request->receiver_id)
                ->where('meeting_status','2')
                ->get();
        if (count($meetings) == 0) {
            $meeting = new Meeting();
            $meeting->user_id = $id;
            $meeting->receiver_id = $request->receiver_id;
            $meeting->meetingtype_id = $request->type_id;
            $meeting->start_time = $request->approx_start_time;
            $meeting->end_time = $request->approx_end_time;
            $meeting->venue = $request->venue;
            $meeting->save();
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Meeting request successfully sent.',
                'data' => $meeting
            ]);
        }else {
           return response()->json([
                'code' => 200,
                'status' => false,
                'data' => [
                    'user' => []
                ],
                'message' => 'Other Meeting Already Is Pending From User.'
            ]);
        }
        
    }
    public function getMeetings()
    {
        $id = Auth::user()->id;
        $meetings = [];
        $arary1 = Meeting::with('userProfileByPending')
                ->Where('receiver_id',$id)
                ->where('meeting_status',1)
                ->get();
        if(!empty($arary1)) {
            foreach ($arary1 as $v1) {
                array_push($meetings, $v1);
            }
        }
        $arary2 = Meeting::with('userProfileBySending')
                ->Where('user_id',$id)
                ->where('meeting_status',1)
                ->get();
        if(!empty($arary2)) {
            foreach ($arary2 as $v2) {
                array_push($meetings, $v2);
            }
        }
        if (count($meetings) > 0) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => [
                    'user' => $meetings
                ]
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => [
                    'user' => []
                ],
                'message' => 'Data Not Found.'
            ]);
        }
    }
    public function pendingMeetings()
    {
        $id = Auth::user()->id;
        $meets = Meeting::with('userProfileByPending')
                ->select('meetings.*')
                ->where('meetings.receiver_id',$id)
                ->get();
        if (count($meets) > 0) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => [
                    'user' => $meets
                ]
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => [
                    'user' => []
                ],
                'message' => 'Data Not Found.'
            ]);
        }
    }
    public function sentMeetings()
    {
        $id = Auth::user()->id;
        $meets = Meeting::with('userProfileBySending')
                ->select('meetings.*')
                ->where('meetings.user_id',$id)
                ->get();
        if (count($meets) > 0) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => [
                    'user' => $meets
                ]
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => [
                    'user' => []
                ],
                'message' => 'Data Not Found.'
            ]);
        }
    }
    public function actionMeeting(Request $request)
    {
        $meetig = Meeting::find($request->meeting_id);
        if($meetig) {
            $meetig->id = $request->meeting_id;
            $meetig->meeting_status = $request->status;
            $meetig->save();
            if($request->status == 1) {
                $message = "Request Accept Successfully.";
            }else if($request->status == 3){
                $message = "Request Declined Successfully.";
            }else if($request->status == 4){
                $message = "Request Cancel Successfully.";
            }
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => $message,
                'data' => $meetig
            ]);
        }else {
            return response()->json([
                'code' => 200,
                'status' => false,
                'data' => [
                    'user' => []
                ],
                'message' => 'Invalid Meeting ID. '
            ]);
        }
        
    }
}
