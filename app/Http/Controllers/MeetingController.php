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
        $arary1 = [];
        $arary2 = [];
        // START SENDER
//        $arary1 = Meeting::with('userProfile/1')
//                ->Where('receiver_id',$id)
//                ->where('meeting_status',1)
//                ->get();
        $met1 = DB::table('meetings')
                ->select('user_profiles.*','user_profiles.id as up_id','meetings.*','meetings.id as meeting_id')
                ->join('user_profiles', 'user_profiles.user_id', '=', 'meetings.user_id')
                ->where('meetings.receiver_id', $id)
                ->where('meetings.meeting_status',1)
                ->get();
        if(!empty($met1)) {
            $user = [];
            foreach ($met1 as $m1) {
                $user['id'] = $m1->meeting_id;
                $user['user_id'] = $m1->user_id;
                $user['receiver_id'] = $m1->receiver_id;
                $user['meetingtype_id'] = $m1->meetingtype_id;
                $user['meeting_status'] = $m1->meeting_status;
                $user['start_time'] = $m1->start_time;
                $user['end_time'] = $m1->end_time;
                $user['venue'] = $m1->venue;
                $user['created_at'] = $m1->created_at;
                $user['updated_at'] = $m1->updated_at;
                $user['user_profile']['id'] = $m1->up_id;
                $user['user_profile']['user_id'] = $m1->user_id;
                $user['user_profile']['first_name'] = $m1->first_name;
                $user['user_profile']['last_name'] = $m1->last_name;
                $user['user_profile']['email'] = $m1->email;
                $user['user_profile']['country_id'] = $m1->country_id;
                $user['user_profile']['state_id'] = $m1->state_id;
                $user['user_profile']['city_id'] = $m1->city_id;
                $user['user_profile']['face_rate'] = $m1->face_rate;
                $user['user_profile']['video_rate'] = $m1->video_rate;
                $user['user_profile']['voice_rate'] = $m1->voice_rate;
                $user['user_profile']['chat_rate'] = $m1->chat_rate;
                $user['user_profile']['video_url'] = $m1->video_url;
                $user['user_profile']['user_thumb'] = $m1->user_thumb;
                $user['user_profile']['profession'] = $m1->profession;
                $user['user_profile']['company_name'] = $m1->company_name;
                $user['user_profile']['meeting_type'] = $m1->meeting_type;
                $user['user_profile']['created_at'] = $m1->created_at;
                $user['user_profile']['updated_at'] = $m1->updated_at;
                array_push($arary1, $user);
            }
        }
        if(!empty($arary1)) {
            foreach ($arary1 as $v1) {
                array_push($meetings, $v1);
            }
        }
//        $arary2 = Meeting::with('userProfileBySending')
//                ->Where('user_id',$id)
//                ->where('meeting_status',1)
//                ->get();
        // START RECEIVER
        $met2 = DB::table('meetings')
                ->select('user_profiles.*','user_profiles.id as up_id','meetings.*','meetings.id as meeting_id')
                ->join('user_profiles', 'user_profiles.user_id', '=', 'meetings.receiver_id')
                ->where('meetings.receiver_id', $id)
                ->where('meetings.meeting_status',1)
                ->get();
        if(!empty($met2)) {
            $user = [];
            foreach ($met2 as $m1) {
                $user['id'] = $m1->meeting_id;
                $user['user_id'] = $m1->user_id;
                $user['receiver_id'] = $m1->receiver_id;
                $user['meetingtype_id'] = $m1->meetingtype_id;
                $user['meeting_status'] = $m1->meeting_status;
                $user['start_time'] = $m1->start_time;
                $user['end_time'] = $m1->end_time;
                $user['venue'] = $m1->venue;
                $user['created_at'] = $m1->created_at;
                $user['updated_at'] = $m1->updated_at;
                $user['user_profile']['id'] = $m1->up_id;
                $user['user_profile']['user_id'] = $m1->user_id;
                $user['user_profile']['first_name'] = $m1->first_name;
                $user['user_profile']['last_name'] = $m1->last_name;
                $user['user_profile']['email'] = $m1->email;
                $user['user_profile']['country_id'] = $m1->country_id;
                $user['user_profile']['state_id'] = $m1->state_id;
                $user['user_profile']['city_id'] = $m1->city_id;
                $user['user_profile']['face_rate'] = $m1->face_rate;
                $user['user_profile']['video_rate'] = $m1->video_rate;
                $user['user_profile']['voice_rate'] = $m1->voice_rate;
                $user['user_profile']['chat_rate'] = $m1->chat_rate;
                $user['user_profile']['video_url'] = $m1->video_url;
                $user['user_profile']['user_thumb'] = $m1->user_thumb;
                $user['user_profile']['profession'] = $m1->profession;
                $user['user_profile']['company_name'] = $m1->company_name;
                $user['user_profile']['meeting_type'] = $m1->meeting_type;
                $user['user_profile']['created_at'] = $m1->created_at;
                $user['user_profile']['updated_at'] = $m1->updated_at;
                array_push($arary2, $user);
            }
        }
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
        $meets =[];
        $arary1 =[];
