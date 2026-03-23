<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_puppy_waitlist', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ki iratkozott fel
            $table->unsignedBigInteger('user_id');

            // mire iratkozott fel
            $table->unsignedBigInteger('litter_id')->nullable();
            $table->unsignedBigInteger('puppy_id')->nullable();

            // státusz: pending / contacted / accepted / rejected
            $table->string('status', 20)->default('pending');

            // preferenciák (szín, nem, temperamentum)
            $table->json('preferences')->nullable();

            // mikor iratkozott fel
            $table->timestamp('joined_at')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_waitlist_user');
            $table->index(['litter_id'], 'idx_pd_waitlist_litter');
            $table->index(['puppy_id'], 'idx_pd_waitlist_puppy');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_puppy_waitlist');
    }
};