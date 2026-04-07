<?php

namespace App\Http\Services;

use App\Models\Campaign;
use Exception;
use Illuminate\Support\Facades\Log;

class AddNewCampaignService
{
    public function createCampaign(array $data)
    {
        try {
           
            return Campaign::create([
                'title'      => $data['title'],
                'body'       => $data['content'], 
                'status'     => 'draft',          
                'created_by' => auth()->id() ?? 1, 
                'send_at'    => now(),           
            ]);
        } catch (Exception $e) {
            Log::error("Lỗi Database khi tạo Campaign: " . $e->getMessage());
            throw $e;
        }
    }
}