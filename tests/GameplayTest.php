<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * Class GameplayTest
 *
 * TDD: Tests should account for the following:
 *
 * + Players should be able to create games and define which card decks they want to use and the max points for a game
 *   to be won, max number of players will be set as a function of the number of white cards available.
 * + Each 'round' will timeout after 60 seconds, maybe make this a configurable number when setting up the game?
 * + Players can leave and join a game, however if there are less than three players the game switches to a 'hold' state
 *   because you can't play with less than three people.
 * + If someone leaves a game and joins before that game is won it should remember their score, but they should get fresh
 *   cards. (Or maybe it remembers their cards for a length of time before returning them to the deck?)
 * + Cards should be chosen at random and once used placed in a do-not-use list until all the available cards have
 *   been played. One of the most annoying parts of some other digital versions is how often cards would appear
 *   after being played.
 * + Games should be able to be started as public or private, private games will be protected by a simple pass-word
 * + Games should have invite links available so someone can set up a game and then invite friends to play
 * + All games histories should be available in a public log
 */
class GameplayTest extends TestCase
{

    public function testStartAPublicGame()
    {

    }

    public function testStartAPrivateGame()
    {

    }

    public function testInviteLinks()
    {

    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }
}
