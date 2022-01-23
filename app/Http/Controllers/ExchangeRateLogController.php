<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRateLog;
use App\Models\NotificationSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

class ExchangeRateLogController extends Controller
{
    /**
     * @param string $currencySymbol
     * @return JsonResponse
     */
    public function getLastExchangeRates(string $currencySymbol): JsonResponse
    {
        $exchangeRateLogs = ExchangeRateLog::whereDate('created_at', '>=', Date::now()->subMonths(8))
            ->where('currencies_symbol', $currencySymbol)
            ->get();

        return response()->json([
            'currency_symbol' => $currencySymbol,
            'exchange_symbol' => $this->transformExchangeRatesCollectionToArray($exchangeRateLogs),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function subscribeForNotification(Request $request): JsonResponse
    {
        // @TODO Validation should be moved to Middleware or something
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'currencies_symbol' => 'required', // @TODO should be validated by data from this -> https://docs.bitfinex.com/v1/reference#rest-public-symbols
            'notification_price' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages(),
            ]);
        }

        // @TODO This should be in Repository Method
        /** @var Collection $notificationSetting */
        $notificationSetting = NotificationSetting::where('email', $request->get('email'))
            ->where('currencies_symbol', $request->get('currencies_symbol'))
            ->where('notification_price', $request->get('notification_price'))
            ->get();

        if ($notificationSetting->isNotEmpty()) {
            return response()->json([
                'errors' => [
                    'This notification setting already exists.',
                ],
            ]);
        }

        // @TODO Email should be encrypted somehow before saving it to DB.
        NotificationSetting::create($request->all());

        return response()->json([
            'success' => 1,
        ]);
    }

    /**
     * * @TODO This should be moved to separated service for transforming objects and/or serialization.
     * But for the purpose of the task I think this way is okayish.
     *
     * @param Collection $exchangeRateLogs
     * @return array
     */
    private function transformExchangeRatesCollectionToArray(Collection $exchangeRateLogs): array
    {
        return $exchangeRateLogs->map(function (ExchangeRateLog $exchangeRate) {
            return [
                'price' => $exchangeRate->price,
                'created_at' => $exchangeRate->created_at,
            ];
        })->toArray();
    }
}
