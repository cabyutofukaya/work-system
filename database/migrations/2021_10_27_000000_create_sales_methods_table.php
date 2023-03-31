<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('sales_methods', function (Blueprint $table) {
            $table->comment('営業手段');
            $table->id()->comment('営業手段ID');
            $table->string('name')->comment('営業手段名')->unique();
            $table->timestamps();
        });

        DB::table('sales_methods')->insert([
            [
                'id' => 1,
                'name' => '飛び込みセールス',
            ], [
                'id' => 2,
                'name' => '再訪問セールス',
            ], [
                'id' => 3,
                'name' => 'フォローセールス',
            ], [
                'id' => 4,
                'name' => 'インサイドセールス',
            ], [
                'id' => 5,
                'name' => 'webサイト',
            ], [
                'id' => 6,
                'name' => 'DM/メールマガジン',
            ], [
                'id' => 7,
                'name' => '既存契約紹介',
            ], [
                'id' => 8,
                'name' => 'その他',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_methods');
    }
};
