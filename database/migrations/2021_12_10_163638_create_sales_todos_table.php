<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_todos', function (Blueprint $table) {
            $table->comment('営業ToDoリスト 担当メンバー');
            $table->id();
            $table->bigInteger('user_id')->comment('メンバーID')->unsigned();
            $table->bigInteger('client_id')->comment('会社ID')->unsigned();
            $table->datetime('scheduled_at')->comment("日時")->index();
            $table->text('contact_person')->comment('相手先担当者名')->nullable();
            $table->text('description')->comment("要件");
            $table->boolean('is_completed')->comment("対応済みかどうか")->index()->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('sales_todos');
    }
}
