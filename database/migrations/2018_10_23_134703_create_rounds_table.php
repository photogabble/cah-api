<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rounds', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('game_id');
            $table->unsignedInteger('black_card_id');
            $table->unsignedInteger('card_czar_id');
            $table->unsignedInteger('round_number')->default(1);

            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
            $table->foreign('black_card_id')->references('id')->on('cards')->onDelete('cascade');
            $table->foreign('card_czar_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rounds');
    }
}
