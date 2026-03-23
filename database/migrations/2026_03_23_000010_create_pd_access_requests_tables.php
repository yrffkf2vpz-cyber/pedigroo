<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_access_requests', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('requester_user_id'); // guest vagy owner
            $table->unsignedBigInteger('kennel_id');
            $table->unsignedBigInteger('dog_id')->nullable();

            $table->string('request_type', 50); 
            // pl.: view_details, view_pedigree, view_litter, view_private_photos

            $table->text('message')->nullable();

            $table->enum('status', ['pending', 'approved', 'denied'])
                  ->default('pending');

            $table->timestamps();

            // Indexek
            $table->index('requester_user_id');
            $table->index('kennel_id');
            $table->index('dog_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_access_requests');
    }
};