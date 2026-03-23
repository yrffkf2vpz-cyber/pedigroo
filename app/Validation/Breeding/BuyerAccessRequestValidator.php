<?php

namespace App\Validation\Breeding;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BuyerAccessRequestValidator
{
    public function validate(array $data): array
    {
        $rules = [
            'buyer_id' => ['required', 'integer', 'exists:users,id'],
            'dog_id' => ['required', 'integer', 'exists:dogs,id'],
            'kennel_id' => ['required', 'integer', 'exists:kennels,id'],

            'purpose' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],

            'ip' => ['nullable', 'ip'],
            'device' => ['nullable', 'string', 'max:255'],
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}