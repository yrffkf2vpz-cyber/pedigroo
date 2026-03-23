<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_inquiries', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ki küldte
            $table->unsignedBigInteger('user_id');

            // melyik kennelnek
            $table->unsignedBigInteger('kennel_id');

            // mire vonatkozik
            $table->unsignedBigInteger('litter_id')->nullable();
            $table->unsignedBigInteger('dog_id')->nullable();

            // üzenet
            $table->text('message');

            // státusz: new / replied / closed
            $table->string('status', 20)->default('new');

            // kennel válasza
            $table->text('reply')->nullable();
            $table->timestamp('replied_at')->nullable();

            $table->timestamps();

            $table->index(['user_id'], 'idx_pd_inq_user');
            $table->index(['kennel_id'], 'idx_pd_inq_kennel');
            $table->index(['litter_id'], 'idx_pd_inq_litter');
            $table->index(['dog_id'], 'idx_pd_inq_dog');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_inquiries');
    }
};