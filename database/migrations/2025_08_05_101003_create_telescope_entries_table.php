<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('telescope_entries', function (Blueprint $table) {
            $table->bigIncrements('sequence');
            $table->char('uuid', 36);
            $table->char('batch_id', 36);
            $table->string('family_hash', 255)->nullable();
            $table->boolean('should_display_on_index')->default(1);
            $table->string('type', 20);
            $table->longText('content');
            $table->dateTime('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telescope_entries');
    }
};
