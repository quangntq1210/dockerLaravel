<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Campaign extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   protected $fillable = ['title', 'body', 'send_at', 'status', 'created_by'];

    // Lấy danh sách record trung gian
    public function recipients()
    {
        return $this->hasMany(CampaignRecipient::class);
    }

    // Lấy trực tiếp danh sách subscriber thông qua bảng trung gian
    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'campaign_recipients');
    }
}
