<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filament Panel Configuration
    |--------------------------------------------------------------------------
    */

    // 1. Filament panelinin URL kökü. Örneğin '/admin'
    'path' => env('FILAMENT_PATH', 'admin'),

    // 2. Domain (eğer alt alan kullanacaksanız)
    'domain' => env('FILAMENT_DOMAIN'),

    // 3. Middleware grubu
    'middleware' => [
        'web',
        'auth',
        'verified',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Auth Configuration
    |--------------------------------------------------------------------------
    |
    | ‘user’ anahtarı için Closure yerine [ClassName::class, 'method'] biçiminde
    | seri hale getirilebilir (serializable) bir callable kullanıyoruz.
    |
    */

    'auth' => [
        'guard' => env('FILAMENT_AUTH_GUARD', 'web'),

        // Closure yerine şu dizi formatı kullanılmalı:
        'user' => [\App\Filament\FilamentUser::class, 'authorize'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Resources
    |--------------------------------------------------------------------------
    |
    | Eğer Resource’ları elle listelemek isterseniz buraya ekleyin. Aksi takdirde
    | Filament otomatik olarak App\Filament\Resources klasörünüzü tarar.
    |
    */

    'resources' => [
        \App\Filament\Resources\UserResource::class,
        // Diğer Resource sınıflarınız varsa buraya ekleyebilirsiniz...
    ],

    /*
    |--------------------------------------------------------------------------
    | Varsayılan Dosya Sistemi Diski (Filesystem Disk)
    |--------------------------------------------------------------------------
    |
    | Filament’in dosya yükleme vs. için kullanacağı disk.
    |
    */

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Assets Path
    |--------------------------------------------------------------------------
    |
    | Filament asset’lerinin publish edileceği alt klasör (public altında).
    |
    */

    'assets_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    |
    | Filament bileşenleri için cache dosyalarının saklanacağı dizin.
    |
    */

    'cache_path' => base_path('bootstrap/cache/filament'),

    /*
    |--------------------------------------------------------------------------
    | Livewire Loading Delay
    |--------------------------------------------------------------------------
    |
    | Yükleniyor göstergesi gecikmesi: 'default' (200ms) veya 'none'.
    |
    */

    'livewire_loading_delay' => 'default',
];
