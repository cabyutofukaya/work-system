<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLatestEvaluationsTable extends Migration
{
    /**
     * 最新の日報に設定されたの会社・商材ごとの評価
     * 評価の変更時にイベントにより自動的に生成される
     *
     * @return void
     */
    public function up()
    {
        Schema::create('latest_evaluations', function (Blueprint $table) {
            $table->comment('会社・商材ごと最新評価');
            $table->id();
            // ナチュラルキー
            $table->bigInteger('client_id')->comment('会社ID')->unsigned();
            $table->bigInteger('product_id')->comment('商材ID')->unsigned();
            $table->index(['client_id', 'product_id']);
            // 評価
            $table->bigInteger('evaluation_id')->comment('評価ID')->unsigned();
            // 評価元の日報
            $table->bigInteger('report_content_id')->comment('日報コンテンツID')->unsigned();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('evaluation_id')->references('id')->on('evaluations');
            $table->foreign('report_content_id')->references('id')->on('report_contents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('latest_evaluations');
    }
}
