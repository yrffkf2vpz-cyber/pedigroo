<?php

namespace App\Validation\Breeding;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BuyerAccessDecisionValidator
{
    public function validate(array $data): array
    {
        $rules = [
            'decision' => ['required', 'in:approved,rejected'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}