<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_breeds', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 191);
            $table->string('normalized_name', 191)->nullable()->index();

            $table->unsignedInteger('fci_id')->nullable();

            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->unsignedInteger('group_id')->nullable();
            $table->unsignedInteger('subgroup_id')->nullable();

            $table->unsignedInteger('origin_country')->nullable();

            $table->string('size_category', 50)->nullable();
            $table->string('coat_type', 100)->nullable();
            $table->string('usage_category', 255)->nullable();

            $table->string('fci_standard_url', 500)->nullable();

            $table->enum('status', ['stable', 'endangered', 'extinct'])->default('stable');

            $table->string('recognition_type', 50)->default('fci');

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_breeds');
    }
};
