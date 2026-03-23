<?php

namespace App\Modules\AccessControl\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // késobb policy ellenorzi, hogy kennel owner-e
    }

    public function rules(): array
    {
        return [
            'allowed_fields' => 'required|array|min:1',
            'allowed_fields.*' => 'string|in:pedigree,health,litter_info,private_photos,private_videos,kennel_data',
            'expires_at' => 'nullable|date|after:now',
        ];
    }
}