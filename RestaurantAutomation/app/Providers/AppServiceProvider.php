<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use Illuminate\Support\Facades\Session;

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
        Paginator::useBootstrap();
        Paginator::defaultView('pagination::tailwind');
        Paginator::defaultSimpleView('pagination::simple-tailwind');
        User::observe(UserObserver::class);

        // Tüm view'lara settings ve masa bilgisini paylaş
        View::composer('*', function ($view) {
            $settings = Setting::first();
            if (!$settings) {
                $settings = Setting::create([
                    'name' => 'Central Perk Cafe',
                    'address' => 'Merkez Mah. Kahve Sok. No:1 İstanbul',
                    'phone' => '0212 555 55 55',
                    'email' => 'info@centralperk.com'
                ]);
            }
            $view->with('settings', $settings);

            // Masa bilgisini session'dan al ve view'a gönder
            $view->with('sessionTableId', Session::get('table_id'));
        });
    }
}
