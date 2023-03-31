<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportContentLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_content_likes', function (Blueprint $table) {
            $table->comment('日報いいね');
            $table->id();
            $table->bigInteger('report_content_id')->comment('日報コンテンツID')->unsigned();
            $table->bigInteger('user_id')->comment('メンバーID')->unsigned();

            $table->timestamps();

            $table->foreign('report_content_id')->references('id')->on('report_contents');
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
        Schema::dropIfExists('report_content_likes');
    }
}
