<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notice_visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notice_id')->constrained('notices')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['notice_id', 'user_id']); // 重複防止
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notice_visitors');
    }
};
