<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pd_dog_timeline', function (Blueprint $table) {
            $table->foreign('event_type_id')
                ->references('id')->on('event_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pd_dog_timeline', function (Blueprint $table) {
            $table->dropForeign(['event_type_id']);
        });
    }
};