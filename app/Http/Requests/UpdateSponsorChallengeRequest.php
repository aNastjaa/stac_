<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdateSponsorChallengeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        Log::info('Authorize method called', ['user_id' => Auth::id()]);
        return Auth::user()->role->name === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'brief' => 'nullable|string',
            'brand_name' => 'nullable|string|max:255',
            'brand_logo_id' => 'nullable|uuid|exists:uploads,id',
            'submission_deadline' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }
}
