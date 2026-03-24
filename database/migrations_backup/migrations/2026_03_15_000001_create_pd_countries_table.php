<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_countries', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('code', 5)->unique();
            $table->string('name', 100)->unique();

            $table->string('date_format', 20);

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_countries');
    }
};
