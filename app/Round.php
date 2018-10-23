<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Round
 * @package App
 */
class Round extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['round_number'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function blackCard()
    {
        return $this->belongsTo(Card::class, 'black_card_id');
    }

    public function cardCzar()
    {
        return $this->belongsTo(User::class, 'card_czar_id');
    }
}
