<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorLog;
use Carbon\Carbon;

class SensorLogController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'H2S' => 'required|numeric',
            'NH3' => 'required|numeric',
            'Temperature' => 'required|numeric',
            'Humidity' => 'required|numeric',
            'PM1' => 'required|numeric',
            'PM25' => 'required|numeric',
            'PM10' => 'required|numeric',
            'Status' => 'nullable|string',
        ]);

        $log = SensorLog::create([
            'h2s' => $validated['H2S'],
            'nh3' => $validated['NH3'],
            'temp' => $validated['Temperature'],
            'hum' => $validated['Humidity'],
            'press' => 1012.5, // Default pressure as not provided
            'pm1' => $validated['PM1'],
            'pm25' => $validated['PM25'],
            'pm10' => $validated['PM10'],
            'status' => $validated['Status'] ?? null,
        ]);

        return response()->json(['message' => 'Data saved successfully', 'data' => $log], 201);
    }

    public function history(Request $request)
    {
        // Ambil data hari ini saja
        $logs = SensorLog::whereDate('created_at', Carbon::today())->orderBy('created_at', 'asc')->get();
        
        $history = [
            'pagi' => ['labels' => [], 'data' => ['nh3' => [], 'h2s' => [], 'temp' => [], 'hum' => [], 'pm25' => [], 'pm10' => []]],
            'siang' => ['labels' => [], 'data' => ['nh3' => [], 'h2s' => [], 'temp' => [], 'hum' => [], 'pm25' => [], 'pm10' => []]],
            'sore' => ['labels' => [], 'data' => ['nh3' => [], 'h2s' => [], 'temp' => [], 'hum' => [], 'pm25' => [], 'pm10' => []]],
            'malam' => ['labels' => [], 'data' => ['nh3' => [], 'h2s' => [], 'temp' => [], 'hum' => [], 'pm25' => [], 'pm10' => []]],
        ];

        foreach ($logs as $log) {
            $hour = (int) $log->created_at->format('H');
            $time = $log->created_at->format('H:i');
            
            $period = '';
            if ($hour >= 6 && $hour < 12) {
                $period = 'pagi';
            } elseif ($hour >= 12 && $hour < 18) {
                $period = 'siang';
            } elseif ($hour >= 18 && $hour <= 23) {
                $period = 'sore';
            } else {
                $period = 'malam'; // 00 - 05
            }

            $history[$period]['labels'][] = $time;
            $history[$period]['data']['nh3'][] = $log->nh3;
            $history[$period]['data']['h2s'][] = $log->h2s;
            $history[$period]['data']['temp'][] = $log->temp;
            $history[$period]['data']['hum'][] = $log->hum;
            $history[$period]['data']['pm25'][] = $log->pm25;
            $history[$period]['data']['pm10'][] = $log->pm10;
        }

        return response()->json($history);
    }
}
