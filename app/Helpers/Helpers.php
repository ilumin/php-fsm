<?php
use Carbon\Carbon;

class Helpers
{
    public function orderCanRefund($orderDateTime) {
        $output = false;
        $limitDateTime = $orderDateTime->addDays(3);
        if(Carbon::now()->gte($limitDateTime)){
            $output = true;
        }
        return $output;
    }
}
