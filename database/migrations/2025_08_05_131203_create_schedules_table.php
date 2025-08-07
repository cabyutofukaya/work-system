<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id(); // id
            $table->string('title', 300)->nullable();
            $table->string('category', 20)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->string('start_time', 10)->nullable();
            $table->string('end_time', 10)->nullable();
            $table->boolean('all_day')->default(0);
            $table->text('content')->nullable();
            $table->boolean('is_public')->default(1);
            $table->string('notification', 255)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
