<?php

namespace App\Providers;

use App\Nova\User;
use App\Nova\Product;
use App\Nova\Town;
use App\Nova\Rival;
use App\Nova\Review;
use App\Nova\ProductMerchant;
use App\Nova\PriceHistory;
use App\Nova\PriceManagement;
use App\Nova\Discount;

use App\Nova\Order;
use App\Nova\OrderShipment;
use App\Nova\Сonsumer;
use App\Nova\KaspiSetting;
use App\Nova\Category;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Nova\Dashboards\Main;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

use Pragma\KaspiSettings\KaspiSettings;

use Llaski\NovaScheduledJobs\Tool as NovaScheduledJobsTool;

class NovaServiceProvider extends NovaApplicationServiceProvider
{


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();


        Nova::mainMenu(function (Request $request) {
            return [
                MenuSection::dashboard(Main::class)->icon('chart-bar'),

                MenuSection::make('Kaspi.kz', [
                    MenuItem::resource(Product::class),
                    MenuItem::resource(Order::class),
                    MenuItem::resource(Сonsumer::class),
                    MenuItem::resource(Discount::class),
                    MenuItem::make('Настройки')->path('/kaspi-settings'),
                ])->icon('document-text')->collapsable(),
                
                MenuSection::make('Рассылки', [
                    MenuItem::resource(PriceManagement::class),
                ])->icon('chat')
                ->collapsable()
                ->canSee(function ($request) {
                    return $request->user()->isSuperAdmin();
                }),

                MenuSection::make('Управление', [
                    MenuItem::resource(User::class),
                    MenuItem::resource(Town::class),
                    MenuItem::resource(Category::class),
                    MenuItem::resource(ProductMerchant::class),
                    MenuItem::resource(PriceHistory::class),
                    MenuItem::resource(OrderShipment::class),
                    MenuItem::resource(Rival::class),
                    MenuItem::resource(Review::class),
                    // MenuItem::make('Телескоп')->path('https://crm.aurelia.kz/telescope/client-requests                    '),
                    MenuItem::make('Запланированные задачи')->path('/nova-scheduled-jobs'),
                ])->icon('user')
                ->collapsable()
                ->canSee(function ($request) {
                    return $request->user()->isSuperAdmin();
                }),
                
                MenuSection::make('Роли/Разрешения', [
                    MenuItem::make('Роли')->path('resources/roles'),
                    MenuItem::make('Разрешения')->path('resources/permissions'),
                ])->icon('shield-check')
                ->collapsable()
                ->canSee(function ($request) {
                    return $request->user()->isSuperAdmin();
                }),

                Nova::footer(function ($request) {
                    return 'Создано с ♥ в Pragma Agency';
                })
            ];
        });


        
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();

    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                'hello@prgm.kz'
            ]);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            new KaspiSettings,
            new NovaScheduledJobsTool,
            (new \Sereny\NovaPermissions\NovaPermissions())->canSee(function ($request) {
                return $request->user()->isSuperAdmin();
            }),
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
