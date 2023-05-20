<b>Заказ №{{ $order->code }}</b>
Упакуйте и передайте заказ до
{{ $order->transmissionDate }}
----------
<b>Информация о заказе</b>
{{-- @foreach (Order::STATUS_VALUE as $key => $value) {
    @if ($key == $order->status) {
        <strong>Статус:</strong> {{ $value }}
    }
} --}}
<strong>Дата поступления:</strong> {{ $order->creationDate }}
<strong>Планируемая дата доставки:</strong> {{ $order->plannedDeliveryDate }}
<strong>Сумма:</strong> {{ umber_format($order->totalPrice, 0, ',', ' ') . ' ₸' }}
<strong>Способ доставки:</strong> {{ $order->deliveryMode }}

<strong>Получатель:</strong>
{{ $order->customer->name }}
{{ $order->customer->phone }}
{{ $order->customer->town ?? '' }}