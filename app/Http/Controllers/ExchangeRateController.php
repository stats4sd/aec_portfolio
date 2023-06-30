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



        return ExchangeRate::where('date', $validated['date'])
            ->where('base_currency_id', $validated['base_currency_id'])
            ->where('target_currency_id', $validated['target_currency_id'])
            ->first();



    }

}
