<?php
use Carbon\Carbon;

class Helpers
{
    public function orderCanRefund($orderDateTime) {
        $output = false;
        $limitDateTime = $orderDateTime->addDays(3);
        if (Carbon::now()->gte($limitDateTime)) {
            $output = true;
        }
        return $output;
    }

    public function clearSession() {
        $data = Session::all();
        foreach ($data as $key => $value) {
            if (strpos($key, 'PRE_TRANSITION') !== false) {
                Session::remove($key);
            }
            if (strpos($key, 'POST_TRANSITION') !== false) {
                Session::remove($key);
            }
        }
    }
}
