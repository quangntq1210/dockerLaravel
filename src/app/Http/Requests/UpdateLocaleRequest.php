<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocaleRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules(): array
    {
        return [
            'locale' => 'required|in:vi,en',
           
        ];
    }
}