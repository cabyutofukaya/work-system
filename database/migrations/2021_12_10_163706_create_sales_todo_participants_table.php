<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTodoParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_todo_participants', function (Blueprint $table) {
            $table->comment('営業ToDoリスト 担当メンバー');
            $table->id();
            $table->bigInteger('sales_todo_id')->comment('営業ToDoリストID')->unsigned();
            $table->bigInteger('user_id')->comment('担当メンバーID')->unsigned();
            $table->timestamps();

            $table->foreign('sales_todo_id')->references('id')->on('sales_todos');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unique(['sales_todo_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_todo_participants');
    }
}
