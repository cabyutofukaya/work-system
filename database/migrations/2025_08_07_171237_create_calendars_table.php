<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalendarsTable extends Migration
{
    public function up(): void
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');          // 作成者
            $table->string('title');                         // タイトル
            $table->text('content')->nullable();            // 詳細
            $table->string('category')->nullable();         // カテゴリ（営業/会議など）
            $table->date('date');                           // 日付
            $table->boolean('all_day')->default(true);      // 終日かどうか
            $table->boolean('is_public')->default(true);    // 公開・非公開（任意）

            $table->timestamps();
            $table->softDeletes(); // deleted_at

            // 外部キー制約
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendars');
    }
}
