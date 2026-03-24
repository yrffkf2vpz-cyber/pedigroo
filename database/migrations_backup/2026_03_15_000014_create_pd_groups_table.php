<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_groups', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('group_number');
            $table->string('group_name', 255);

            $table->integer('section_number');
            $table->string('section_name', 255);

            $table->tinyInteger('working_test_required')->default(0);

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_groups');
    }
};
