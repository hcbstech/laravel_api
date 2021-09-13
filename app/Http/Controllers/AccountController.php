<?php

namespace App\Http\Controllers;

use App\Models\User;
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

class AccountController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateVideoProfile(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
            'file'  => 'required|mimes:mp4',
        ]);   
 
        if ($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);                        
        }
        if ($files = $request->file('file')) {
            $imageName = 'profiles/'.time().'.'.$request->file->extension();   
            $request->file->move(public_path('profiles'), $imageName);
            
            $id = Auth::user()->id;
            UserProfile::where('user_id', $id )->update(["video_url"=>$imageName]);
            $user = User::getUserDetail($id);
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => "Video profile updated.",
                'data' => [
                    'user' => $user
                ]
            ]);
        }
    }
    public function createThumbnail($videoUrl, $storageUrl, $fileName, $second, $width = 640, $height = 480) {
        $thumbnail_path=$storageUrl;
        $file = $videoUrl;
        $thumbvideoPath  = $videoUrl;
        $video_path       = $videoUrl;
        $thumbnail_image  = "dcdjncjdnj.jpg";

        $thumbnail_status = VideoThumbnail::createThumbnail($thumbvideoPath,$thumbnail_path,$thumbnail_image, 10);

        //dd($thumbnail_status);
           if($thumbnail_status)
           {
             echo "Thumbnail generated            ";
           }
           else
           {
             echo "thumbnail generation has failed";
           }
           echo"<pre>";
           print_r(11);
           exit();
    }
    
    public function updateGallery(Request $request)
    {
//        $validator = Validator::make($request->all(), 
//        [ 
//            'file' => 'required|mimes:jpeg,png,jpg,gif,svg,mp4',
//        ]);  
//        if ($validator->fails()) {          
//            return response()->json(['error'=>$validator->errors()], 401);                        
//        }
        $id = Auth::user()->id;
        if ($files = $request->file('file')) {
            if(!empty($files)) {
                $i=1;
                foreach($files as $file) {
                    $type = 2;
                    if($file->getMimeType() == 'image/png' || $file->getMimeType() == 'image/jpg' || $file->getMimeType() == 'image/jpeg') {
                        $type = 1;
                    }
                    $imageName = 'galleries/'.time().$i.'.'.$file->extension();   
                    $file->move(public_path('galleries'), $imageName);
                    
                    $gallery = new UserGalleries();
                    $gallery->user_id = $id;
                    $gallery->gallery_type = $type;
                    $gallery->gallery_url = $imageName;
                    $gallery->save();
                    $i++;
                }
                $gallery = UserGalleries::where('user_id',$id)->get();
                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'message' => "Gallery updated successfully.",
                    'data' => [
                        'gallery' => $gallery
                    ]
                ]);
            }
        }
    }
    public function getGallary($id = NULL)
    {
        if($id == NULL) {
            $id = Auth::user()->id;
        }
        $gallery = UserGalleries::where('user_id',$id)->get();
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => [
                'gallery' => $gallery
            ]
        ]);
    }
    public function getUsers()
    {
        $id = Auth::user()->id;
        $users = User::with('userProfile')
                ->where('id','!=',$id)
                ->where('status','1')
                ->where('is_profile','1')
                ->where('is_video_profile','1')
                ->get();
        if (count($users) > 0) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => [
                    'user' => $users
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
    public function getUsersBySearch(Request $request)
    {
        $search = NULL;
        if($request->search != '') {
            $search = $request->search;
        }
        $id = Auth::user()->id;
        if($search != NULL) {
            $users = User::with('userProfile')->select('users.*')
                ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                ->where('users.id','!=',$id)
                ->where('users.status','1')
                ->where('users.is_profile',1)
                ->where('is_video_profile',1)
                ->where('user_profiles.first_name','like','%' . $search . '%')
                ->orWhere('user_profiles.last_name','like','%' . $search . '%')
                ->get();
        }else{
            $users = User::with('userProfile')->select('users.*')
                ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                ->where('users.id','!=',$id)
                ->where('users.status','1')
                ->where('users.is_profile',1)
                ->where('is_video_profile',1)
                ->get();
        }
        if (count($users) > 0) { 
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => [
                    'user' => $users
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
}
