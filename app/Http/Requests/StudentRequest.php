<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'student_id' => 'required|unique:students,student_id',
            'name'       => 'required|string|max:255',
            'college'    => 'required|string',
            'city'       => 'required|string',
            'state'      => 'required|string',
            'email'      => 'required|email|unique:students,email',
            'gpa'        => 'required|numeric|min:0|max:10',
        ];
    }

    public function messages()
    {
        return [
            'student_id.required' => 'Student ID required!',
            'email.unique'        => 'This image is already exist',
            'gpa.max'             => 'GPA is no more than 10 ',
        ];
    }
}