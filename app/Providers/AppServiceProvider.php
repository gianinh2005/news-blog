<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Sử dụng giao diện phân trang Bootstrap 5 mặc định toàn hệ thống
        Paginator::useBootstrapFive();

        // Tự động nhận diện URL Codespaces hoặc reverse proxy
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'https';
            URL::forceRootUrl("{$proto}://{$_SERVER['HTTP_X_FORWARDED_HOST']}");
        } elseif (! empty(config('app.url')) && ! str_contains(config('app.url'), 'localhost')) {
            URL::forceRootUrl(config('app.url'));
        }

        // Ép HTTPS khi chạy qua proxy HTTPS hoặc khi APP_URL là https
        if (
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || str_starts_with((string) config('app.url'), 'https://')
        ) {
            URL::forceScheme('https');
        }
    }
}
