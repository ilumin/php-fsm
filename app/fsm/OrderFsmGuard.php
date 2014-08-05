<?php

class OrderFsmGuard
{
    private $order;
    public function __construct($order)
    {
        $this->order = $order;
    }

    public function canRefund()
    {
        return App::make('Helpers')->orderCanRefund($this->order->orderDateTime);
    }
}
