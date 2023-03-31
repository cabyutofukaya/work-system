<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->comment('会社 営業所');
            $table->id();
            $table->bigInteger('client_id')->comment('会社ID')->unsigned();
            $table->string('name')->comment('営業所名');
            $table->string('postcode')->comment("郵便番号")->nullable();
            $table->string('prefecture')->comment("所在地 都道府県")->nullable();
            $table->string('address')->comment("所在地 市区町村以下")->nullable();
            $table->string('lat')->comment("緯度")->nullable();
            $table->string('lng')->comment("経度")->nullable();
            $table->string('tel')->comment("電話番号")->nullable();
            $table->string('fax')->comment("FAX番号")->nullable();
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
        Schema::dropIfExists('branches');
    }
}
