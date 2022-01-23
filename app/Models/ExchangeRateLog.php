<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ExchangeRateLog extends Model
{
    /**
     * @var string
     */
    protected $table = 'exchange_rate_logs';

    protected $fillable = [
        'currencies_symbol',
        'price',
        'created_at'
    ];
}
