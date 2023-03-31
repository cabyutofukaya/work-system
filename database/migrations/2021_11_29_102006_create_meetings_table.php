<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->comment('議事録');
            $table->id();
            $table->datetime('started_at')->comment("開催日時");
            $table->text('title')->comment("会議名");
            $table->bigInteger('user_id')->comment('作成者ID')->unsigned();
            $table->text('participants')->comment("参加者")->nullable();
            $table->text('content')->comment("議事内容")->nullable();
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
        Schema::dropIfExists('meetings');
    }
}
