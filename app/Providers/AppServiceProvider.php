<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Models\Menu;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();
        Carbon::setLocale('id');
        View::composer('*', function ($view) {

            $menus = Menu::with('children')
                ->whereNull('parent_id')
                ->where('status', true)
                ->orderBy('sort')
                ->get();

            $view->with('menus', $menus);
        });
    }

}
