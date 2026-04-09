<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CampaignRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'body' => 'required',
            'send_at' => 'required|date|after:now',
            'status' => 'required|in:draft,scheduled,processing,sent,failed,cancelled',
            'created_by' => 'required|exists:users,id',
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
            "title" => "__('validation.attributes.title')",
            "body" => "__('validation.attributes.body')",
            "send_at" => "__('validation.attributes.send_at')",
            "status" => "__('validation.attributes.status')",
            "created_by" => "__('validation.attributes.created_by')",
        ];
    }
}
