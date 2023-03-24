<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\Customer;


class KaspiController extends Controller
{
    public function start()
    {

        $start_date = strtotime("04.03.2023") * 1000;
        $end_date = strtotime("18.03.2023") * 1000;
        $statuses = array('NEW', 'SIGN_REQUIRED', 'PICKUP', 'DELIVERY', 'KASPI_DELIVERY', 'ARCHIVE');
        $page_number = '0';
        $page_size = '50';

            foreach ($statuses as $status) {
                $url = 'https://kaspi.kz/shop/api/v2/orders?page[number]=' . $page_number . '&page[size]=' . $page_size . '&filter[orders][state]=' . $status . '&filter[orders][creationDate][$ge]=' . $start_date . '&filter[orders][creationDate][$le]=' . $end_date . '&include[orders]=user';

                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $headers = array(
                    "Content-Type: application/vnd.api+json",
                    "X-Auth-Token: OO8qBPEcmd05GTBWi9i7sD30GE3982kCP6rB5tRVIRM=",
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                $resp = curl_exec($curl);
                curl_close($curl);

                $json = json_decode($resp);

                foreach ($json->data as $data) {

                    $customer = Customer::where('kaspi_id', $data->attributes->customer->id)->first();

                    if ($customer === NULL) {

                        $customer = Customer::create([
                            'kaspi_id' => $data->attributes->customer->id ?? '',
                            'name' => $data->attributes->customer->lastName . ' ' . $data->attributes->customer->firstName ?? '',
                            'phone' => $data->attributes->customer->cellPhone ?? '',
                        ]);
                    }

                    $order = Order::where('kaspi_id', $data->id)->first();

                    if ($order === NULL) {
                        Order::create([
                            'type' => $data->type ?? '',
                            'kaspi_id' => $data->id ?? '',
                            'code' => $data->attributes->code ?? '',
                            'totalPrice' => $data->attributes->totalPrice ?? '',
                            'paymentMode' => $data->attributes->paymentMode ?? '',
                            'deliveryCostForSeller' => $data->attributes->deliveryCostForSeller ?? '',
                            'isKaspiDelivery' => $data->attributes->isKaspiDelivery ?? '',
                            'signatureRequired' => $data->attributes->signatureRequired ?? '',
                            'deliveryMode' => $data->attributes->deliveryMode ?? '',
                            'creditTerm' => $data->attributes->creditTerm ?? '0',
                            'waybill' => $data->attributes->kaspiDelivery->waybill ?? '',
                            'state' => $data->attributes->state ?? '',
                            'status' => $data->attributes->status ?? '',
                            'customer_id' => $customer->id ?? '',
                            'preOrder' => $data->attributes->preOrder ?? '',
                            'pickupPointId' => $data->attributes->pickupPointId ?? '',
                            'deliveryAddress' => $data->attributes->deliveryAddress->formattedAddress ?? '',
                            'deliveryCost' => $data->attributes->deliveryCost ?? '',
                            'creationDate' => date("Y-m-d H:i:s", $data->attributes->creationDate / 1000)
                        ]);
                    } else {

                        $order->type = $data->type ?? '';
                        $order->kaspi_id = $data->id ?? '';
                        $order->code = $data->attributes->code ?? '';
                        $order->totalPrice = $data->attributes->totalPrice ?? '';
                        $order->paymentMode = $data->attributes->paymentMode ?? '';
                        $order->deliveryCostForSeller = $data->attributes->deliveryCostForSeller ?? '';
                        $order->isKaspiDelivery = $data->attributes->isKaspiDelivery ?? '';
                        $order->signatureRequired = $data->attributes->signatureRequired ?? '';
                        $order->deliveryMode = $data->attributes->deliveryMode ?? '';
                        $order->creditTerm = $data->attributes->creditTerm ?? '0';
                        $order->waybill = $data->attributes->kaspiDelivery->waybill ?? '';
                        $order->state = $data->attributes->state ?? '';
                        $order->status = $data->attributes->status ?? '';
                        $order->customer_id = $customer->id ?? '';
                        $order->preOrder = $data->attributes->preOrder ?? '';
                        $order->pickupPointId = $data->attributes->pickupPointId ?? '';
                        $order->deliveryAddress = $data->attributes->deliveryAddress->formattedAddress ?? '';
                        $order->deliveryCost = $data->attributes->deliveryCost ?? '';
                        $order->creationDate = date("Y-m-d H:i:s", $data->attributes->creationDate / 1000);
                        $order->save();
                    }
                }
            }
        
        return date("d.m.Y H:i:s", $json->data['0']->attributes->creationDate / 1000);
    }
}
