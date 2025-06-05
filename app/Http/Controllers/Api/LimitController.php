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
        // 1. Mevcut authenticated kullanıcıyı al
        $user = $request->user(); // Auth::user()

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // 2. Kullanıcının günlük limitini al
        $dailyLimit = $user->daily_limit;

        // 3. Bugün içinde yapılan sorgu sayısını hesapla
        // Carbon ile bugünün başlangıcını ve sonunu bulup sorgula
        $todayStart = Carbon::now()->startOfDay();
        $todayEnd = Carbon::now()->endOfDay();

        $usedCount = OperatorQuery::where('user_id', $user->id)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->count();

        // 4. Kalan hakkı hesapla
        $remaining = $dailyLimit - $usedCount;
        if ($remaining < 0) {
            $remaining = 0;
        }

        // 5. JSON olarak geri dön
        return response()->json([
            'daily_limit' => $dailyLimit,
            'used_count' => $usedCount,
            'remaining' => $remaining,
        ]);
    }
}
