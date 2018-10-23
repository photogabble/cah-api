<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_users', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('game_id');
            $table->timestamps();

            $table->boolean('online')->default(false);
            $table->timestamp('last_active')->nullable()->default(null);

            $table->primary(['user_id', 'game_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games_users');
    }
}
