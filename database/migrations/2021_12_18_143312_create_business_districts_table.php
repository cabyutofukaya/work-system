<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_districts', function (Blueprint $table) {
            $table->comment('営業エリア');
            $table->id();
            $table->bigInteger('client_id')->comment('会社ID')->unsigned();
            $table->string('prefecture')->comment("都道府県");
            $table->string('address')->comment("市区町村以下")->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_districts');
    }
}
