<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGalleries extends Model
{
    use HasFactory;
    
    protected $table = "user_galleries";


    protected $fillable = [
        'user_id',
        'gallery_type',
        'gallery_url',
        'gallery_status',
    ];
}
