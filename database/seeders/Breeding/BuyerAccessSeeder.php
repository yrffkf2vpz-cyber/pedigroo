<?php

namespace Database\Seeders\Breeding;

use Illuminate\Database\Seeder;
use App\Models\Breeding\BuyerAccessRequest;
use App\Models\Breeding\BuyerAccessGrant;

class BuyerAccessSeeder extends Seeder
{
    public function run(): void
    {
        // 10 pending request
        BuyerAccessRequest::factory()
            ->count(10)
            ->create();

        // 10 approved request + grant
        BuyerAccessRequest::factory()
            ->count(10)
            ->approved()
            ->create()
            ->each(function ($request) {
                BuyerAccessGrant::factory()->create([
                    'request_id' => $request->id,
                    'buyer_id'   => $request->buyer_id,
                    'dog_id'     => $request->dog_id,
                    'kennel_id'  => $request->kennel_id,
                ]);
            });

        // 5 rejected request
        BuyerAccessRequest::factory()
            ->count(5)
            ->rejected()
            ->create();

        // 5 expired grants
        BuyerAccessRequest::factory()
            ->count(5)
            ->approved()
            ->create()
            ->each(function ($request) {
                BuyerAccessGrant::factory()
                    ->expired()
                    ->create([
                        'request_id' => $request->id,
                        'buyer_id'   => $request->buyer_id,
                        'dog_id'     => $request->dog_id,
                        'kennel_id'  => $request->kennel_id,
                    ]);
            });
    }
}