<?php

namespace App\Api\Kaspi;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Log;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;
use App\Exceptions\KaspiException;

class MerchantGetSettings
{
    /**
     * Получение списка настроек мерчанта
     *
     * @param string $token
     * @return object|null
     *
     * @throws KaspiException
     */
    public static function gen(string $token): ?object
    {
        $config = config('services.kaspi');
        $headers = self::createHeaders($token);

        try {
            $response = Http::withHeaders($headers)
                ->withOptions([
                    'debug' => $config['debug'],
                    'allow_redirects' => false,
                ])
                ->accept('application/json')
                ->get('https://kaspi.kz/merchantcabinet/api/merchant/settings');

            if ($response->successful()) {
                return json_decode($response->body());
            } else {
                throw new KaspiException('Failed to get merchant settings.');
            }
        } catch (ConnectException $exception) {
            throw new KaspiException('Connection error: ' . $exception->getMessage());
        } catch (RequestException $exception) {
            throw new KaspiException('Request error: ' . $exception->getMessage());
        } catch (\Exception $exception) {
            throw new KaspiException('Error while getting merchant settings.');
        }
    }

    /**
     * Создание заголовков для HTTP-запроса
     *
     * @param string $token
     * @return array
     */
    private static function createHeaders(string $token): array
    {
        return [
            'Content-Type' => 'application/json',
            'Referer' => 'https://kaspi.kz/mc/',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.2 Safari/605.1.15',
            'Cookie' => 'X-Mc-Api-Session-Id=' . $token,
        ];
    }
}




