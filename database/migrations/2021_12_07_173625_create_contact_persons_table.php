<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactPersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_persons', function (Blueprint $table) {
            $table->comment('相手先担当者');
            $table->id();
            $table->bigInteger('client_id')->comment('会社ID')->unsigned();
            $table->string('name')->comment('相手先担当者名');
            $table->string('email')->comment("メールアドレス")->nullable();
            $table->string('tel')->comment("電話番号")->nullable();
            $table->string('department')->comment("所属部署")->nullable();
            $table->string('position')->comment("役職")->nullable();
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
        Schema::dropIfExists('contact_persons');
    }
}
