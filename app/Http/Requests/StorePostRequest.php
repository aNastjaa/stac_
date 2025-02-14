<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePostRequest extends FormRequest
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
    public function rules()
    {
        return [
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:10240', // Max file size 10MB
            'description' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'An image is required for artwork submission.',
            'image.file' => 'The uploaded file must be a valid image.',
            'image.mimes' => 'Only JPEG, PNG, JPG, and GIF formats are allowed.',
            'image.max' => 'The image size must not exceed 10MB.',
            'description.required' => 'The description is required.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
    }
}
