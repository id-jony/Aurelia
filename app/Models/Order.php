<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Model;

use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Order extends Model
{
    use HasFactory, AsSource;
    use Filterable;


    protected $fillable = [
        'id',
        'kaspi_id',
        'code',
        'totalPrice',
        'paymentMode',
        'deliveryCostForSeller',
        'isKaspiDelivery',
        'signatureRequired',
        'deliveryMode',
        'creditTerm',
        'waybill',
        'state',
        'status',
        'customer_id',
        'preOrder',
        'pickupPointId',
        'deliveryAddress',
        'deliveryCost',
        'creationDate',
        'transmissionDate',
        'plannedDeliveryDate',
        'user_id'

    ];

    protected $allowedFilters = [
        'id',
        'kaspi_id',
        'code',
        'totalPrice',
        'paymentMode',
        'deliveryCostForSeller',
        'isKaspiDelivery',
        'signatureRequired',
        'deliveryMode',
        'creditTerm',
        'waybill',
        'state',
        'status',
        'customer_id',
        'preOrder',
        'pickupPointId',
        'deliveryAddress',
        'deliveryCost',
        'creationDate',
        'transmissionDate',
        'plannedDeliveryDate',
        'user_id',
        'count'
       
    ];

    protected $allowedSorts = [
        'id',
        'kaspi_id',
        'code',
        'totalPrice',
        'paymentMode',
        'deliveryCostForSeller',
        'isKaspiDelivery',
        'signatureRequired',
        'deliveryMode',
        'creditTerm',
        'waybill',
        'state',
        'status',
        'customer_id',
        'preOrder',
        'pickupPointId',
        'deliveryAddress',
        'deliveryCost',
        'creationDate',
        'transmissionDate',
        'plannedDeliveryDate',
        'user_id',
        'count'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'creationDate' => 'datetime:d-m-Y H:i:s',
        'transmissionDate' => 'datetime:d M Y',
        'plannedDeliveryDate' => 'datetime:d M Y',
    ];
    
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(OrderShipment::class, 'order_id');
    }


    protected static function boot()
    {
        parent::boot();
        static::created(function ($model) {
            
        });

        self::deleting(function ($model) {

        });
    }

    const DELIVERY_VALUE = [
        'DELIVERY_LOCAL' => 'Доставка своими силами',
        'DELIVERY_PICKUP' => 'Самовывоз',
        'DELIVERY_REGIONAL_PICKUP' => 'Kaspi доставка до точки самовывоза',
        'DELIVERY_REGIONAL_TODOOR' => 'Kaspi доставка',
        'DELIVERY_POSTOMAT' => 'Kaspi постомат',

    ];

    const  PAYMENT_VALUE = [
        'PAY_WITH_CREDIT' => 'В кредит',
        'PREPAID' => 'Безналичная',
    ];

    const  STATUS_VALUE = [
        'APPROVED_BY_BANK' => 'Одобрен',
        'ACCEPTED_BY_MERCHANT' => 'Принят',
        'COMPLETED' => 'Завершён',
        'CANCELLED' => 'Отменён',
        'CANCELLING' => 'Ожидает отмены',
        'KASPI_DELIVERY_RETURN_REQUESTED' => 'Ожидает возврата',
        'RETURN_ACCEPTED_BY_MERCHANT' => 'Ожидает решения по возврату',
        'RETURNED' => 'Возвращён',
    ];

    const  STATE_VALUE = [
        'NEW' => 'Новый',
        'SIGN_REQUIRED' => 'На подписании',
        'PICKUP' => 'Самовывоз',
        'DELIVERY' => 'Доставка',
        'KASPI_DELIVERY' => 'Kaspi Доставка',
        'ARCHIVE' => 'Архив',
    ];
 

}
