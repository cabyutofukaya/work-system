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
        Schema::create('todos', function (Blueprint $table) {
            $table->id(); // 主キー (BIGINT UNSIGNED AUTO_INCREMENT)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 外部キー（users.id）
            $table->string('title'); // タイトル
            $table->date('date'); // 日付（予定日）
            $table->boolean('is_done')->default(false);
            $table->timestamps(); // created_at / updated_at
            $table->softDeletes(); // deleted_at（ソフトデリート）
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
