<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BitfinexTickerDataReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param array $bitfinexTickerData
     * @param string $currenciesSymbol
     */
    public function __construct(
        private array $bitfinexTickerData,
        private string $currenciesSymbol,
    ) {
    }

    /**
     * @return array
     */
    public function getBitfinexTickerData(): array
    {
        return $this->bitfinexTickerData;
    }

    /**
     * @return string
     */
    public function getCurrenciesSymbol(): string
    {
        return $this->currenciesSymbol;
    }
}
