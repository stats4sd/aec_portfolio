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

        dd($validated['date']);

        return ExchangeRate::where('date', $validated['date'])
            ->where('base_currency_id', $validated['base_currency_id'])
            ->where('conversion_currency_id', $validated['conversion_currency_id'])
            ->first();



    }

}
