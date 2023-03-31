<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_visitors', function (Blueprint $table) {
            $table->comment('日報閲覧者');
            $table->id();
            $table->bigInteger('report_id')->comment('日報ID')->unsigned();
            $table->bigInteger('user_id')->comment('メンバーID')->unsigned();

            $table->timestamps();

            $table->unique(['report_id', 'user_id']);

            $table->foreign('report_id')->references('id')->on('reports');
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
        Schema::dropIfExists('report_visitors');
    }
}
