<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_contents', function (Blueprint $table) {
            $table->comment('日報コンテンツ');
            $table->id();
            $table->bigInteger('report_id')->comment('日報ID')->unsigned();
            $table->string('type')->comment("日報タイプ(work|sales)");

            // type:workのみ入力される情報
            $table->string('title')->comment("仕事内容(workのみ)")->nullable();

            // type:salesのみ入力される情報
            $table->bigInteger('client_id')->comment('会社ID(salesのみ)')->unsigned()->nullable();
            $table->bigInteger('branch_id')->comment('営業所ID(salesのみ)')->unsigned()->nullable();
            $table->text('participants')->comment("面談者")->nullable();
            $table->bigInteger('sales_method_id')->comment('営業手段ID(salesのみ)')->unsigned()->nullable();

            $table->text('description')->comment("詳細")->nullable();
            $table->boolean('is_complaint')->comment("クレーム・トラブル")->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('report_id')->references('id')->on('reports');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('sales_method_id')->references('id')->on('sales_methods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_contents');
    }
}
