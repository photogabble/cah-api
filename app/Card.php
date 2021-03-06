<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 'type', 'pick',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Deck
     */
    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }
}
