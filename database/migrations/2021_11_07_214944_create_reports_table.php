<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->comment('日報');
            $table->id();
            $table->date('date')->comment('日付')->index();
            $table->bigInteger('user_id')->comment('メンバーID')->unsigned();
            $table->boolean('is_private')->comment("非公開かどうか")->index()->default(false);

            // コメント更新日時 モベルイベントにより自動的に設定される
            $table->datetime('comment_updated_at')->comment('コメント更新日時')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
