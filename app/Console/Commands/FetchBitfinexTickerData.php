<?php

namespace App\Console\Commands;

use App\Events\BitfinexTickerDataReceived;
use Exception;
use http\Client\Response;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * @see https://docs.bitfinex.com/v1/reference#rest-public-ticker
 */
class FetchBitfinexTickerData extends Command
{
    // @TODO The following constants can be moved to config file
    private const BITFINEX_API_URL = "https://api.bitfinex.com/v1/";

    private const BITFINEX_API_ENDPOINT = "pubticker";

    private const BITFINEX_TARGET_SYMBOL = "btcusd";

   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitfinex:fetch-ticker-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch ticker data from bitfinex public API. See https://docs.bitfinex.com/v1/reference#rest-public-ticker for more info.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $targetSymbol = self::BITFINEX_TARGET_SYMBOL;

        try {
            /** @var Response $response */
            $response = Http::get(self::BITFINEX_API_URL.self::BITFINEX_API_ENDPOINT."/".$targetSymbol);
        } catch (Exception $exception) {
            $this->error("Something went wrong on connecting to Bitfinex API.\n{$exception->getMessage()}");
            return -1;
        }

        $responseBody = json_decode($response->body(), true);

        if (!is_array($responseBody) || empty($responseBody['last_price'])) {
            $this->error("Response body from Bitfinex API is not valid!");
            return -1;
        }

        BitfinexTickerDataReceived::dispatch($responseBody, $targetSymbol);

        return 1;
    }
}
