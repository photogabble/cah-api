<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Card[]
     */
    public function cards()
    {
        return $this->hasMany(Card::class);
    }
}
