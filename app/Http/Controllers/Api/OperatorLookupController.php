<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use Exception;
use Illuminate\Http\Request;

class OperatorLookupController extends Controller
{
    // GET /api/operator-lookup?number=5XXXXXXXXX

    public function lookup(Request $request)
    {
        $number = $request->input('number');
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
                $operator = Operator::where('code', $decode->route_id)->first();
                $response = $operator ? $operator->name : 'Bilinmiyor';
            } else {
                $response = false;
            }

            return response()->json([
                'success' => true,
                'operator' => $response
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
