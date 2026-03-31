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
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'campaign_id.required'      => 'Vui lòng chọn campaign.',
            'campaign_id.exists'        => 'Campaign không tồn tại.',
            'subscriber_ids.required'   => 'Vui lòng chọn ít nhất 1 người nhận.',
            'subscriber_ids.min'        => 'Vui lòng chọn ít nhất 1 người nhận.',
            'subscriber_ids.*.exists'   => 'Một hoặc nhiều người nhận không hợp lệ.',
            'send_at.required'          => 'Vui lòng chọn thời gian gửi.',
            'send_at.after'             => 'Thời gian gửi phải ở trong tương lai.',
        ];
    }
}
