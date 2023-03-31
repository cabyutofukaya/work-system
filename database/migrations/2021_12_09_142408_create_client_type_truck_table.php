<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientTypeTruckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_type_truck', function (Blueprint $table) {
            $table->comment('会社 トラック会社固有情報');
            $table->id();
            $table->bigInteger('client_id')->comment('会社ID')->unsigned()->unique();
            $table->string('drivers_count')->comment("運転手数")->nullable();
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
        Schema::dropIfExists('client_type_truck');
    }
}
