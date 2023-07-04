<?php

namespace App\Http\Controllers\Nova;

use App\Models\Shop;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

use App\Api\Kaspi\MerchantGetSettings;
use App\Api\Kaspi\MerchantLogin;

class ShopController
{
    public function index(NovaRequest $request)
    {
        $user = $request->user();
        $settings = $user->shop ?? new Shop();

        return response()->json([
            'token' => $settings->token ?? '',
            'username' => $settings->username ?? '',
            'password' => $settings->password ?? '',
            'interval_demp' => $settings->interval_demp ?? '',
            'percent_sales' => $settings->percent_sales ?? '',

        ]);
    }


    public function store(Request $request)
{
    $user = $request->user();
    $settings = $user->shop ?? new Shop();
    $settings->fill($request->only(['token', 'username', 'interval_demp', 'percent_sales']));
    
    // Проверяем, заполнено ли поле "password"
    if (!empty($request->input('password'))) {
        $settings->password = $request->input('password');
    }

    $user->shop()->save($settings);

    $token = MerchantLogin::gen($settings->username, $settings->password);
        $info = MerchantGetSettings::gen($token);

        $points = collect();

        foreach ($info->pointOfServiceList as $point) {
            if ($point->status === 'ACTIVE') {
                $name = array("name" => $point->name);
                $points->push($name);
            }
        }
        $settings->shop_name = $info->name;
        $settings->shop_id = $info->affiliateId;
        $settings->points = $points;
        $settings->save();

    return response()->json(null, 200);
}
}
