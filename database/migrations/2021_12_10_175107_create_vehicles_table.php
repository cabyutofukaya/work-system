<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->comment('会社保有車両');
            $table->id();
            $table->bigInteger('client_id')->comment('会社ID')->unsigned();
            $table->text('image')->comment("画像パス")->nullable();
            $table->string('type')->comment('車両タイプ(taxi|bus)');
            $table->string('name')->comment('車両名');
            $table->text('description')->comment("車両説明")->nullable();

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
        Schema::dropIfExists('vehicles');
    }
}
