<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientTypeRestaurantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_type_restaurant', function (Blueprint $table) {
            $table->comment('会社 飲食店固有情報');
            $table->id();
            $table->bigInteger('client_id')->comment('会社ID')->unsigned()->unique();
            $table->json('languages')->comment("言語リスト")->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('client_type_restaurant');
    }
}
