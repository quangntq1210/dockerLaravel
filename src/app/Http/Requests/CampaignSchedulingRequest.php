<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CampaignSchedulingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'campaign_id' => [
                'required',
                'exists:campaigns,id',
            ],
            'subscriber_ids' => 'required|array|min:1',
            'subscriber_ids.*' => 'required|exists:subscribers,id',
            'send_at' => 'required|date|after:now',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            "campaign_id" => "__('validation.attributes.campaign_id')",
            "subscriber_ids" => "__('validation.attributes.subscriber_ids')",
            "send_at" => "__('validation.attributes.send_at')",
        ];
    }
}
