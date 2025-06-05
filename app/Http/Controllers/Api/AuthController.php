<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OperatorQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // POST /api/login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Kimlik doğrulamayı yapalım
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Bilgiler hatalı!'], 401);
        }

        // Geçerli kullanıcıyı alalım
        $user = Auth::user();

        // Önce varsa tüm eski token'ları silelim
        $user->tokens()->delete();

        // Yeni bir token üretelim
        $token = $user->createToken('desktop-app')->plainTextToken;

        // Kullanıcının günlük limiti ve bugün yapılan sorgu sayısını alalım
        $dailyLimit = $user->daily_limit;
        $usedCount = OperatorQuery::countTodayByUser($user->id);

        return response()->json([
            'token' => $token,
            'user' => $user,
            'daily_limit' => $dailyLimit,
            'used_count' => $usedCount,
        ]);
    }

    // POST /api/logout
    public function logout(Request $request)
    {
        // O anda geçerli olan token'ı silebiliriz veya tüm token'ları silebiliriz
        // Eğer tüm token'ları silmek isterseniz:
        // $request->user()->tokens()->delete();

        // Sadece o anki token'ı silmek için:
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Çıkış yapıldı.']);
    }
}