//  JSON Ответ
// {
//     "name": "Aurelia",
//     "affiliateId": "11906026",
//     "hybrisUid": "11906026",
//     "logoUrl": "https://cdn-kaspi.kz/merchantcabinet/medias/sys_master/root/h99/h0e/69041188470814/-thumb-1648281.png",
//     "accountManager": null,
//     "orderProcessingManager": {
//       "name": "11906026 master",
//       "firstName": "Раушан",
//       "lastName": "Кобылецкая",
//       "phone": "7068131356",
//       "email": "hello@prgm.kz"
//     },
//     "managerOpeningHours": {
//       "code": "11906026-processing-hours",
//       "name": null,
//       "weekdayOpeningDays": {
//         "0": {
//           "closingTime": {
//             "minute": 0,
//             "hour": 21,
//             "formattedHour": "21:00"
//           },
//           "openingTime": {
//             "minute": 0,
//             "hour": 10,
//             "formattedHour": "10:00"
//           },
//           "weekDay": "Пн",
//           "closed": false
//         },
//         "1": {
//           "closingTime": {
//             "minute": 0,
//             "hour": 21,
//             "formattedHour": "21:00"
//           },
//           "openingTime": {
//             "minute": 0,
//             "hour": 10,
//             "formattedHour": "10:00"
//           },
//           "weekDay": "Вт",
//           "closed": false
//         },
//         "2": {
//           "closingTime": {
//             "minute": 0,
//             "hour": 21,
//             "formattedHour": "21:00"
//           },
//           "openingTime": {
//             "minute": 0,
//             "hour": 10,
//             "formattedHour": "10:00"
//           },
//           "weekDay": "Ср",
//           "closed": false
//         },
//         "3": {
//           "closingTime": {
//             "minute": 0,
//             "hour": 21,
//             "formattedHour": "21:00"
//           },
//           "openingTime": {
//             "minute": 0,
//             "hour": 10,
//             "formattedHour": "10:00"
//           },
//           "weekDay": "Чт",
//           "closed": false
//         },
//         "4": {
//           "closingTime": {
//             "minute": 0,
//             "hour": 21,
//             "formattedHour": "21:00"
//           },
//           "openingTime": {
//             "minute": 0,
//             "hour": 10,
//             "formattedHour": "10:00"
//           },
//           "weekDay": "Пт",
//           "closed": false
//         },
//         "5": {
//           "closingTime": {
//             "minute": 0,
//             "hour": 21,
//             "formattedHour": "21:00"
//           },
//           "openingTime": {
//             "minute": 0,
//             "hour": 10,
//             "formattedHour": "10:00"
//           },
//           "weekDay": "Сб",
//           "closed": false
//         },
//         "6": {
//           "closingTime": {
//             "minute": 0,
//             "hour": 21,
//             "formattedHour": "21:00"
//           },
//           "openingTime": {
//             "minute": 0,
//             "hour": 10,
//             "formattedHour": "10:00"
//           },
//           "weekDay": "Вс",
//           "closed": false
//         }
//       }
//     },
//     "pointOfServiceList": [
//       {
//         "name": "11906026_PP1",
//         "displayName": "PP1",
//         "cityName": "Алматы",
//         "address": {
//           "streetName": "Virtual",
//           "streetNumber": "Street",
//           "town": "г. Алматы",
//           "district": null,
//           "building": null,
//           "apartment": null,
//           "formattedAddress": "г. Алматы, Virtual, Street",
//           "city": null,
//           "location": null
//         },
//         "phoneNumber": null,
//         "workingHours": {
//           "code": "11906026-Sch2",
//           "name": null,
//           "weekdayOpeningDays": {
//             "0": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 21,
//                 "formattedHour": "21:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 9,
//                 "formattedHour": "9:00"
//               },
//               "weekDay": "Пн",
//               "closed": false
//             },
//             "1": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 21,
//                 "formattedHour": "21:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 9,
//                 "formattedHour": "9:00"
//               },
//               "weekDay": "Вт",
//               "closed": false
//             },
//             "2": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 21,
//                 "formattedHour": "21:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 9,
//                 "formattedHour": "9:00"
//               },
//               "weekDay": "Ср",
//               "closed": false
//             },
//             "3": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 21,
//                 "formattedHour": "21:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 9,
//                 "formattedHour": "9:00"
//               },
//               "weekDay": "Чт",
//               "closed": false
//             },
//             "4": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 21,
//                 "formattedHour": "21:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 9,
//                 "formattedHour": "9:00"
//               },
//               "weekDay": "Пт",
//               "closed": false
//             },
//             "5": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 12,
//                 "formattedHour": "12:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 12,
//                 "formattedHour": "12:00"
//               },
//               "weekDay": "Сб",
//               "closed": true
//             },
//             "6": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 12,
//                 "formattedHour": "12:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 12,
//                 "formattedHour": "12:00"
//               },
//               "weekDay": "Вс",
//               "closed": true
//             }
//           }
//         },
//         "available": false,
//         "status": "INACTIVE",
//         "warehouse": true,
//         "city": null,
//         "geoPoint": null
//       },
//       {
//         "name": "11906026_01",
//         "displayName": "01",
//         "cityName": "Алматы",
//         "address": {
//           "streetName": "проспект Достык",
//           "streetNumber": "19",
//           "town": "г. Алматы",
//           "district": null,
//           "building": null,
//           "apartment": null,
//           "formattedAddress": "г. Алматы, проспект Достык, 19",
//           "city": null,
//           "location": null
//         },
//         "phoneNumber": null,
//         "workingHours": {
//           "code": "11906026-Sch1",
//           "name": null,
//           "weekdayOpeningDays": {
//             "0": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 20,
//                 "formattedHour": "20:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 10,
//                 "formattedHour": "10:00"
//               },
//               "weekDay": "Пн",
//               "closed": false
//             },
//             "1": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 20,
//                 "formattedHour": "20:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 10,
//                 "formattedHour": "10:00"
//               },
//               "weekDay": "Вт",
//               "closed": false
//             },
//             "2": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 20,
//                 "formattedHour": "20:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 10,
//                 "formattedHour": "10:00"
//               },
//               "weekDay": "Ср",
//               "closed": false
//             },
//             "3": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 20,
//                 "formattedHour": "20:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 10,
//                 "formattedHour": "10:00"
//               },
//               "weekDay": "Чт",
//               "closed": false
//             },
//             "4": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 20,
//                 "formattedHour": "20:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 10,
//                 "formattedHour": "10:00"
//               },
//               "weekDay": "Пт",
//               "closed": false
//             },
//             "5": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 20,
//                 "formattedHour": "20:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 10,
//                 "formattedHour": "10:00"
//               },
//               "weekDay": "Сб",
//               "closed": false
//             },
//             "6": {
//               "closingTime": {
//                 "minute": 0,
//                 "hour": 20,
//                 "formattedHour": "20:00"
//               },
//               "openingTime": {
//                 "minute": 0,
//                 "hour": 10,
//                 "formattedHour": "10:00"
//               },
//               "weekDay": "Вс",
//               "closed": false
//             }
//           }
//         },
//         "available": false,
//         "status": "ACTIVE",
//         "warehouse": true,
//         "city": null,
//         "geoPoint": null
//       }
//     ],
//     "merchantUrl": "https://kaspi.kz/shop/info/merchant/11906026/address-tab/",
//     "merchantAllProductsUrl": "https://kaspi.kz/shop/search/?q=%3AallMerchants%3A11906026",
//     "uploadFileTypeEnums": [
//       "XML"
//     ]
//   }
