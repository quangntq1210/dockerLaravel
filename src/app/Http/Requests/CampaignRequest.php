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
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
<<<<<<< HEAD
        // return [
        //     'title.required' => 'Vui lòng nhập tiêu đề.',
        //     'title.string' => 'Tiêu đề phải là một chuỗi.',
        //     'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
        //     'body.required' => 'Vui lòng nhập nội dung.',
        //     'send_at.required' => 'Vui lòng chọn thời gian gửi.',
        //     'send_at.date' => 'Thời gian gửi phải là một ngày.',
        //     'send_at.after' => 'Thời gian gửi phải là sau ngày hiện tại.',
        //     'status.required' => 'Vui lòng chọn trạng thái.',
        //     'status.in' => 'Trạng thái không hợp lệ.',
        //     'created_by.required' => 'Vui lòng chọn người tạo.',
        //     'created_by.exists' => 'Người tạo không hợp lệ.',
        // ];
=======
        return [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'title.string' => 'Tiêu đề phải là một chuỗi.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'body.required' => 'Vui lòng nhập nội dung.',
            'send_at.required' => 'Vui lòng chọn thời gian gửi.',
            'send_at.date' => 'Thời gian gửi phải là một ngày.',
            'send_at.after' => 'Thời gian gửi phải là sau ngày hiện tại.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'created_by.required' => 'Vui lòng chọn người tạo.',
            'created_by.exists' => 'Người tạo không hợp lệ.',
        ];
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e
    }
}
