<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileImageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'image' => ['mimes:png,jpg,jpeg', 'required']
        ];
    }
}
