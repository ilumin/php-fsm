<?php
use Carbon\Carbon;

class HomeController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Default Home Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |	Route::get('/', 'HomeController@showWelcome');
    |
    */

    public $order;
    public $machine;
    public function __construct() {
        $this->order = new OrderFsm();
        $this->machine = $this->order->getStateMachine();
    }

    public function showWelcome() {
        return View::make('hello');
    }

    public function anyDashboard() {

        $view = View::make('dashboard');
        $view->order = $this->order;
        $view->machine = $this->machine;
        return $view;
    }
    //http://localhost:8888/fsm/buyer-pay-success
    public function anyBuyerPaySuccess() {
        $this->machine->apply('BuyerPaySuccess');
        $this->order->orderPayDateTime = Carbon::now();
        $view = View::make('dashboard');
        $view->order = $this->order;
        $view->machine = $this->machine;
        return $view;
    }
    //http://localhost:8888/fsm/buyer-click-refund
    public function anyBuyerClickRefund() {

    	try{
    		$this->machine->apply('BuyerClickRefund');
    	}catch(Finite\Exception\StateException $e){
    		return Redirect::back()->with('error', 'Cannot refund na ja.');
    	}

        $view = View::make('dashboard');
        $view->order = $this->order;
        $view->machine = $this->machine;
        return $view;
    }
}
