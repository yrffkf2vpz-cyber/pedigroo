<?php

namespace App\Transformers\Breeding;

use App\Models\Breeding\BuyerAccessRequest;

class BuyerAccessRequestTransformer
{
    public function transform(BuyerAccessRequest $model): array
    {
        return [
            'id' => $model->id,
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

            'purpose' => $model->purpose,
            'message' => $model->message,
            'status' => $model->status,

            'is_pending' => $model->isPending(),
            'is_approved' => $model->isApproved(),
            'is_rejected' => $model->isRejected(),

            'created_at' => $model->created_at?->toIso8601String(),
            'updated_at' => $model->updated_at?->toIso8601String(),
        ];
    }
}