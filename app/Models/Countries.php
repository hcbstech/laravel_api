<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserProfile;

class Countries extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "countries";
    
    protected $fillable = [
        'name'
    ];
    
    public function userProfileCountry()
    {
        return $this->hasMany(UserProfile::country, 'id');
    }
    
}
