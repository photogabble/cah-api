<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovesCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moves_cards', function (Blueprint $table) {
            $table->unsignedInteger('move_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();
            $table->unsignedInteger('order')->default(0);

            $table->primary(['user_id', 'move_id']);
            $table->foreign('move_id')->references('id')->on('moves')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moves_cards');
    }
}
