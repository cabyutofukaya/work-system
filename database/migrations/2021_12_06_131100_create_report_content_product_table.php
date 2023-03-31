<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportContentProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_content_product', function (Blueprint $table) {
            $table->comment('日報・商材ごと評価');
            $table->id();
            $table->bigInteger('report_content_id')->comment('日報コンテンツID')->unsigned();
            $table->bigInteger('product_id')->comment('商材ID')->unsigned();
            $table->bigInteger('evaluation_id')->comment('評価ID')->unsigned();
            $table->timestamps();

            $table->foreign('report_content_id')->references('id')->on('report_contents');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('evaluation_id')->references('id')->on('evaluations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_content_product');
    }
}
