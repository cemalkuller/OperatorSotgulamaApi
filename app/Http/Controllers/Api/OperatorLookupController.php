<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\OperatorQuery;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperatorLookupController extends Controller
{
    // GET /api/operator-lookup?number=5XXXXXXXXX
    public function lookup(Request $request)
    {
        // 1. Kullanıcı oturumu kontrolü (sanctum, jwt vb. kullandıysanız middleware zaten Auth::user()'a izin verecek)
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Önce giriş yapmalısınız.'
            ], 401);
        }

        $number = $request->input('number');

        // 2. Bugün için yapılan sorgu sayısını al
        $usedCount = OperatorQuery::countTodayByUser($user->id);
        $dailyLimit = $user->daily_limit; // users tablosundaki daily_limit sütunu

        if ($usedCount >= $dailyLimit) {
            return response()->json([
                'success' => false,
                'message' => 'Bugün sorgu limitinizi doldurdunuz.'
            ], 429); // 429 Too Many Requests
        }

        try {
            $cleanedNumber = preg_replace('/[^0-9]/', '', $number);

            if (strpos($cleanedNumber, '90') === 0) {
                $cleanedNumber = '0' . substr($cleanedNumber, 2);
            } elseif (strpos($cleanedNumber, '90') === 1) {
                $cleanedNumber = '0' . substr($cleanedNumber, 3);
            } elseif (strpos($cleanedNumber, '9') === 0) {
                $cleanedNumber = '0' . substr($cleanedNumber, 1);
            }
            if (!preg_match('/^(?:\+90|0)?5\d{9}$/', $cleanedNumber)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz telefon numarası formatı'
                ], 400);
            }
            if (strpos($cleanedNumber, '0') !== 0) {
                $cleanedNumber = '0' . $cleanedNumber;
            }

            $postData = http_build_query([
                'apikey' => 'b9b2db54eb351db965e884c0f95a8f0c',
                'gsm' => $cleanedNumber
            ]);

            $url = "http://185.85.237.197/service/1.0/gsmquery/";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new Exception('cURL hatası: ' . curl_error($ch));
            }

            curl_close($ch);

            $decode = json_decode($data);

            if (isset($decode->route_id)) {
                $operatorModel = Operator::where('code', $decode->route_id)->first();
                $response = $operatorModel ? $operatorModel->name : 'Bilinmiyor';
            } else {
                $response = false;
            }

            // 3. Eğer burada bir sonuç (başarı veya “Bilinmiyor”) döndüysek,
            //    operator_queries tablosuna bir kayıt ekleyelim:
            OperatorQuery::create([
                'user_id' => $user->id,
                'phone_number' => $cleanedNumber,
                'operator_name' => $response,
            ]);

            return response()->json([
                'success' => true,
                'operator' => $response
            ]);
        } catch (Exception $e) {
            // Eğer hata oluşursa yine kayıt ekleyebiliriz (operator_name = null veya “Hata”)
            OperatorQuery::create([
                'user_id' => $user->id,
                'phone_number' => $cleanedNumber ?? $number,
                'operator_name' => null,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
