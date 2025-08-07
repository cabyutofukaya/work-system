<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();

            $table->string('user_type')->nullable(); // モデルのクラス名（例: App\Models\User）
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('event'); // created, updated, deleted など

            $table->string('auditable_type'); // 監査対象モデルのクラス名
            $table->unsignedBigInteger('auditable_id');

            $table->text('old_values')->nullable(); // 変更前
            $table->text('new_values')->nullable(); // 変更後

            $table->text('url')->nullable();        // リクエストURL
            $table->string('ip_address', 45)->nullable(); // IPv6対応
            $table->string('user_agent', 1023)->nullable(); // ブラウザなどのUA文字列
            $table->string('tags')->nullable();     // 任意タグ

            $table->timestamps();

            // インデックス追加（必要に応じて）
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_type', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
