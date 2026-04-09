<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Authenticatable
{
    use SoftDeletes, HasFactory;
    protected $fillable = ['name', 'email', 'user_id'];

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_recipients');
    }
     
    public function user()  
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
