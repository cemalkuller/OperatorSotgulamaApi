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

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Bilgiler hatalı!'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('desktop-app')->plainTextToken;

        // Kullanıcının günlük limiti:
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
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Çıkış yapıldı.']);
    }
}
