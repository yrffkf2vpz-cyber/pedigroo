<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_aliases', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('test_type', 20);
            $table->string('alias', 255);
            $table->string('canonical', 50);
            $table->longText('countries');

            $table->string('created_at', 255)->nullable();
            $table->date('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_aliases');
    }
};
