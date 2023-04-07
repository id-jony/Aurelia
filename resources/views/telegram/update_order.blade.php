<b>Обновлен заказ №{{ $order->code }}</b>
Упакуйте и передайте заказ до
{{ $order->transmissionDate }}

<b>Информация о заказе</b>
<strong>Статус:</strong> {{ $order->state }}


<strong>Дата поступления:</strong> {{ $order->creationDate }}
<strong>Планируемая дата доставки:</strong> {{ $order->plannedDeliveryDate }}
<strong>Сумма:</strong> {{ number_format($order->totalPrice, 0, ',', ' ') . ' ₸'}}
<strong>Способ доставки:</strong> {{ $order->deliveryMode }}

<b>Информация о товарах</b>


<b>Получатель:</b>
{{ $order->customer->name }}
{{ $order->customer->phone }}
{{ $order->customer->town }}