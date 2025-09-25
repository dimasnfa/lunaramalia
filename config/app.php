<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |----------------------------------------------------------------------
    | Application Name
    |----------------------------------------------------------------------
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |----------------------------------------------------------------------
    | Application Environment
    |----------------------------------------------------------------------
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |----------------------------------------------------------------------
    | Application Debug Mode
    |----------------------------------------------------------------------
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |----------------------------------------------------------------------
    | Application URL
    |----------------------------------------------------------------------
    */

    'url' => env('APP_URL', 'http://localhost'),
    'https-url' => env('HTTPS_URL', 'https://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |----------------------------------------------------------------------
    | Application Timezone
    |----------------------------------------------------------------------
    | ✅ PERBAIKAN UTAMA: Ubah dari 'UTC' ke 'Asia/Jakarta'
    */

    'timezone' => 'Asia/Jakarta', // ✅ PERBAIKAN: Ganti dari UTC

    /*
    |----------------------------------------------------------------------
    | Application Locale Configuration
    |----------------------------------------------------------------------
    */

    'locale' => 'en',

    /*
    |----------------------------------------------------------------------
    | Application Fallback Locale
    |----------------------------------------------------------------------
    */

    'fallback_locale' => 'en',

    /*
    |----------------------------------------------------------------------
    | Faker Locale
    |----------------------------------------------------------------------
    */

    'faker_locale' => 'en_US',

    /*
    |----------------------------------------------------------------------
    | Encryption Key
    |----------------------------------------------------------------------
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |----------------------------------------------------------------------
    | Maintenance Mode Driver
    |----------------------------------------------------------------------
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store' => 'redis',
    ],

    /*
    |----------------------------------------------------------------------
    | Autoloaded Service Providers
    |----------------------------------------------------------------------
    */

    'providers' => ServiceProvider::defaultProviders()->merge([

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        Spatie\Permission\PermissionServiceProvider::class,
        Milon\Barcode\BarcodeServiceProvider::class,

    ])->toArray(),

    /*
    |----------------------------------------------------------------------
    | Class Aliases
    |----------------------------------------------------------------------
    */

    'aliases' => Facade::defaultAliases()->merge([

        'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
        'DNS1D' => Milon\Barcode\Facades\DNS1DFacade::class,
        'DNS2D' => Milon\Barcode\Facades\DNS2DFacade::class,
        'webhook_url' => env('WEBHOOK_URL', 'http://127.0.0.1:8000'),

    ])->toArray(),

];