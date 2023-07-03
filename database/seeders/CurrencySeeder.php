<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::create([
            'id' => 'EUR',
            'name' => 'Euros',
        ]);


        Currency::create([
            'id' => 'USD',
            'name' => 'US Dollar',
        ]);
        Currency::create([
            'id' => 'JPY',
            'name' => 'Japanese Yen',
        ]);
        Currency::create([
            'id' => 'BGN',
            'name' => 'Bulgarian Lev',
        ]);
        Currency::create([
            'id' => 'CZK',
            'name' => 'Czech Republic Koruna',
        ]);
        Currency::create([
            'id' => 'DKK',
            'name' => 'Danish Krone',
        ]);
        Currency::create([
            'id' => 'GBP',
            'name' => 'British Pound Sterling',
        ]);
        Currency::create([
            'id' => 'HUF',
            'name' => 'Hungarian Forint',
        ]);
        Currency::create([
            'id' => 'PLN',
            'name' => 'Polish Zloty',
        ]);
        Currency::create([
            'id' => 'RON',
            'name' => 'Romanian Leu',
        ]);
        Currency::create([
            'id' => 'SEK',
            'name' => 'Swedish Krona',
        ]);
        Currency::create([
            'id' => 'CHF',
            'name' => 'Swiss Franc',
        ]);
        Currency::create([
            'id' => 'ISK',
            'name' => 'Icelandic Króna',
        ]);
        Currency::create([
            'id' => 'NOK',
            'name' => 'Norwegian Krone',
        ]);
        Currency::create([
            'id' => 'HRK',
            'name' => 'Croatian Kuna',
        ]);
        Currency::create([
            'id' => 'RUB',
            'name' => 'Russian Ruble',
        ]);
        Currency::create([
            'id' => 'TRY',
            'name' => 'Turkish Lira',
        ]);
        Currency::create([
            'id' => 'AUD',
            'name' => 'Australian Dollar',
        ]);
        Currency::create([
            'id' => 'BRL',
            'name' => 'Brazilian Real',
        ]);
        Currency::create([
            'id' => 'CAD',
            'name' => 'Canadian Dollar',
        ]);
        Currency::create([
            'id' => 'CNY',
            'name' => 'Chinese Yuan',
        ]);
        Currency::create([
            'id' => 'HKD',
            'name' => 'Hong Kong Dollar',
        ]);
        Currency::create([
            'id' => 'IDR',
            'name' => 'Indonesian Rupiah',
        ]);
        Currency::create([
            'id' => 'ILS',
            'name' => 'Israeli New Sheqel',
        ]);
        Currency::create([
            'id' => 'INR',
            'name' => 'Indian Rupee',
        ]);
        Currency::create([
            'id' => 'KRW',
            'name' => 'South Korean Won',
        ]);
        Currency::create([
            'id' => 'MXN',
            'name' => 'Mexican Peso',
        ]);
        Currency::create([
            'id' => 'MYR',
            'name' => 'Malaysian Ringgit',
        ]);
        Currency::create([
            'id' => 'NZD',
            'name' => 'New Zealand Dollar',
        ]);
        Currency::create([
            'id' => 'PHP',
            'name' => 'Philippine Peso',
        ]);
        Currency::create([
            'id' => 'SGD',
            'name' => 'Singapore Dollar',
        ]);
        Currency::create([
            'id' => 'THB',
            'name' => 'Thai Baht',
        ]);
        Currency::create([
            'id' => 'ZAR',
            'name' => 'South African Rand',
        ]);


    }
}
