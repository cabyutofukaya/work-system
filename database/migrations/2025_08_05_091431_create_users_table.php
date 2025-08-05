<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // bigint unsigned AUTO_INCREMENT
            $table->string('username', 255)->unique(); // ログイン用ユーザー名
            $table->string('name', 255); // 氏名
            $table->string('email', 255)->unique(); // メールアドレス
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255); // ハッシュ済みパスワード
            $table->string('tel', 255)->nullable()->comment('電話番号');
            $table->string('department', 255)->nullable()->comment('所属部署');
            $table->rememberToken(); // varchar(100)
            $table->timestamp('login_at')->nullable(); // 最終ログイン時刻
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
