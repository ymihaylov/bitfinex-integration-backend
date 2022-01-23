<?php

namespace App\Listeners;

use App\Events\BitfinexTickerDataReceived;
use App\Mail\NotifyForExchangeRatePrice;
use App\Models\NotificationSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

class NotifySubscribedEmails
{
    /**
     * Handle the event.
     *
     * @param BitfinexTickerDataReceived $event
     * @return void
     */
    public function handle(BitfinexTickerDataReceived $event): void
    {
        $tickerData = $event->getBitfinexTickerData();
        $currenciesSymbol = $event->getCurrenciesSymbol();

        /** @var Collection $notificationSetting */
        $notificationSetting = NotificationSetting::where('currencies_symbol', $currenciesSymbol)
            ->where('notification_price', '<=', $tickerData['last_price'])
            ->get();

        $emails = $notificationSetting->pluck('email');

        $emailData = array_merge($tickerData, ['currencies_symbol' => $currenciesSymbol]);

        return;

        // @TODO Not tested
        $emails->map(function (string $email) use ($emailData) {
            Mail::to($email)->send(new NotifyForExchangeRatePrice($emailData));
        });
    }
}
