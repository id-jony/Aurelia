<?php

namespace App\Orchid\Screens\Setting;

use Orchid\Screen\Screen;
use Illuminate\Http\Request;

use App\Models\KaspiSetting;

use App\Helpers\Kaspi\MerchantGetSettings;
use App\Helpers\Kaspi\MerchantLogin;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Color;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Password;

use Illuminate\Support\Facades\Auth;

class SettingScreen extends Screen
{
    public function query(): iterable
    {
        $setting = KaspiSetting::where('user_id', Auth::user()->id)->first();

        return [
            'setting' => $setting,
        ];
    }

    public function name(): ?string
    {
        return 'Настройки Kaspi.kz';
    }

    public function description(): ?string
    {
        return 'Настройки АПИ, чтобы приложение начало получать данные от Kaspi.';
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::block([
                Layout::rows([
                    Input::make('setting.token')
                        ->type('text')
                        ->max(255)
                        ->required()
                        ->title('Токен API')
                        ->placeholder('Введите токен'),
                ])
            ])
                ->title('Kaspi Токен')
                ->description('Уникальный идентификатор, позволяющий получить доступ к программному интерфейсу Kaspi.kz. Сгенерировать токен авторизации можно самостоятельно через личный кабинет на Kaспи мерчант. Подробнее по ссылке.')
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->method('save')
                ),


                Layout::block([
                Layout::rows([
                    Input::make('setting.username')
                        ->type('text')
                        ->max(255)
                        ->required()
                        ->title('Email')
                        ->placeholder('Введите email'),
                    Password::make('setting.password')
                        ->type('text')
                        ->max(255)
                        ->title('Пароль')
                        ->placeholder('Введите пароль'),
                ])
            ])
                ->title('Kaspi Мерчант')
                ->description('Введите данные для входа в личный кабинет Каспи Мерчант, что бы иметь возможность управления товарами через платформу')
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->method('save')
                ),

                Layout::block([
                Layout::rows([
                    Input::make('setting.shop_name')
                        ->type('text')
                        ->max(255)
                        ->required()
                        ->title('Название магазина')
                        ->placeholder('Введите название'),
                    Input::make('setting.count_day')
                        ->type('text')
                        ->max(255)
                        ->required()
                        ->title('Сколько дней сканировать (рекомендуемое значение 30)')
                        ->placeholder('30'),
                    Input::make('setting.interval_day')
                        ->type('number')
                        ->max(255)
                        ->required()
                        ->title('Интервал дней сканирования (рекомендуемое значение 14)')
                        ->placeholder('14'),
                ])
            ])
                ->title('Общие настройки')
                ->description('Дополнительные настройки')
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->method('save')
                ),

        ];
    }


public function save(Request $request): void
{
    $setting = KaspiSetting::where('user_id', Auth::user()->id)->first();

    if ($setting === null) {
        $setting = KaspiSetting::create([
            'user_id' => Auth::user()->id,
            'token' => $request->input('setting.token'),
            'username' => $request->input('setting.username'),
            'password' => $request->input('setting.password'),
        ]);
    } else {
        $setting->fill($request->get('setting'))->save();
    }

    $token = MerchantLogin::gen($request->input('setting.username'), $request->input('setting.password'));
    $info = MerchantGetSettings::gen($token);
    $info = json_decode($info);
    $setting->shop_name = $info->name;
    $setting->shop_id = $info->affiliateId;
    $setting->save();


    Toast::info('Настройки успешно сохранены');
}
}
