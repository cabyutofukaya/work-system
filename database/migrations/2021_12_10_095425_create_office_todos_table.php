<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeTodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_todos', function (Blueprint $table) {
            $table->comment('社内ToDoリスト');
            $table->id();
            $table->bigInteger('user_id')->comment('メンバーID')->unsigned();
            $table->datetime('scheduled_at')->comment("日時")->index();
            $table->text('title')->comment("タイトル");
            $table->text('description')->comment("メモ");
            $table->boolean('is_completed')->comment("対応済みかどうか")->index()->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_todos');
    }
}
