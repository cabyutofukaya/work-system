<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientTypeTravelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_type_travel', function (Blueprint $table) {
            $table->comment('会社 旅行業社固有情報');
            $table->id();
            $table->bigInteger('client_id')->comment('会社ID')->unsigned()->unique();
            $table->string('payment_method')->comment("支払い方法")->nullable();
            $table->string('registration_number')->comment("旅行業登録番号")->nullable();
            $table->string('group')->comment("JATA/ANTA/その他")->nullable();
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
        Schema::dropIfExists('client_type_travel');
    }
}
