<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExchangeRateRetrivalReqeust;
use App\Models\ExchangeRate;
use Carbon\Carbon;


class ExchangeRateController
{

    public function index(ExchangeRateRetrivalReqeust $request)
    {

        $validated = $request->validated();

        if(!$validated['date']) {
            $validated['date'] = Carbon::now()->subDay()->toDateString();
        }



        $rate = $this->getExchangeRate($validated);

        // if no exchange rate exists for the given date + currency combo, try the day before.
        if(!$rate) {
            $validated['date'] = (new Carbon($validated['date']))->subDay();
            $rate = $this->getExchangeRate($validated);
        }

        return $rate;

    }

    public function getExchangeRate(array $data): ?ExchangeRate
    {
        return ExchangeRate::where('date', $data['date'])
            ->where('base_currency_id', $data['base_currency_id'])
            ->where('target_currency_id', $data['target_currency_id'])
            ->first();
    }

}
