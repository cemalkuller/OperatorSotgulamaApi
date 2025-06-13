<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppVersion;

class AppVersionSeeder extends Seeder
{
    public function run()
    {
        AppVersion::create([
            'latest_version' => '2.1.0',
            'download_url' => 'https://yourserver.com/downloads/OperatorSorgulamaApp_v2.1.0.exe',
            'release_notes' => '2.1.0 sürümü: Performans iyileştirmeleri ve hata düzeltmeleri.',
        ]);
    }
}
