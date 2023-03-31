<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->comment('商材');
            $table->id()->comment('商材ID');
            $table->string('name')->comment('商材名')->unique();
            $table->timestamps();
        });

        DB::table('products')->insert([
            [
                'id' => 1,
                'name' => 'バスキング',
            ], [
                'id' => 2,
                'name' => 'グッドラーニング！タクシー',
            ], [
                'id' => 3,
                'name' => 'グッドラーニング！バス',
            ], [
                'id' => 4,
                'name' => 'グッドラーニング！トラック',
            ], [
                'id' => 5,
                'name' => 'グッドラーニング！飲食',
            ], [
                'id' => 6,
                'name' => 'グッドラーニング！（初任者）',
            ], [
                'id' => 7,
                'name' => 'トラックキング',
            ], [
                'id' => 8,
                'name' => 'HAKKEN',
            ], [
                'id' => 9,
                'name' => 'BOON',
            ], [
                'id' => 10,
                'name' => 'Links',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
