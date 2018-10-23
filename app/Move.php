<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Move extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function whiteCards()
    {
        return $this->belongsToMany(Card::class, 'moves_cards');
    }
}
