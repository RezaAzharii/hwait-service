<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|regex:/^[a-zA-Z0-9_]+$/|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ];
    }
}