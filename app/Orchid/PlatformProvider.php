<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

use App\Models\Order;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * @param Dashboard $dashboard
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * @return Menu[]
     */
    public function registerMainMenu(): array
    {

        $new_order = Order::where('state', 'NEW')->count();
        
        return [

            Menu::make('Продажи')
                ->title('Kaspi.kz')
                ->icon('basket-loaded')
                ->route('platform.order.list')
                ->badge(fn () => $new_order)
                ->permission('platform.order.list'),

            Menu::make('Товары')
                ->icon('modules')
                ->route('platform.product.list')
                ->permission('platform.product.list'),

            Menu::make('Покупатели')
                ->icon('people')
                ->route('platform.customer.list')
                ->permission('platform.customer.list'),

            Menu::make('Настройки')
                ->icon('settings')
                ->route('platform.kaspi.settings')
                ->permission('platform.kaspi.settings'),

            Menu::make(__('Users'))
                ->icon('user')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access rights')),

            Menu::make(__('Roles'))
                ->icon('lock')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),
        ];
    }

    /**
     * @return Menu[]
     */
    public function registerProfileMenu(): array
    {
        return [
            Menu::make(__('Profile'))
                ->route('platform.profile')
                ->icon('user'),
        ];
    }

    /**
     * @return ItemPermission[]
     */
    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.order.list', 'Продажи')
                ->addPermission('platform.product.list', 'Товары')
                ->addPermission('platform.customer.list', 'Покупатели')
                ->addPermission('platform.kaspi.settings', 'Настройки'),
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}
