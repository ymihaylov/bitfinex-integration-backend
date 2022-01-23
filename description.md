# Bitfinex integration -  Backend. Technical description.
## Environment details
- Developed with PHP 8.1.2 and MySQL 8.0.27
- Laravel v8.80.8
- Used Laravel command `php artisan server` for Web server and local installation for MySQL server (My Laptop is a bit oldish and slow to run Docker)
- Used PHPStorm 2021.2.3
- On MacBook Air Late 2013 with macOS Big Sur version 11.6

## Database 
- To get a clear idea about database structure see db migrations dir - ``database/migrations`` 
- There are two tables
  - ``exchange_rate_logs`` - as the name suggests, there are history of exchange rates
  - ``notification_settings`` - there are kept notification settings for exchange range prices

## Integration process explained
- To fetch the data from the [Bitfinex Ticker endpoint](https://docs.bitfinex.com/v1/reference#rest-public-ticker), I created the following command
```
\App\Console\Commands\FetchBitfinexTickerData
```
The idea of this command is to use for `cronjob` and to be executed at certain time interval. 
- If everything is fine with API call and response data, the command dispatch the following event with data from the Bitfinex API response
```
\App\Events\BitfinexTickerDataReceived
```
- There are two event listeners for this event 
  - ``\App\Events\BitfinexTickerDataReceived`` which will transform response raw data to Eloquent ``\App\Models\ExchangeRateLog`` entity
  - ``\App\Listeners\NotifySubscribedEmails`` which will notify by email every subscriber for new price if it meets the new price conditions 

## Application API explained
There are two API endpoints:
- GET ``/api/get-last-exchange-rates/{currencies_symbol}`` - this endpoint will return the history of exchange rates by currencies_symbol for last 8 months
```
curl -H "Content-Type: application/json" http://localhost:8000/api/get-last-exchange-rates/btcusd
```
- POST ``api/subscribe-for-notification`` with example POST data:
```json
{
   "email":"test@bittyfinny.com",
   "currencies_symbol":"btcusd",
   "notification_price":"40000"
}
```
```bash
curl -X POST -H "Content-Type: application/json" \                                                
    -d '{"email": "test@bittyfinny.com", "currencies_symbol": "btcusd", "notification_price": "40001"}' \
    http://localhost:8000/api/subscribe-for-notification
```
This endpoint will create a notification setting for an email ``test@bittyfinny.com``, exchange rate for BTC to USD, and notification price 40000. ``\App\Listeners\NotifySubscribedEmails`` will notify by email when BTC -> USD exchange price exceeds 40000.
- For more clarity, see ```\App\Http\Controllers\ExchangeRateLogController```
