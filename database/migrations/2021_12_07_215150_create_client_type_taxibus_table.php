<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientTypeTaxibusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_type_taxibus', function (Blueprint $table) {
            $table->comment('会社 タクシー・バス会社固有情報');
            $table->id();
            $table->bigInteger('client_id')->comment('会社ID')->unsigned()->unique();
            $table->integer('membership_fee')->comment('会費')->nullable();
            $table->integer('fee_taxi_cab')->comment('手数料 タクシー CAB')->nullable();
            $table->integer('fee_taxi_tabinoashi')->comment('手数料 タクシー たびの足')->nullable();
            $table->integer('fee_bus_cab')->comment('手数料 バス CAB')->nullable();
            $table->integer('fee_bus_tabinoashi')->comment('手数料 バス たびの足')->nullable();
            $table->string('category')->comment("カテゴリー(taxi|bus|taxibus)")->nullable();
            $table->boolean('has_dr_sightseeing')->comment("観光DR")->nullable();
            $table->boolean('has_dr_female')->comment("女性DR")->nullable();
            $table->boolean('has_dr_language_english')->comment("外国語DR 英語")->nullable();
            $table->boolean('has_dr_language_chinese')->comment("外国語DR 中国語")->nullable();
            $table->boolean('has_dr_language_korean')->comment("外国語DR 韓国語")->nullable();
            $table->boolean('has_dr_language_other')->comment("外国語DR 他言語")->nullable();
            $table->boolean('has_wheelchair')->comment("車椅子")->nullable();
            $table->boolean('has_baby_seat')->comment("ベビーシート")->nullable();
            $table->boolean('has_child_seat')->comment("チャイルドシート")->nullable();
            $table->integer('fee_child_seat')->comment('チャイルドシート料金')->nullable();
            $table->boolean('has_junior_seat')->comment("ジュニアシート")->nullable();
            $table->integer('fee_junior_seat')->comment('ジュニアシート料金')->nullable();
            $table->boolean('is_bus_association_member')->comment("バス協加盟")->nullable();
            $table->boolean('has_safety_mark')->comment("セーフティーマーク")->nullable();
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
        Schema::dropIfExists('client_type_taxibus');
    }
}
