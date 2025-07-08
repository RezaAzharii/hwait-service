<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecommendationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title'         => 'required|string|max:255',
            'ticket'        => 'nullable|numeric|min:0',
            'food'          => 'nullable|numeric|min:0',
            'transport'     => 'nullable|numeric|min:0',
            'others'        => 'nullable|numeric|min:0',
            'location_name' => 'required|string|max:255',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'description'   => 'nullable|string'
        ];

        if ($this->method() === 'POST') {
            $rules['image_path'] = 'required|image|mimes:jpg,jpeg,png|max:2048';
        } else {
            $rules['image_path'] = 'nullable|image|mimes:jpg,jpeg,png|max:2048';
        }

        return $rules;
    }
}