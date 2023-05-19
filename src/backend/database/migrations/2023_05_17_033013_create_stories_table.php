<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('avatar')->nullable();
            $table->string('author')->nullable()->comment('Tac gia');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Người nhúng truyện');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('total_chapters')->nullable();
            $table->tinyInteger('type')->default(1)->comment('1: Đang ra, 2: Tạm dừng, 3: Hoàn thành');
            $table->text('content')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
