<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGenresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->comment('ジャンル');
            $table->id();
            $table->string('client_type_id')->comment("会社タイプ");
            $table->string('name')->comment('ジャンル名');
            $table->timestamps();
        });

        foreach (["ハイヤー・リムジン", "観光", "一般", "福祉", "路線", "高速バス・ツアーバス", "一般貸切", "ロケバス"] as $genre) {
            DB::table('genres')->insert([
                'client_type_id' => 'taxibus',
                'name' => $genre,
            ]);
        }

        foreach (["生コン", "産廃", "引越し", "軽貨物", "ダンプ", "建築・建設・土木", "重機", "クレーン", "食品・飲料", "工業機械", "海運コンテナ", "鉄道コンテナ", "車両運搬", "液体運送", "農産品", "水産品", "日配品"] as $genre) {
            DB::table('genres')->insert([
                'client_type_id' => 'truck',
                'name' => $genre,
            ]);
        }

        foreach (["チェーン", "多店舗展開", "個人経営", "フランチャイズ", "その他"] as $genre) {
            DB::table('genres')->insert([
                'client_type_id' => 'restaurant',
                'name' => $genre,
            ]);
        }

        foreach (["総合", "海外", "国内", "WEB", "インバウンド", "インハウス", "観光協会など"] as $genre) {
            DB::table('genres')->insert([
                'client_type_id' => 'travel',
                'name' => $genre,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genres');
    }
}
