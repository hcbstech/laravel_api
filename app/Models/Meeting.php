<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;
    
    protected $table = "meetings";
    
    protected $fillable = [
        'user_id',
        'receiver_id',
        'meetingtype_id',
        'meeting_status'
    ];
    
    public function userProfileByPending() {
        return $this->belongsTo('App\Models\UserProfile','user_id','user_id');
    }
    public function userProfileBySending() {
        return $this->belongsTo('App\Models\UserProfile','receiver_id','user_id');
    }
}
