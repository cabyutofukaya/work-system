<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeTodoParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_todo_participants', function (Blueprint $table) {
            $table->comment('社内ToDoリスト 担当メンバー');
            $table->id();
            $table->bigInteger('office_todo_id')->comment('社内ToDoリストID')->unsigned();
            $table->bigInteger('user_id')->comment('担当メンバーID')->unsigned();
            $table->timestamps();

            $table->foreign('office_todo_id')->references('id')->on('office_todos');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unique(['office_todo_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_todo_participants');
    }
}
