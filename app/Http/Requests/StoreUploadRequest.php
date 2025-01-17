<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        // Check if the user is authenticated
            //return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|image|mimes:jpg,jpeg,png|max:10240',
        ];
    }

     /**
     * Custom error messages.
     */
    public function messages()
    {
        return [
            'file.required' => 'Please upload a file.',
            'file.image' => 'The file must be an image (jpg, jpeg, png).',
            'file.mimes' => 'Allowed file types are: jpg, jpeg, png.',
            'file.max' => 'The file size should not exceed 10MB.',
        ];
    }

}
