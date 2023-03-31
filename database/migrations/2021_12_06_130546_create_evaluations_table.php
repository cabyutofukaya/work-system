<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->comment('評価');
            $table->id()->comment('評価ID');
            $table->string('grade')->comment('評価')->unique();
            $table->string('label')->comment('ラベル')->unique();
            $table->timestamps();
        });

        DB::table('evaluations')->insert([
            [
                'id' => 1,
                'grade' => 'S',
                'label' => '導入済み',
            ], [
                'id' => 2,
                'grade' => 'A',
                'name' => '契約予定',
            ], [
                'id' => 3,
                'grade' => 'B',
                'name' => '見積り提出済み',
            ], [
                'id' => 4,
                'grade' => 'C',
                'name' => '見込あり',
            ], [
                'id' => 5,
                'grade' => 'D',
                'name' => '不採用・NG',
            ], [
                'id' => 6,
                'grade' => 'E',
                'name' => '不在・ポスティング',
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
        Schema::dropIfExists('evaluations');
    }
}
