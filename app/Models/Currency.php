<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{

    protected $guarded = [];
    protected $keyType = 'string';

    public function exchangeRates(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'base_currency_id');
    }

    public function targetRates(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'target_currency_id');
    }


}
