<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_litters', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('sire_id')->nullable();   // apa (kan)
            $table->unsignedBigInteger('dam_id')->nullable();    // anya (szuka)

            $table->unsignedBigInteger('breeder_id')->nullable();
            $table->unsignedBigInteger('kennel_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();

            $table->string('litter_code', 100)->nullable();      // belso/klub kód
            $table->date('whelped_at')->nullable();              // születés dátuma
            $table->date('registered_at')->nullable();           // regisztráció dátuma

            $table->unsignedInteger('total_puppies')->default(0);
            $table->unsignedInteger('male_puppies')->default(0);
            $table->unsignedInteger('female_puppies')->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['sire_id'], 'idx_pd_litters_sire');
            $table->index(['dam_id'], 'idx_pd_litters_dam');
            $table->index(['kennel_id'], 'idx_pd_litters_kennel');
            $table->index(['breeder_id'], 'idx_pd_litters_breeder');
            $table->index(['country_id'], 'idx_pd_litters_country');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_litters');
    }
};