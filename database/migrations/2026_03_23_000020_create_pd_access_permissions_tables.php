<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_access_permissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('granted_by_user_id'); // kennel owner

            $table->json('allowed_fields'); 
            // pl.: ["pedigree", "health", "private_photos"]

            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            // Indexek
            $table->index('request_id');
            $table->index('granted_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_access_permissions');
    }
};