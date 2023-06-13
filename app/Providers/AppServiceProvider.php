<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
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
        Filament::serving(function () {
            Filament::registerNavigationItems([
                NavigationItem::make('Author')
                    ->url('https://t.me/salwat_me', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-cursor-click')
                    ->group('Other')
                    ->sort(3),
            ]);
        });
    }
}
