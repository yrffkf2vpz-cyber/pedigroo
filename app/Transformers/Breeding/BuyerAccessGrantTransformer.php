<?php

namespace App\Transformers\Breeding;

use App\Models\Breeding\BuyerAccessGrant;

class BuyerAccessGrantTransformer
{
    public function transform(BuyerAccessGrant $model): array
    {
        return [
            'id' => $model->id,
            'request_id' => $model->request_id,

            'buyer' => [
                'id' => $model->buyer_id,
                'name' => $model->buyer?->name,
            ],
            'dog' => [
                'id' => $model->dog_id,
                'name' => $model->dog?->name,
            ],
            'kennel' => [
                'id' => $model->kennel_id,
                'name' => $model->kennel?->name,
            ],

            'expires_at' => $model->expires_at?->toIso8601String(),
            'is_expired' => $model->isExpired(),

            'created_at' => $model->created_at?->toIso8601String(),
            'updated_at' => $model->updated_at?->toIso8601String(),
        ];
    }
}