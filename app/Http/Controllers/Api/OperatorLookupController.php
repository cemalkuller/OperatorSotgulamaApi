<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Illuminate\Support\Arr;

class OperatorLookupController extends Controller
{
    // GET /api/operator-lookup?number=5XXXXXXXXX


    public function lookup(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Önce giriş yapmalısınız.'
            ], 401);
        }

        $dailyLimit = $user->daily_limit ?? 100;
        $userId = $user->id;
        $today = date('Y-m-d');
        $limitFile = storage_path('app/operator_limits.json');

        // Limit dosyasını oku veya başlat
        $limits = file_exists($limitFile) ? json_decode(file_get_contents($limitFile), true) : [];
        $usedCount = $limits[$today][$userId] ?? 0;

        // Numara listesi ayıklama
        $numbers = [];
        if ($request->has('numbers')) {
            $numbers = $request->input('numbers');
        } elseif ($request->has('number')) {
            $numbers = [$request->input('number')];
        } else {
            return response()->json([
                'success' => false,
                'message' => 'En az bir telefon numarası gerekli.'
            ], 422);
        }
        if (!is_array($numbers)) {
            $numbers = [$numbers];
        }

        // Limit kontrolü
        $remaining = $dailyLimit - $usedCount;
        if ($remaining <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Bugün sorgu limitinizi doldurdunuz.'
            ], 429);
        }
        $numbers = array_slice($numbers, 0, $remaining);

        $operatorNames = Operator::pluck('name', 'code')->toArray();

        // Normalize fonksiyon
        $normalizePhoneNumber = function (string $number): ?string {
            $digits = preg_replace('/\D+/', '', $number);
            if (substr($digits, 0, 2) === '90')
                $digits = substr($digits, 2);
            if (substr($digits, 0, 1) !== '0')
                $digits = '0' . $digits;
            if (preg_match('/^05\d{9}$/', $digits))
                return $digits;
            if (preg_match('/^5\d{9}$/', $digits))
                return '0' . $digits;
            return null;
        };

        $results = [];
        $requests = [];
        $mapNumberToIndex = [];

        $client = new Client([
            'timeout' => 5,
            'connect_timeout' => 3,
        ]);
        $apiUrl = 'http://185.85.237.197/service/1.0/gsmquery/';
        $apiKey = 'b9b2db54eb351db965e884c0f95a8f0c';

        foreach ($numbers as $i => $number) {
            $cleanedNumber = $normalizePhoneNumber($number);
            if ($cleanedNumber === null) {
                $results[$i] = [
                    'number' => $number,
                    'success' => false,
                    'message' => 'Geçersiz telefon numarası formatı.'
                ];
                continue;
            }

            $postData = [
                'apikey' => $apiKey,
                'gsm' => $cleanedNumber,
            ];

            $requests[] = new GuzzleRequest(
                'POST',
                $apiUrl,
                ['Content-Type' => 'application/x-www-form-urlencoded'],
                http_build_query($postData)
            );
            $mapNumberToIndex[] = [$i, $number, $cleanedNumber];
        }

        $concurrency = 10;
        if (count($requests) > 0) {
            $pool = new Pool($client, $requests, [
                'concurrency' => $concurrency,
                'fulfilled' => function ($response, $index) use (&$results, $mapNumberToIndex, $operatorNames) {
                    [$i, $number, $cleanedNumber] = $mapNumberToIndex[$index];
                    try {
                        $json = json_decode($response->getBody()->getContents(), true);
                        $operatorName = 'Bilinmiyor';
                        if (!empty($json['route_id'])) {
                            $operatorName = $operatorNames[$json['route_id']] ?? 'Bilinmiyor';
                        }
                        $results[$i] = [
                            'number' => $number,
                            'success' => true,
                            'operator' => $operatorName,
                            'cached' => false
                        ];
                    } catch (\Throwable $ex) {
                        $results[$i] = [
                            'number' => $number,
                            'success' => false,
                            'message' => 'Hata: ' . $ex->getMessage()
                        ];
                    }
                },
                'rejected' => function ($reason, $index) use (&$results, $mapNumberToIndex) {
                    [$i, $number, $cleanedNumber] = $mapNumberToIndex[$index];
                    $results[$i] = [
                        'number' => $number,
                        'success' => false,
                        'message' => 'API Hatası: ' . (is_string($reason) ? $reason : (method_exists($reason, 'getMessage') ? $reason->getMessage() : 'Bilinmiyor'))
                    ];
                },
            ]);
            $pool->promise()->wait();
        }

        ksort($results);

        // ✅ Başarılı sorgu sayısını JSON dosyasına yaz
        $queryCount = collect($results)->where('success', true)->count();
        if ($queryCount > 0) {
            $limits[$today][$userId] = ($limits[$today][$userId] ?? 0) + $queryCount;
            file_put_contents($limitFile, json_encode($limits, JSON_PRETTY_PRINT));
        }

        return response()->json([
            'success' => true,
            'results' => array_values($results),
            'limit' => [
                'daily_limit' => $dailyLimit,
                'used_count' => ($limits[$today][$userId] ?? 0),
                'remaining' => max(0, $dailyLimit - ($limits[$today][$userId] ?? 0))
            ]
        ]);
    }



}

