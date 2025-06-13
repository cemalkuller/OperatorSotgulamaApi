<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OperatorQuery;
use Carbon\Carbon;

class LimitController extends Controller
{
    /**
     * GET /api/user/limits
     *
     * Dönen JSON yapısı:
     * {
     *   "daily_limit": 1000,
     *   "used_count": 275,
     *   "remaining": 725
     * }
     */
    public function limits(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $dailyLimit = $user->daily_limit;

        $userId = $user->id;
        $today = date('Y-m-d');
        $limitFile = storage_path('app/operator_limits.json');

        // JSON dosyasını oku, yoksa boş dizi
        $limits = file_exists($limitFile) ? json_decode(file_get_contents($limitFile), true) : [];

        // Günlük kullanılan sorgu sayısını JSON'dan al
        $usedCount = $limits[$today][$userId] ?? 0;

        $remaining = max(0, $dailyLimit - $usedCount);

        return response()->json([
            'daily_limit' => $dailyLimit,
            'used_count' => $usedCount,
            'remaining' => $remaining,
            "batch_size" => $user->batch_size ?? 1000
        ]);
    }
}
