<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Game.
 *
 * - A game can only start with three or more player
 * - The maximum number of players is equal to the number of white cards
 *   available in the decks chosen for the game divided by nine.
 *
 * @package App
 */
class Game extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['target_score', 'private', 'decks'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function decks()
    {
        // @todo query that looks up decks based upon decks csv value
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Round[]
     */
    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|User[]
     */
    public function players()
    {
        return $this->belongsToMany(User::class, 'games_users');
    }
}
