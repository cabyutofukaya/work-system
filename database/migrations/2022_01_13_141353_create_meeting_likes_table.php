<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_likes', function (Blueprint $table) {
            $table->comment('議事録いいね');
            $table->id();
            $table->bigInteger('meeting_id')->comment('議事録ID')->unsigned();
            $table->bigInteger('user_id')->comment('メンバーID')->unsigned();

            $table->timestamps();

            $table->foreign('meeting_id')->references('id')->on('meetings');
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
        Schema::dropIfExists('meeting_likes');
    }
}
