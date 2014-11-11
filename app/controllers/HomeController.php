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
    |   Route::get('/', 'HomeController@showWelcome');
    |
    */

    public $order;
    public $machine;
    public function __construct() {
        App::make('Helpers')->clearSession();
        $initial_state = null;
        if (Session::has('current_state')) {
            $initial_state = Session::get('current_state');
        }
        $this->order = new OrderFsm($initial_state);
        $this->machine = $this->order->getStateMachine();
        Session::put('current_state', $this->order->getFiniteState());

        $states = $this->machine->getStates();
        Session::put('states', $states);
    }

    public function showWelcome() {
        return View::make('hello');
    }

    public function anyDashboard() {
        $initial_state = Input::get('init_state_name');
        $this->order->setFiniteState($initial_state);
        $this->machine->initialize();
        Session::put('current_state', $initial_state);

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

        try {
            $this->machine->apply('BuyerClickRefund');
        }
        catch(Finite\Exception\StateException $e) {
            return Redirect::back()->with('error', 'Cannot refund na ja.');
        }

        $view = View::make('dashboard');
        $view->order = $this->order;
        $view->machine = $this->machine;
        return $view;
    }
    //http://localhost:8888/fsm/welove-click-transfer
    public function anyWeloveClickTransfer() {

        try {
            //sd($this->machine);
            $this->machine->apply('WeloveClickTransfer');
        }
        catch(Finite\Exception\StateException $e) {
            return Redirect::back()->with(array(
                'error' => 'Cannot transfer na ja.',
                'message' => $e->getMessage() ,
            ));
        }

        $view = View::make('dashboard');
        $view->order = $this->order;
        $view->machine = $this->machine;
        return $view;
    }
    //http://localhost:8888/fsm/set-init-state
    public function anySetInitState() {
        $initial_state = Input::get('init_state_name');
        $this->order->setFiniteState($initial_state);
        $this->machine->initialize();
        Session::put('current_state', $initial_state);

        $view = View::make('dashboard');
        $view->order = $this->order;
        $view->machine = $this->machine;
        return $view;
    }
}
