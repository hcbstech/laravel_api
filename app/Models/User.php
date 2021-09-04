<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserProfile;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'otp',
        'active',
        'role_type',
        
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*@ get user profile detail*/
    public function userProfile() {
        return $this->belongsTo('App\Models\UserProfile','id','user_id');
    }
    
    public static function getUserDetail($id = NULL) {
        if($id == NULL) {
            $id = Auth::user()->id;
        }
        $user = User::with('userProfile')->where('id',$id)->first();
        $user['userProfile']->country_name = null;
        $user['userProfile']->state_name = null;
        $user['userProfile']->city_name = null;
        if($user['userProfile']->country_id != null) {
            $country = Countries::find($user['userProfile']->country_id);
            $user['userProfile']->country_name = $country['name'];
            $country = States::find($user['userProfile']->state_id);
            $user['userProfile']->state_name = $country['name'];
            $country = Cities::find($user['userProfile']->city_id);
            $user['userProfile']->city_name = $country['name'];
        }
        return $user;
    }
}
