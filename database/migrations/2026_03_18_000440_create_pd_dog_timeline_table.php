<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_dog_timeline', function (Blueprint $table) {
            $table->bigIncrements('id');

            // A timeline univerzális, ezért polymorphic:
            $table->unsignedBigInteger('entity_id');      // dog, kennel, breed, club, event, country
            $table->string('entity_type', 100);           // 'dog', 'kennel', 'breed', 'club', 'event', 'country'

            $table->unsignedBigInteger('event_type_id')->nullable(); // event_types tábla
            $table->date('occurred_at')->nullable();

            $table->string('title', 255)->nullable();     // rövid cím
            $table->text('description')->nullable();      // részletes leírás

            $table->json('meta')->nullable();             // extra adatok (pl. pontszám, eredmény, helyszín)

            $table->timestamps();

            $table->index(['entity_id', 'entity_type'], 'idx_pd_timeline_entity');
            $table->index(['event_type_id'], 'idx_pd_timeline_event_type');
            $table->index(['occurred_at'], 'idx_pd_timeline_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_dog_timeline');
    }
};