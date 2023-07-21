<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestStudent extends FormRequest
{

    public function rules(): array
    {
        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email'],
            'phone_number' => ['required', 'min_digits:11', 'max_digits:11', 'numeric'],
            'school_name' => ['required'],
            'school_year' => ['required'],
            'address' => ['required'],
            'course' => ['required'],
            'remaining' => ['required', 'integer'],
            'position' => ['required', 'in:Web Developer,Graphic Designer,Video Editor'],
        ];
    }
}
