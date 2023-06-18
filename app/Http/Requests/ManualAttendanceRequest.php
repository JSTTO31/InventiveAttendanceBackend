<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManualAttendanceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'time_in' => ['required', 'date'],
            'time_out' => ['required', 'date'],
            'option' => ['required', 'in:present,absent,policy'],
        ];
    }
}
