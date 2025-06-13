<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppVersion;

class AppVersionController extends Controller
{
    public function index(Request $request)
    {

        // En son kayıtlı sürümü alıyoruz
        $version = AppVersion::orderByDesc('created_at')->first();

        if (!$version) {
            return response()->json([
                'message' => 'Version not found.'
            ], 404);
        }

        return response()->json([
            'latest_version' => $version->latest_version,
            'download_url' => asset('storage/app_versions/' . $version->download_url),
            'release_notes' => $version->release_notes,
        ]);
    }
}
