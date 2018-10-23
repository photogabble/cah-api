<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->boolean('private')->default(false);
            $table->unsignedInteger("max_score")->default(8);
            $table->unsignedInteger("max_rounds")->default(10);
            $table->text('decks')->default(''); // comma delimited
            $table->unsignedInteger('created_by');

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game');
    }
}
