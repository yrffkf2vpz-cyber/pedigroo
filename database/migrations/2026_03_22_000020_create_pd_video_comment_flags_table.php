<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pd_video_comment_flags', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('comment_id');
            $table->unsignedBigInteger('user_id')->nullable(); // ki jelentette

            // spam / abusive / duplicate / bot / other
            $table->string('flag_type', 50);

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['comment_id'], 'idx_pd_comment_flags_comment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pd_video_comment_flags');
    }
};