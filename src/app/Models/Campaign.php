<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'body', 'send_at', 'status', 'created_by'];
    
    /**
     * Get the recipients for the campaign.
     */
    public function recipients()
    {
        return $this->hasMany(CampaignRecipient::class);
    }

    /**
     * Get the subscribers for the campaign.
     */
    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'campaign_recipients');
    }
}