//        $meets = Meeting::with('userProfileByPending')
//                ->select('meetings.*')
//                ->where('meetings.receiver_id',$id)
//                ->get();
        
        $met1 = DB::table('meetings')
                ->select('user_profiles.*','user_profiles.id as up_id','meetings.*','meetings.id as meeting_id')
                ->join('user_profiles', 'user_profiles.user_id', '=', 'meetings.user_id')
                ->where('meetings.receiver_id', $id)
                ->where('meetings.meeting_status',1)
                ->get();
        if(!empty($met1)) {
            $user = [];
            foreach ($met1 as $m1) {
                $user['id'] = $m1->meeting_id;
                $user['user_id'] = $m1->user_id;
                $user['receiver_id'] = $m1->receiver_id;
                $user['meetingtype_id'] = $m1->meetingtype_id;
                $user['meeting_status'] = $m1->meeting_status;
                $user['start_time'] = $m1->start_time;
                $user['end_time'] = $m1->end_time;
                $user['venue'] = $m1->venue;
                $user['created_at'] = $m1->created_at;
                $user['updated_at'] = $m1->updated_at;
                $user['user_profile']['id'] = $m1->up_id;
                $user['user_profile']['user_id'] = $m1->user_id;
                $user['user_profile']['first_name'] = $m1->first_name;
                $user['user_profile']['last_name'] = $m1->last_name;
                $user['user_profile']['email'] = $m1->email;
                $user['user_profile']['country_id'] = $m1->country_id;
                $user['user_profile']['state_id'] = $m1->state_id;
                $user['user_profile']['city_id'] = $m1->city_id;
                $user['user_profile']['face_rate'] = $m1->face_rate;
                $user['user_profile']['video_rate'] = $m1->video_rate;
                $user['user_profile']['voice_rate'] = $m1->voice_rate;
                $user['user_profile']['chat_rate'] = $m1->chat_rate;
                $user['user_profile']['video_url'] = $m1->video_url;
                $user['user_profile']['user_thumb'] = $m1->user_thumb;
                $user['user_profile']['profession'] = $m1->profession;
                $user['user_profile']['company_name'] = $m1->company_name;
                $user['user_profile']['meeting_type'] = $m1->meeting_type;
                $user['user_profile']['created_at'] = $m1->created_at;
                $user['user_profile']['updated_at'] = $m1->updated_at;
                array_push($arary1, $user);
            }
        }
        if(!empty($arary1)) {
            foreach ($arary1 as $v1) {
                array_push($meets, $v1);
            }
        }
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
        $meets =[];
        $arary2 =[];
//        $meets = Meeting::with('userProfileBySending')
//                ->select('meetings.*')
//                ->where('meetings.user_id',$id)
//                ->get();
        $met2 = DB::table('meetings')
                ->select('user_profiles.*','user_profiles.id as up_id','meetings.*','meetings.id as meeting_id')
                ->join('user_profiles', 'user_profiles.user_id', '=', 'meetings.receiver_id')
                ->where('meetings.receiver_id', $id)
                ->where('meetings.meeting_status',1)
                ->get();
        if(!empty($met2)) {
            $user = [];
            foreach ($met2 as $m1) {
                $user['id'] = $m1->meeting_id;
                $user['user_id'] = $m1->user_id;
                $user['receiver_id'] = $m1->receiver_id;
                $user['meetingtype_id'] = $m1->meetingtype_id;
                $user['meeting_status'] = $m1->meeting_status;
                $user['start_time'] = $m1->start_time;
                $user['end_time'] = $m1->end_time;
                $user['venue'] = $m1->venue;
                $user['created_at'] = $m1->created_at;
                $user['updated_at'] = $m1->updated_at;
                $user['user_profile']['id'] = $m1->up_id;
                $user['user_profile']['user_id'] = $m1->user_id;
                $user['user_profile']['first_name'] = $m1->first_name;
                $user['user_profile']['last_name'] = $m1->last_name;
                $user['user_profile']['email'] = $m1->email;
                $user['user_profile']['country_id'] = $m1->country_id;
                $user['user_profile']['state_id'] = $m1->state_id;
                $user['user_profile']['city_id'] = $m1->city_id;
                $user['user_profile']['face_rate'] = $m1->face_rate;
                $user['user_profile']['video_rate'] = $m1->video_rate;
                $user['user_profile']['voice_rate'] = $m1->voice_rate;
                $user['user_profile']['chat_rate'] = $m1->chat_rate;
                $user['user_profile']['video_url'] = $m1->video_url;
                $user['user_profile']['user_thumb'] = $m1->user_thumb;
                $user['user_profile']['profession'] = $m1->profession;
                $user['user_profile']['company_name'] = $m1->company_name;
                $user['user_profile']['meeting_type'] = $m1->meeting_type;
                $user['user_profile']['created_at'] = $m1->created_at;
                $user['user_profile']['updated_at'] = $m1->updated_at;
                array_push($arary2, $user);
            }
        }
        if(!empty($arary2)) {
            foreach ($arary2 as $v2) {
                array_push($meets, $v2);
            }
        }
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
