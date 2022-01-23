<?php

namespace App\Listeners;

use App\Events\BitfinexTickerDataReceived;
use App\Models\ExchangeRateLog;

class SaveBitfinexTickerDataToDb
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\BitfinexTickerDataReceived  $event
     * @return void
     */
    public function handle(BitfinexTickerDataReceived $event): void
    {
        $tickerData = $event->getBitfinexTickerData();

        ExchangeRateLog::create([
            'currencies_symbol' => $event->getCurrenciesSymbol(),
            'price' => $tickerData['last_price'],
            'created_at' => $tickerData['timestamp'],
        ]);
    }
}
