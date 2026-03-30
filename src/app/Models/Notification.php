<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // Cho phép các trường này được chèn dữ liệu nhanh
    protected $fillable = [
        'user_id',
        'campaign_id',
        'title',
        'message',
        'read_at'
    ];

    // Ép kiểu read_at về dạng datetime để dễ xử lý với Carbon
    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Mỗi thông báo thuộc về một User (người nhận)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Một thông báo có thể bắt nguồn từ một Chiến dịch (Campaign) cụ thể
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Scope để lọc nhanh các thông báo chưa đọc
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
