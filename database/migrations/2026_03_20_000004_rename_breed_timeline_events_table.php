<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('breed_timeline_events', 'pd_breed_timeline');
    }

    public function down(): void
    {
        Schema::rename('pd_breed_timeline', 'breed_timeline_events');
    }
};