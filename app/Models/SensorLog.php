<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorLog extends Model
{
    protected $fillable = [
        'nh3', 'h2s', 'temp', 'hum', 'press', 'pm1', 'pm25', 'pm10', 'status'
    ];
}
