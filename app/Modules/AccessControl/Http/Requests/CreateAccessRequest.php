<?php

namespace App\Modules\AccessControl\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // guest nem küldhet API-ból
    }

    public function rules(): array
    {
        return [
            'kennel_id' => 'required|integer|exists:kennels,id',
            'dog_id' => 'nullable|integer|exists:dogs,id',
            'request_type' => 'required|string|in:view_details,view_pedigree,view_litter,view_private_photos',
            'message' => 'nullable|string|max:500',
        ];
    }
}