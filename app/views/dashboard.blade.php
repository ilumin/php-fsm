<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @section('title')
            Dashboard
        @show
    </title>
    <meta name="keywords" content="{{{ Lang::get('site.meta-keywords') }}}" />
    <meta name="author" content="{{{ Lang::get('site.meta-author') }}}" />
    <meta name="description" content="{{{ Lang::get('site.meta-desc') }}}" />

    {{-- Mobile Specific Metas --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{{ asset('css/bootstrap.min.css') }}}">
    <link rel="stylesheet" href="{{{ asset('css/normalize.css') }}}">
    <link rel="stylesheet" href="{{{ asset('css/main.css') }}}">
    @yield('page-css')
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

</head>

<body>
<!--[if lt IE 7]>
    <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-8 col-md-offset-2">
            <h2>FSM Demo</h2>
                <p>Order Date Time: {{ $order->orderDateTime->toDateTimeString() }}</p>
                <p>Order Pay Date Time: @if(!empty($order->orderPayDateTime)){{ $order->orderPayDateTime->toDateTimeString() }}@endif</p>
                <p>Order Pay Date Time2: {{ $machine->getCurrentState()->get('paidDate') }}</p>
                <p>Current State: {{ $machine->getCurrentState()->getName() }}</p>
                @yield('content')

            @if(Session::has('error'))
                <div class="alert alert-error alert-danger">
                    {{ Session::get('error') }}
                </div>
            @endif
            <br><hr>
            <button type="submit" class="btn btn-primary" onclick="post_to_url('<?php echo URL::to('fsm/dashboard') ?>', null, 'get');
            ">Reset</button>

            @if($machine->getCurrentState()->get('payable'))
            <button type="submit" class="btn btn-primary" onclick="post_to_url('<?php echo URL::to('fsm/buyer-pay-success') ?>', null, 'get');
            ">Pay</button>
            @endif

            @if($machine->getCurrentState()->get('refundable'))
            <button type="submit" class="btn btn-primary" onclick="post_to_url('<?php echo URL::to('fsm/buyer-click-refund') ?>', null, 'get');
            ">Buyer Refund</button>
            @endif
            <br><hr>
            <div id="footer">
                <div class="container">
                    <p>© 2014 BridgeAsia</p>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Javascripts --}}
<script src="{{{ asset('js/vendor/jquery-1.11.1.min.js') }}}"></script>
<script src="{{{ asset('js/bootstrap.min.js') }}}"></script>
<script src="{{{ asset('js/plugins.js') }}}"></script>
<script src="{{{ asset('js/main.js') }}}"></script>
<script>
function post_to_url(path, params, method) {
        method = method || "post";
        var form = document.createElement("form");
        form.setAttribute("method", method);
        form.setAttribute("action", path);
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", '_token');
        hiddenField.setAttribute("value", {{ '"' . Session::getToken() . '"' }});
        form.appendChild(hiddenField);

        for(var key in params) {
            if(params.hasOwnProperty(key)) {
                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", key);
                hiddenField.setAttribute("value", params[key]);
                form.appendChild(hiddenField);
             }
        }
        document.body.appendChild(form);
        form.submit();
    }
</script>
@yield('page-js-script')
</body>
</html>
