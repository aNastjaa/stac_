<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreSponsorChallengeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'brief' => 'required|string',
            'brand_name' => 'required|string|max:255',
            'brand_logo_id' => 'required|uuid|exists:uploads,id',
            'submission_deadline' => 'required|date',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('submission_deadline')) {
            // Convert the submission_deadline to the desired format
            $this->merge([
                'submission_deadline' => Carbon::parse($this->submission_deadline)->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
