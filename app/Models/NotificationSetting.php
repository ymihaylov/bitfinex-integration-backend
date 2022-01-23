<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    /**
     * @var string
     */
    protected $table = 'notification_settings';

    protected $fillable = [
        'email',
        'currencies_symbol',
        'notification_price',
    ];
}
