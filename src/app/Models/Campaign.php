<?php

namespace App\Models;

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
    
    /**
     * Get the recipients for the campaign.
     * @return \Illuminate\Database\Eloquent\Relations\HasManyCampaignRecipient
     */
    public function recipients()
    {
        return $this->hasMany(CampaignRecipient::class);
    }

    /**
     * Get the subscribers for the campaign.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToManyCampaignRecipient
     */
    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'campaign_recipients');
    }
}
