<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSponsorSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->role->name === 'pro';
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

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'image.required' => 'The image file is required.',  // Fix this
            'image.file' => 'The image must be a valid file.',
            'image.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif.', // Remove 'pdf'
            'image.max' => 'The image may not be greater than 10MB.',
            'description.required' => 'The description is required.',
            'description.string' => 'The description must be a valid string.',
        ];
    }
}
