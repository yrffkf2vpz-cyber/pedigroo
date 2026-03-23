<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_user_security_events', function (Blueprint $table) {
            $table->bigIncrements('id');

            // melyik felhasználóhoz tartozik az esemény
            $table->unsignedBigInteger('user_id');

            // esemény típusa (pl. failed_login, new_device, password_change)
            $table->string('event_type', 100);

            // opcionális polymorphic cél (pl. session, device)
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type', 100)->nullable();

            // extra adatok (pl. IP, ország, user-agent)
            $table->json('metadata')->nullable();

            // mikor történt
            $table->timestamp('occurred_at')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_sec_user');
            $table->index(['event_type'], 'idx_pd_sec_event_type');
            $table->index(['subject_id', 'subject_type'], 'idx_pd_sec_subject');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_user_security_events');
    }
};