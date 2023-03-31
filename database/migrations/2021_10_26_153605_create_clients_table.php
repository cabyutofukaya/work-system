<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->comment('会社');
            $table->id()->comment("会社ID");
            $table->text('image')->comment("画像パス")->nullable();
            $table->string('client_type_id')->comment("会社タイプ");
            $table->string('name')->comment("会社名");
            $table->string('name_kana')->comment("会社名よみがな")->nullable();
            $table->string('postcode')->comment("郵便番号")->nullable();
            $table->string('prefecture')->comment("所在地 都道府県")->nullable();
            $table->string('address')->comment("所在地 市区町村以下")->nullable();
            $table->string('lat')->comment("緯度")->nullable();
            $table->string('lng')->comment("経度")->nullable();
            $table->text('url')->comment("URL")->nullable();
            $table->string('email')->comment("メールアドレス")->nullable();
            $table->string('representative')->comment("代表者名")->nullable();
            $table->string('tel')->comment("電話番号")->nullable();
            $table->string('fax')->comment("FAX番号")->nullable();
            $table->string('business_hours')->comment("営業時間")->nullable();
            $table->text('description')->comment("説明")->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
