<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'campaign_id',
        'title',
        'message',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the user for the notification.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToUser
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the campaign for the notification.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToCampaign
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Scope to get unread notifications.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
