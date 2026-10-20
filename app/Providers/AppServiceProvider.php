<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use App\Models\AppMenu;
use App\Models\Contact;
use App\Models\Item;
use App\Models\Order;

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
        if (!app()->runningInConsole()) {
            ini_set('max_execution_time', '180');
            set_time_limit(180);
        }

        View::composer(['layouts.erp', 'erp.*'], function ($view) {
            $userGrpId = auth()->check() ? (auth()->user()->usergrpid ?? 1) : 1;
            $sidebarMenus = AppMenu::getTreeForUser($userGrpId, 'erp');
            $customers = Contact::where('type', 'customer')->where('is_active', true)->orderBy('name')->get();
            $catalogItems = Item::where('is_active', true)->get();
            $activeQuotationsCount = Order::where('order_type', 'quotation')->count();

            $view->with(compact('sidebarMenus', 'customers', 'catalogItems', 'activeQuotationsCount'));
        });
    }
}
