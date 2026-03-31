<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Authenticatable
{
    use SoftDeletes, HasFactory;
    protected $fillable = ['name', 'email', 'user_id'];

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_recipients');
    }
}
