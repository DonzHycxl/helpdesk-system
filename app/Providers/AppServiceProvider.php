<?php

namespace App\Providers;

use App\Filament\Widgets\CurrentDateTimeWidget;
use App\Models\User;
use App\Policies\UserPolicy;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Gate;
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
        //
        Gate::policy(User::class, UserPolicy::class);
    }

    protected function getDashboardWidgets(): array
    {
        return [
            Widget::make(CurrentDateTimeWidget::class),
        ];
    }
}
