<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notice_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notice_id')->constrained('notices')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_extension', 10);
            $table->string('type');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notice_files');
    }
};
