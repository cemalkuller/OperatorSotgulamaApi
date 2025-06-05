<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Genel Filament Terimleri
    |--------------------------------------------------------------------------
    */
    'dashboard' => 'Kontrol Paneli',
    'resource_label' => 'Kaynak',
    'resource_label_plural' => 'Kaynaklar',
    'navigation' => [
        'welcome' => 'Hoş Geldiniz',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tablo (Listeleme) Bileşeni
    |--------------------------------------------------------------------------
    */
    'table' => [
        'actions' => 'İşlemler',
        'bulk_actions' => 'Toplu İşlemler',
        'search' => 'Ara...',
        'loading' => 'Yükleniyor...',
        'no_records' => 'Gösterilecek kayıt yok.',
        'saved' => 'Kaydedildi',
        'filters' => 'Filtreler',
        'reset_filters' => 'Filtreleri Sıfırla',
        'select_all' => 'Tümünü Seç',
        'select_page' => 'Bu Sayfadakileri Seç',
        'actions_disabled' => 'İşlem yapmadan önce bir kayıt seçin.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Form (Oluşturma/Düzenleme) Bileşeni
    |--------------------------------------------------------------------------
    */
    'forms' => [
        'save' => 'Kaydet',
        'save_and_continue' => 'Kaydet ve Devam Et',
        'cancel' => 'İptal',
        'create' => 'Oluştur',
        'edit' => 'Düzenle',
        'view' => 'Görüntüle',
        'delete' => 'Sil',
        'delete_confirmation' => 'Bu kaydı silmek istediğinize emin misiniz?',
        'deleted' => 'Kayıt silindi.',
        'undeleted' => 'Kayıt geri yüklendi.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Kullanıcı İşlemleri
    |--------------------------------------------------------------------------
    */
    'user' => [
        'profile' => 'Profil',
        'logout' => 'Çıkış Yap',
        'login' => 'Giriş Yap',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Bileşenleri İçin Örnek Metinler
    |--------------------------------------------------------------------------
    */
    'components' => [
        'modal' => [
            'confirm' => 'Onayla',
            'cancel' => 'İptal',
        ],
        'select' => [
            'placeholder' => 'Seçim yapın',
        ],
        'datepicker' => [
            'placeholder' => 'Tarih seçin',
        ],
        'timepicker' => [
            'placeholder' => 'Saat seçin',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Doğrulama (Validation) Hata Mesajları İçin Örnek
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'required' => ':attribute alanı gereklidir.',
        'email' => ':attribute geçerli bir e-posta adresi olmalıdır.',
        // Gerekirse diğer kuralları da ekleyin...
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource Bazlı Özel Etiketler (Örnek)
    |--------------------------------------------------------------------------
    |
    | Örneğin UserResource içinde şunları kullanabilirsiniz:
    | __('filament.resources.user.singular')
    | __('filament.resources.user.plural')
    | __('filament.resources.user.nav_label')
    |
    */
    'resources' => [
        'user' => [
            'singular' => 'Kullanıcı',
            'plural' => 'Kullanıcılar',
            'nav_label' => 'Kullanıcı Yönetimi',
        ],
        // Başka Resource’lar eklemek isterseniz burada tanımlayın...
    ],

];
