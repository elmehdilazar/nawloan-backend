@extends('layouts.admin.app')
@section('title',' | ' . __('site.dashboard'))
@section('styles')
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/daterangepicker.css')}}">
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
    <div class="flex-space mb-4 dash-head">
        <h2 class="section-title mb-0">@lang('site.overview')</h2>
        <div class="form-group mb-0">
            <div id="reportrange" class="angle-down">
                <i class="fad fa-calendar-alt"></i>
                <span></span>
            </div>
            <form action="{{route('admin.index')}}" method="get" id="changeStatistics">
                <input type="hidden" id="start_date" name="start_date">
                <input type="hidden" id="end_date" name="end_date">
            </form>
        </div>
    </div>
    <div class="row mb-4">
        @if (auth()->user()->hasPermission('transactions_read') || auth()->user()->hasRole('superadministrator'))
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="status-box-wrapper">
                    <a href="{{route('admin.transactions.index')}}" title="@lang('site.view_all')" class="card status-box">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col">
                                    <small class="">@lang('site.net_earnings')</small>
                                    <h3 class="card-title mb-0">{{$earn['profit']}}</h3>
                                    <p class="small text-muted mb-0">
                                        <i class="far {{ $stats_change['profit'] >= 0 ? 'text-success fa-long-arrow-alt-up' : 'text-danger fa-long-arrow-alt-down' }}"></i>
                                       <span class="{{ $stats_change['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
    {{ abs($stats_change['profit']) }}%
</span>

                                    </p>
                                </div>
                                <div class="col d-flex justify-content-end barchart">
                                    <img src="{{asset('assets/images/svgs/bar-chart-green.svg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="status-box-wrapper">
                    <a href="{{route('admin.offers.index',['status'=>'complete'])}}" title="@lang('site.view_all')" class="card status-box">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col">
                                    <small class="">@lang('site.total_tax')</small>
                                    <h3 class="card-title mb-0">{{$total_tax}}</h3>
                                    <p class="small text-muted mb-0">
                                         <i class="far {{ $stats_change['tax'] >= 0 ? 'text-success fa-long-arrow-alt-up' : 'text-danger fa-long-arrow-alt-down' }}"></i>
                                        <span class="{{ $stats_change['tax'] >= 0 ? 'text-success' : 'text-danger' }}">
    {{ abs($stats_change['tax']) }}%
</span>

                                    </p>
                                </div>
                                <div class="col d-flex justify-content-end barchart">
                                    <img src="{{asset('assets/images/svgs/bar-chart-navy.svg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif
        @if (auth()->user()->hasPermission('customers_read') || auth()->user()->hasRole('superadministrator'))
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="status-box-wrapper">
                    <a href="{{route('admin.customers.index')}}" title="@lang('site.view_all')" class="card status-box">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col">
                                    <small class="">@lang('site.total_customers')</small>
                                    <h3 class="card-title mb-0">{{$customersCount}}</h3>
                                    <p class="small text-muted mb-0">
                                       <i class="far {{ $stats_change['customers'] >= 0 ? 'text-success fa-long-arrow-alt-up' : 'text-danger fa-long-arrow-alt-down' }}"></i>
                                        <span class="{{ $stats_change['customers'] >= 0 ? 'text-success' : 'text-danger' }}">
    {{ abs($stats_change['customers']) }}%
</span>

                                    </p>
                                </div>
                                <div class="col d-flex justify-content-end barchart">
                                    <img src="{{asset('assets/images/svgs/bar-chart-navy.svg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="status-box-wrapper">
                    <a href="{{route('admin.factories.index')}}" title="@lang('site.view_all')" class="card status-box">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col">
                                    <small class="">@lang('site.total_enterprise_accounts')</small>
                                    <h3 class="card-title mb-0">{{$factoriesCount}}</h3>
                                    <p class="small text-muted mb-0">
                                        <i class="far {{ $stats_change['factories'] >= 0 ? 'text-success fa-long-arrow-alt-up' : 'text-danger fa-long-arrow-alt-down' }}"></i>
                                        <span class="{{ $stats_change['factories'] >= 0 ? 'text-success' : 'text-danger' }}">
    {{ abs($stats_change['factories']) }}%
</span>

                                    </p>
                                </div>
                                <div class="col d-flex justify-content-end barchart">
                                    <img src="{{asset('assets/images/svgs/bar-chart-navy.svg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif
        @if (auth()->user()->hasPermission('drivers_read') || auth()->user()->hasRole('superadministrator'))
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="status-box-wrapper">
                    <a href="{{route('admin.drivers.index')}}" title="@lang('site.view_all')" class="card status-box">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col">
                                    <small class="">@lang('site.total_drivers')</small>
                                    <h3 class="card-title mb-0">{{$driversCount}}</h3>
                                    <p class="small text-muted mb-0">
                                     <i class="far {{ $stats_change['drivers'] >= 0 ? 'text-success fa-long-arrow-alt-up' : 'text-danger fa-long-arrow-alt-down' }}"></i>
                                        <span class="{{ $stats_change['drivers'] >= 0 ? 'text-success' : 'text-danger' }}">
    {{ abs($stats_change['drivers']) }}%
</span>

                                    </p>
                                </div>
                                <div class="col d-flex justify-content-end barchart">
                                    <img src="{{asset('assets/images/svgs/bar-chart-navy.svg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif
        @if (auth()->user()->hasPermission('driverCompanies_read') || auth()->user()->hasRole('superadministrator'))
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="status-box-wrapper">
                    <a href="{{route('admin.companies.index')}}" title="@lang('site.view_all')" class="card status-box">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col">
                                    <small class="">@lang('site.total_shipping_companies')</small>
                                    <h3 class="card-title mb-0">{{$driversCompanyCount}}</h3>
                                    <p class="small text-muted mb-0">
                                        <i class="far {{ $stats_change['companies'] >= 0 ? 'text-success fa-long-arrow-alt-up' : 'text-danger fa-long-arrow-alt-down' }}"></i>
                                        <span class="{{ $stats_change['companies'] >= 0 ? 'text-success' : 'text-danger' }}">
    {{ abs($stats_change['companies']) }}%
</span>

                                    </p>
                                </div>
                                <div class="col d-flex justify-content-end barchart">
                                    <img src="{{asset('assets/images/svgs/bar-chart-navy.svg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif
        @if (auth()->user()->hasPermission('drivers_read') || auth()->user()->hasRole('superadministrator'))
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="status-box-wrapper">
                    <a href="{{route('admin.drivers.index',['online_drivers'=>'1'])}}" title="@lang('site.view_all')" class="card status-box">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col">
                                    <small class="">@lang('site.online_drivers')</small>
                                    <h3 class="card-title mb-0">{{$online_drivers}}</h3>
                                    <p class="small text-muted mb-0">
                                    <i class="far {{ $stats_change['onlineDrivers'] >= 0 ? 'text-success fa-long-arrow-alt-up' : 'text-danger fa-long-arrow-alt-down' }}"></i>
                                       <span class="{{ $stats_change['onlineDrivers'] >= 0 ? 'text-success' : 'text-danger' }}">
    {{ abs($stats_change['onlineDrivers']) }}%
</span>

                                    </p>
                                </div>
                                <div class="col d-flex justify-content-end barchart">
                                    <img src="{{asset('assets/images/svgs/bar-chart-navy.svg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif
        @if (auth()->user()->hasPermission('orders_read') || auth()->user()->hasRole('superadministrator'))
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="status-box-wrapper">
                    <a href="{{route('admin.orders.index',['status'=>'open'])}}" title="@lang('site.view_all')" class="card status-box">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col">
                                    <small class="">@lang('site.orders')</small>
                                    <h3 class="card-title mb-0">{{$ordersConut}}</h3>
                                    <p class="small text-muted mb-0">
                                         <i class="far {{ $stats_change['orders'] >= 0 ? 'text-success fa-long-arrow-alt-up' : 'text-danger fa-long-arrow-alt-down' }}"></i>
                                       <span class="{{ $stats_change['orders'] >= 0 ? 'text-success' : 'text-danger' }}">
    {{ abs($stats_change['orders']) }}%
</span>

                                    </p>
                                </div>
                                <div class="col d-flex justify-content-end barchart">
                                    <img src="{{asset('assets/images/svgs/bar-chart-navy.svg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif
        @if (auth()->user()->hasPermission('orders_read') || auth()->user()->hasRole('superadministrator'))
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="status-box-wrapper">
                    <a href="{{route('admin.orders.index',['status'=>'pending'])}}" title="@lang('site.view_all')" class="card status-box">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col">
                                    <small class="">@lang('site.pend_orders')</small>
                                    <h3 class="card-title mb-0">{{$pendingOrdersCount}}</h3>
                                    <p class="small text-muted mb-0">
                                       <i class="far {{ $stats_change['pendingOrders'] >= 0 ? 'text-success fa-long-arrow-alt-up' : 'text-danger fa-long-arrow-alt-down' }}"></i>
                                       <span class="{{ $stats_change['pendingOrders'] >= 0 ? 'text-success' : 'text-danger' }}">
    {{ abs($stats_change['pendingOrders']) }}%
</span>

                                    </p>
                                </div>
                                <div class="col d-flex justify-content-end barchart">
                                    <img src="{{asset('assets/images/svgs/bar-chart-navy.svg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="status-box-wrapper">
                    <a href="{{route('admin.orders.index',['status'=>'approve'])}}" title="@lang('site.view_all')" class="card status-box">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col">
                                    <small class="">@lang('site.aproved_orders')</small>
                                    <h3 class="card-title mb-0">{{$approveOrdersCount}}</h3>
                                    <p class="small text-muted mb-0">
                                       <i class="far {{ $stats_change['approvedOrders'] >= 0 ? 'text-success fa-long-arrow-alt-up' : 'text-danger fa-long-arrow-alt-down' }}"></i>
                                       <span class="{{ $stats_change['approvedOrders'] >= 0 ? 'text-success' : 'text-danger' }}">
    {{ abs($stats_change['approvedOrders']) }}%
</span>

                                    </p>
                                </div>
                                <div class="col d-flex justify-content-end barchart">
                                    <img src="{{asset('assets/images/svgs/bar-chart-navy.svg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="status-box-wrapper">
                    <a href="{{route('admin.orders.index',['status'=>'complete'])}}" title="@lang('site.view_all')" class="card status-box">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col">
                                    <small class="">@lang('site.complete_orders')</small>
                                    <h3 class="card-title mb-0">{{$completeOrderCount}}</h3>
                                    <p class="small text-muted mb-0">
                                       <i class="far {{ $stats_change['completeOrders'] >= 0 ? 'text-success fa-long-arrow-alt-up' : 'text-danger fa-long-arrow-alt-down' }}"></i>
                                       <span class="{{ $stats_change['completeOrders'] >= 0 ? 'text-success' : 'text-danger' }}">
    {{ abs($stats_change['completeOrders']) }}%
</span>

                                    </p>
                                </div>
                                <div class="col d-flex justify-content-end barchart">
                                    <img src="{{asset('assets/images/svgs/bar-chart-navy.svg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="status-box-wrapper">
                    <a href="{{route('admin.orders.index',['status'=>'cancel'])}}" title="@lang('site.view_all')" class="card status-box">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col">
                                    <small class="">@lang('site.cancel_orders')</small>
                                    <h3 class="card-title mb-0">{{$cancelOrderCount}}</h3>
                                    <p class="small text-muted mb-0">
                                        <i class="far {{ $stats_change['cancelOrders'] >= 0 ? 'text-success fa-long-arrow-alt-up' : 'text-danger fa-long-arrow-alt-down' }}"></i>
                                      <span class="{{ $stats_change['cancelOrders'] >= 0 ? 'text-success' : 'text-danger' }}">
    {{ abs($stats_change['cancelOrders']) }}%
</span>

                                    </p>
                                </div>
                                <div class="col d-flex justify-content-end barchart">
                                    <img src="{{asset('assets/images/svgs/bar-chart-navy.svg')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif
    </div>
    @if (auth()->user()->hasPermission('offers_read') || auth()->user()->hasRole('superadministrator'))
    <h2 class="section-title">@lang('site.last_offers')</h2>
    <table class="table datatables datatables-active" id="">
            <thead>
                <tr role="row">
                    <th>@lang('site.num')</th>
                    <th class="min-width-170">@lang('site.service_provider')</th>
                    <th>@lang('site.order_number')</th>
                    <th>@lang('site.price')</th>
                    <th>@lang('site.time')</th>
                    <th>@lang('site.status')</th>
                    <th>@lang('site.edit')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($offers as $index=>$offer )
                <tr>
                    <td>{{ $offer->id }}</td>
                    <td>
                        <div class="user-col">
                            <img src="{{ $offer->user?->userData?->image != '' ? asset($offer->user?->userData?->image) : asset('uploads/users/default.png') }}"
                                alt="{{ $offer->user?->name }}">
                            <span class="name">{{ $offer->user?->name }}</span>
                        </div>
                    </td>
                    <td><a href="{{ route('admin.orders.index', ['number' => $offer->order_id]) }}">{{ $offer->order_id }}</a></td>
                    <td>{{ $offer->price . ' ' . setting('currency_atr') }}</td>
                    <td>{{ $offer->created_at }}</td>
                    <td>
                        <span class="badge badge-pill
                        @if($offer->status =='pending' || $offer->status =='pend') badge-warning
                        @elseif($offer->status=='approve' || $offer->status=='pick_up' || $offer->status=='delivered') badge-primary
                        @elseif ($offer->status=='complete' ||  $offer->status=='completed') badge-success
                        @elseif ($offer->status=='cancel' || $offer->status=='cancelled') badge-danger @endif">
                            @if($offer->status=='pending' || $offer->status =='pend')
                                @lang('site.pend')
                            @elseif($offer->status=='approve')
                                @lang('site.approval')
                            @elseif($offer->status=='pick_up')
                                @lang('site.Pick Up')
                            @elseif($offer->status=='delivered')
                                @lang('site.Delivered')
                            @elseif($offer->status=='complete' ||  $offer->status=='completed')
                                @lang('site.completed')
                            @elseif($offer->status=='cancel' || $offer->status=='cancelled')
                                @lang('site.canceled')
                            @endif
                        </span>
                    </td>
                    <td>
                        <ul class="actions">
                            @if(auth()->user()->hasPermission('offers_read'))
                            <li>
                                <a href="{{ route('admin.offers.show', ['id' => $offer->id]) }}" title="@lang('site.show')" class="show">
                                    <i class="fad fa-eye"></i>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('offers_update') && ($offer->status!='complete' && $offer->status!='cancel'))
                            <li>
                                <a href="#" class="cancel"
                                   onclick="event.preventDefault();document.getElementById('cancel-form_{{$index}}').submit();">
                                    <i class="fad fa-times"></i>
                                </a>
                                <form action="{{route('admin.orders.changeOfferStatus',$offer->id)}}"
                                      method="POST" id="cancel-form_{{$index}}">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" id="status" name="status" value="cancel">
                                </form>
                            </li>
                            @endif
                        </ul>
                    </td>
                </tr>
                @endforeach
                @if($offers->count()==0)
                    <tr>
                        <td colspan="11" style="text-align: center !important;">@lang('site.no_records_found')</td>
                    </tr>
                @endif
            </tbody>
        </table>
    <div class="flex-center mt-4 mb-5">
        <a href="{{route('admin.offers.index')}}" title="@lang('site.all_offers')" class="btn btn-navy fe-14">@lang('site.all_offers')</a>
    </div>
    @endif
    @if (auth()->user()->hasPermission('orders_read') || auth()->user()->hasRole('superadministrator'))
    <h2 class="section-title">@lang('site.last_orders')</h2>
    <table class="table datatables datatables-active" id="">
        <thead>
            <tr>
                <th>@lang('site.num')</th>
                <th class="min-width-170">@lang('site.service_seeker')</th>
                <th>@lang('site.car')</th>
                <th>@lang('site.offers')</th>
                <th>@lang('site.total_price')</th>
                <th>@lang('site.transaction_status')</th>
                <th>@lang('site.status')</th>
                <th>@lang('site.edit')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $index => $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>
                        <div class="user-col">
                            <img src="{{ asset($order->user?->userData?->image ?: 'uploads/users/default.png') }}"
                                alt="{{ $order->user?->name }}">
                            <span class="name">{{ $order->user?->name }}</span>
                        </div>
                    </td>
                    <td>
                        <a href="#" data-toggle="modal" data-target="#truckModal_{{ $index }}">
                            {{ $order->car?->name_en }}
                        </a>
                    </td>
                    <td>
                        @if (!empty($order->offers))
                            <a href="#" class="act-btn" data-toggle="modal"
                                data-target="#offersModal_{{ $index }}">
                                <i class="fad fa-paper-plane"></i>
                            </a>
                        @endif
                    </td>
                    <td>{{ number_format($order->ton_price * $order->weight_ton, 2, '.', '') }}{{ ' ' . setting('currency_atr') }}
                    </td>
                    @php
                        $transactionStatus = $order->transaction?->status;
                        $transactionStatusKey = $transactionStatus ? strtolower(trim($transactionStatus)) : null;
                    @endphp
                    <td>
                        {{ $transactionStatusKey && \Illuminate\Support\Facades\Lang::has('site.' . $transactionStatusKey)
                            ? __('site.' . $transactionStatusKey)
                            : ($transactionStatus ?: __('site.Delayed Payment')) }}
                    </td>
                    <td>
                        <span
                            class="badge badge-pill
                            @if ($order->status == 'pend' || $order->status == 'pending') badge-warning
                            @elseif($order->status == 'approve' || $order->status == 'pick_up' || $order->status == 'delivered') badge-primary
                            @elseif ($order->status == 'complete' || $order->status == 'completed') badge-success
                            @elseif ($order->status == 'cancel' || $order->status == 'cancelled') badge-danger @endif">
                            @if ($order->status == 'pend' || $order->status == 'pending')
                                @lang('site.pend')
                            @elseif($order->status == 'approve')
                                @lang('site.approval')
                            @elseif($order->status == 'pick_up')
                                @lang('site.Pick Up')
                            @elseif($order->status == 'delivered')
                                @lang('site.Delivered')
                            @elseif($order->status == 'complete' || $order->status == 'completed')
                                @lang('site.completed')
                            @elseif($order->status == 'cancel' || $order->status == 'cancelled')
                                @lang('site.canceled')
                            @endif
                        </span>
                    </td>
                    <td>
                        <ul class="actions">
                            @if (auth()->user()->hasPermission('orders_read'))
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#orderModal_{{ $index }}"
                                        title="@lang('site.show')" class="show">
                                        <i class="fad fa-eye"></i>
                                    </a>
                                    {{-- <a href="{{route('admin.orders.show',$order->id)}}" --}}
                                    {{-- title="@lang('site.show')" class="show"> --}}
                                    {{-- <i class="fad fa-eye"></i> --}}
                                    {{-- </a> --}}
                                </li>
                            @endif
                            @if (auth()->user()->hasPermission('orders_update'))
                                <li><a href="{{ route('admin.orders.edit', $order->id) }}" title="@lang('site.edit')"
                                        class="show"><i class="fad fa-edit"></i></a></li>
                                @if ($order->status == 'cancel')
                                    <li>
                                        <a href="#" class="check"
                                            onclick="event.preventDefault();document.getElementById('pend-form_{{ $index }}').submit();">
                                            <i class="fad fa-check-double"></i>
                                        </a>
                                    </li>
                                    <form action="{{ route('admin.orders.changeStatus', $order->id) }}" method="POST"
                                        id="pend-form_{{ $index }}">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" id="status" name="status" value="pend">
                                    </form>
                                @endif
                                @if ($order->status != 'complete' && $order->status != 'cancel' && $order->status != 'pend')
                                    <li>
                                        <a href="#" class="success"
                                            onclick="event.preventDefault();document.getElementById('complete-form_{{ $index }}').submit();">
                                            <i class="fad fa-clipboard-check"></i>
                                        </a>
                                    </li>
                                    <form action="{{ route('admin.orders.changeStatus', $order->id) }}" method="POST"
                                        id="complete-form_{{ $index }}">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" id="status" name="status" value="complete">
                                    </form>
                                @endif
                                @if ($order->status != 'complete' && $order->status != 'cancel')
                                    <li>
                                        <a href="#" class="cancel"
                                            onclick="event.preventDefault();document.getElementById('cancel-form_{{ $index }}').submit();">
                                            <i class="fad fa-times"></i>
                                        </a>
                                    </li>
                                    <form action="{{ route('admin.orders.changeStatus', $order->id) }}" method="POST"
                                        id="cancel-form_{{ $index }}">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" id="status" name="status" value="cancel">
                                    </form>
                                @endif
                            @endif
                            @if (auth()->user()->hasPermission('orders_read'))
                                <li>
                                    <a href="#" class="show" title="@lang('site.order_track')"
                                        onclick="showModal({{ $order->pick_up_late }},{{ $order->pick_up_long }},{{ $order->drop_of_late }},{{ $order->drop_of_long }},{{ $order->serviceProvider?->userData?->latitude ?? '0' }},{{ $order->serviceProvider?->userData?->longitude ?? '0' }},{{ $order }});">
                                        <i class="fad fa-map-marked-alt"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="flex-center mt-4">
        <a href="{{route('admin.orders.index')}}" title="@lang('site.all_orders')" class="btn btn-navy fe-14">@lang('site.all_orders')</a>
    </div>
    @foreach ($orders as $index=>$order)
    <!-- Start driverTruck Modal -->
    <div class="modal fade" id="truckModal_{{$index}}" tabindex="-1" role="dialog" aria-labelledby="verticalModalTitle_{{$index}}"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
            <div class="modal-content fog-background">
                <div class="bring-to-front">
                    <div class="modal-header flex-center">
                        <h4 class="modal-title text-navy mb-0" id="verticalModalTitle_{{$index}}"></h4>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                <path id="Exclusion_23" data-name="Exclusion 23"
                                      d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                      transform="translate(-0.384 0.422)" fill="#d27979" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="max-width-70">
                            <div class="truck-box driver">
                                <img src="{{$order->car->image !='' ? asset($order->car->image) : asset('uploads/cars/default.png')}}">
                                <span class="name">
                                @if(app()->getLocale()=='ar')
                                        {{$order->car->name_ar}}
                                    @else
                                        {{$order->car->name_en}}
                                    @endif
                            </span>
                            </div>
                            <ul class="truck-info mt-4">
                                <li>
                                    <span>@lang('site.track_number')</span>
                                    <span>{{$order->track_number}}</span>
                                </li>
                                <li>
                                    <span>@lang('site.track_license_number')</span>
                                    <span>{{$order->track_license_number}}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End selectTruck Modal -->
    <!-- Start DriversOffers Modal -->
    <div class="modal fade" id="offersModal_{{$index}}" tabindex="-1" role="dialog" aria-labelledby="verticalModalTitle_{{$index}}"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
            <div class="modal-content fog-background">
                <div class="bring-to-front">
                    <div class="modal-header flex-center">
                        <h4 class="modal-title text-navy mb-0" id="verticalModalTitle_{{$index}}">@lang('site.offers')</h4>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                <path id="Exclusion_23" data-name="Exclusion 23"
                                      d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                      transform="translate(-0.384 0.422)" fill="#d27979" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if($order->offers->count())
                            <ul class="offers-list max-width-80">
                                @foreach ($order->offers as $offer)
                                    <li class="offer-item">
                                        <div class="flex-align-center">
                                            <a href="" class="flex-col-center">
                                                <img src="{{$offer->user->userData->image !='' ? asset($offer->user->userData->image) : asset('uploads/users/default.png')}}" class="avatar">
                                                <span class="rate flex-col-center">
                                                <span class="total">
                                                    @for($i = 0; $i < floor($offer->user->evaluates->avg('rate')); $i++)
                                                        <img src="{{asset('assets/images/svgs/star-fill.svg')}}" alt="">
                                                    @endfor
                                                    @for($i = 0; $i < 5 - ($offer->user->evaluates->avg('rate')); $i++)
                                                        <img src="{{asset('assets/images/svgs/star.svg')}}" alt="">
                                                    @endfor
                                                </span>
                                                <span class="brief">
                                                    {{$offer->user->evaluates->avg('rate')}}
                                                    <span>({{$offer->user->evaluates->count()}})</span>
                                                </span>
                                            </span>
                                            </a>
                                            @if(!empty($offer->driver->userData->car))
                                                <div class="flex-space">
                                                    <div class="flex-column">
                                                        <span class="name">{{$offer->user->name}}</span>
                                                        <ul>
                                                            <li>
                                                                <img src="{{asset('assets/images/svgs/map-marker.svg')}}" alt="">
                                                                <span class="val">
                                                            {{$offer->driver->userData->location}}
                                                        </span>
                                                            </li>
                                                            <li>
                                                                <img src="{{asset('assets/images/svgs/truck-fill.svg')}}" alt="">
                                                                @if(app()->getLocale()=='ar' )
                                                                    {{$offer->driver->userData->car->name_ar}}
                                                                @else
                                                                    {{$offer->driver->userData->car->name_en}}
                                                                @endif
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <h4 class="offer-cost special mb-0">
                                                        {{$offer->price}}
                                                        {{setting('currency_atr') != '' ? ' '. setting('currency_atr') : ''}}
                                                    </h4>
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="no-offer flex-col-center max-width-80">
                                <img src="{{asset('assets/images/no-data.png')}}" alt="">
                                <h4>There are no offers has been found for this order yet</h4>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End DriversOffers Modal -->
    <div class="modal fade order-modal" id="orderModal_{{ $index }}" tabindex="-1" role="dialog"
            aria-labelledby="verticalModalTitle_{{ $index }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content yellow-background border-0">
                    <div class="modal-header border-bottom-0 flex-space">
                        <h4 class="modal-title mb-0" id="verticalModalTitle_{{ $index }}">@lang('site.order')
                            {{ ' # ' . $order->id }}</h4>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                <path id="Exclusion_23" data-name="Exclusion 23"
                                    d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                    transform="translate(-0.384 0.422)" fill="#d27979" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row col-gap-70 mb-4">
                            @if ($order->serviceProvider)
                                <div class="col-lg-6 col-12 pt-lg-3 pt-0 mb-lg-0 mb-4">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-12 col-sm-6 col-12 mb-lg-3 mb-md-0 mb-3">
                                            <div class="participant">
                                                <h5 class="title mb-4">@lang('site.customer')/@lang('site.' . $order->user->type . '')</h5>
                                                <div class="flex-align-center">
                                                    <img src="{{ asset($order->user?->userData?->image ?: 'uploads/users/default.png') }}"
                                                        alt="{{ $order->user?->name }}">
                                                    <div class="flex-column">
                                                        <span class="name">{{ $order->user?->name }}</span>
                                                        <span>@lang('site.id') : {{ $order->user?->id }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-12 col-sm-6 col-12">
                                            <div class="participant">
                                                <h5 class="title mb-4">
                                                    @lang('site.driver')/
                                                    @if ($order->offer)
                                                        @lang('site.' . $order->offer->user->type . '')
                                                    @else
                                                        @lang('site.' . $order->serviceProvider->type . '')
                                                    @endif
                                                </h5>
                                                <div class="flex-align-center">
                                                    <img src="{{ $order->serviceProvider &&
                                                    $order->serviceProvider->userData &&
                                                    $order->serviceProvider->userData->image != ''
                                                        ? asset($order->serviceProvider->userData->image)
                                                        : asset('uploads/users/default.png') }}"
                                                        alt="{{ $order->serviceProvider->name }}">

                                                    <div class="flex-column">
                                                        <span class="name">{{ $order->serviceProvider->name }}</span>
                                                        <span>@lang('site.id') : {{ $order->serviceProvider->id }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="row">
                                        <div class="col-md-6 col-12 mb-md-0 mb-3">
                                            <ul class="order-details">
                                                <li>
                                                    <img src="{{ asset('assets/images/svgs/map-marker-solid.svg') }}"
                                                        alt="">
                                                    Sharjah
                                                </li>
                                                <li>
                                                    <img src="{{ asset('assets/images/svgs/dumbbells.svg') }}"
                                                        alt="">
                                                    {{ $order->car?->frames }} @lang('site.axes')
                                                </li>
                                                <li>
                                                    <img src="{{ asset('assets/images/svgs/weight-solid.svg') }}"
                                                        alt="">
                                                    {{ $order->car?->weight }} @lang('site.weight')
                                                </li>
                                                <li>
                                                    {{ app()->getLocale() == 'ar' ? $order->car?->name_ar : $order->car?->name_en }}
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="flex-center h-100">
                                                <img src="{{ $order->car && $order->car->image != '' ? asset($order->car->image) : asset('uploads/cars/default.png') }}"
                                                    alt="@lang('site.order')" class="truck-preview">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (!$order->serviceProvider)
                                <div class="col-lg-6 col-12">
                                    <div class="row">
                                        <div class="col-md-6 col-12 mb-md-0 mb-3">
                                            <ul class="order-details">
                                                <li>
                                                    <img src="{{ asset('assets/images/svgs/map-marker-solid.svg') }}"
                                                        alt="">
                                                    Sharjah
                                                </li>
                                                <li>
                                                    <img src="{{ asset('assets/images/svgs/dumbbells.svg') }}"
                                                        alt="">
                                                    {{ $order->car?->frames }} @lang('site.axes')
                                                </li>
                                                <li>
                                                    <img src="{{ asset('assets/images/svgs/weight-solid.svg') }}"
                                                        alt="">
                                                    {{ $order->car?->weight }} @lang('site.weight')
                                                </li>
                                                <li>
                                                    {{ app()->getLocale() == 'ar' ? $order->car?->name_ar : $order->car?->name_en }}
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="flex-center h-100">
                                                <img src="{{ $order->car && $order->car->image != '' ? asset($order->car->image) : asset('uploads/cars/default.png') }}"
                                                    alt="" class="truck-preview">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row col-gap-70 mb-4">
                            <div class="col-lg-6 col-12 mb-lg-0 mb-4">
                                <div class="order-path mb-4">
                                    <img src="{{ asset('assets/images/order-path-1.png') }}" alt="">
                                    <span class="flex-column">
                                        <span class="from gray">
                                            <span>{{ $order->pick_up_address }}</span>
                                            <img src="{{ asset('assets/images/svgs/check-circle-gray.svg') }}"
                                                alt="">
                                        </span>
                                        <span class="to navy">
                                            <span>{{ $order->drop_of_address }}</span>
                                            <img src="{{ asset('assets/images/svgs/check-circle-navy.svg') }}"
                                                alt="">
                                        </span>
                                    </span>
                                </div>
                                <div class="table-responsive shipment-datetime">
                                    <table class="">
                                        <thead>
                                            <tr>
                                                <th>@lang('site.date')</th>
                                                <th>@lang('site.time')</th>
                                                <th>@lang('site.expected_ton_price')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ date('Y-m-d', strtotime($order->shipping_date)) }}</td>
                                                <td>{{ date('h:i A', strtotime($order->shipping_date)) }}</td>
                                                <td>{{ $order->ton_price }}
                                                    {{ setting('currency_atr') != '' ? '' . setting('currency_atr') : '' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="order-history">
                                    <h4>@lang('site.history')</h4>
                                    @php
                                        $statusTimes = [];
                                        foreach ($order->statuses?->sortBy('created_at') ?? [] as $stat) {
                                            $key = strtolower($stat->status);
                                            if (!isset($statusTimes[$key])) {
                                                $statusTimes[$key] = $stat->created_at;
                                            }
                                        }
                                        $currentStatus = strtolower($order->status ?? '');
                                        $reached = function (array $aliases) use ($currentStatus) {
                                            return in_array($currentStatus, array_map('strtolower', $aliases), true);
                                        };
                                        $firstTime = function (array $aliases) use ($statusTimes) {
                                            foreach ($aliases as $alias) {
                                                $key = strtolower($alias);
                                                if (isset($statusTimes[$key])) {
                                                    return $statusTimes[$key];
                                                }
                                            }
                                            return null;
                                        };
                                        $isCanceled = $reached(['cancel', 'cancelled']);
                                    @endphp
                                    <ul>
                                        <li class="step completed">
                                            <span class="title">@lang('site.Create Order')</span>
                                            <span class="time">{{ date('h:i A', strtotime($order->created_at)) }}</span>
                                            <span class="checkmark"></span>
                                        </li>
                                        <li class="step @if ($reached(['approve','approved','pick_up','pickup','delivered','complete','completed','cancel','cancelled'])) completed @endif">
                                            <span class="title">@lang('site.Accept Offer')</span>
                                            <span class="time">
                                                @if ($time = $firstTime(['approve','approved']))
                                                    {{ date('h:i A', strtotime($time)) }}
                                                @else
                                                    --
                                                @endif
                                            </span>
                                            <span class="checkmark"></span>
                                        </li>
                                        <li class="step @if ($reached(['pick_up','pickup','delivered','complete','completed'])) completed @endif">
                                            <span class="title">@lang('site.Driver Pick Up')</span>
                                            <span class="time">
                                                @if ($time = $firstTime(['pick_up','pickup']))
                                                    {{ date('h:i A', strtotime($time)) }}
                                                @else
                                                    --
                                                @endif
                                            </span>
                                            <span class="checkmark"></span>
                                        </li>
                                        <li class="step @if ($reached(['delivered','complete','completed'])) completed @endif">
                                            <span class="title">@lang('site.Driver Delivered')</span>
                                            <span class="time">
                                                @if ($time = $firstTime(['delivered']))
                                                    {{ date('h:i A', strtotime($time)) }}
                                                @else
                                                    --
                                                @endif
                                            </span>
                                            <span class="checkmark"></span>
                                        </li>
                                        <li class="step @if ($reached(['complete','completed'])) completed @endif">
                                            <span class="title">@lang('site.Order Completed')</span>
                                            <span class="time">
                                                @if ($time = $firstTime(['complete','completed']))
                                                    {{ date('h:i A', strtotime($time)) }}
                                                @else
                                                    --
                                                @endif
                                            </span>
                                            <span class="checkmark"></span>
                                        </li>
                                        @if ($isCanceled)
                                            <li class="step @if ($isCanceled) completed @endif">
                                                <span class="title">@lang('site.Order Cancel')</span>
                                                <span class="time">
                                                    @if ($time = $firstTime(['cancel','cancelled']))
                                                        {{ date('h:i A', strtotime($time)) }}
                                                    @else
                                                        --
                                                    @endif
                                                </span>
                                                <span class="checkmark"></span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row col-gap-70">
                            <div class="col-lg-6 col-12 mb-lg-0 mb-4">
                                <ul class="shipment-details">
                                    <li class="flex-space">
                                        <span class="title">@lang('site.shipment_type')</span>
                                        <span>{{ app()->getLocale() == 'ar' ? $order->shipmentType?->name_ar ?? '-' : $order->shipmentType?->name_en ?? '-' }}</span>
                                    </li>
                                </ul>
                                @if (!empty($order->shipment_details))
                                    <ul class="shipment-details">
                                        <li class="flex-space">
                                            <span class="title">@lang('site.shipment_details')</span>
                                            <span>{{ $order->shipment_details }}</span>
                                        </li>
                                    </ul>
                                @endif
                                <ul class="extra-precautions my-4">
                                    @if ($order->spoil_quickly == 1)
                                        <li>@lang('site.spoil_quickly')</li>
                                    @endif
                                    @if ($order->breakable == 1)
                                        <li>@lang('site.breakable')</li>
                                    @endif
                                </ul>
                                <ul class="shipment-details">
                                    <li class="flex-space">
                                        <span class="title">@lang('site.Shipment Size')</span>
                                        <span>{{ $order->size }}</span>
                                    </li>
                                    <li class="flex-space">
                                        <span class="title">@lang('site.weight')</span>
                                        <span>{{ $order->weight_ton }} @lang('site.ton')</span>
                                    </li>
                                    <li class="flex-space">
                                        <span class="title">@lang('site.total_price')</span>
                                        <span>{{ $order->total_price != 0.0 ? number_format($order->total_price, 2, '.', '') : number_format($order->ton_price * $order->weight_ton, 2, '.', '') }}
                                            {{ ' ' . setting('currency_atr') }}</span>
                                    </li>
                                    @if ($order->accountant && $order->accountant->fine != '')
                                        <li class="flex-space">
                                            <span class="title">@lang('site.fine')</span>
                                            <span>{{ $order->accountant->fine }}
                                                {{ ' ' . setting('currency_atr') }}</span>
                                        </li>
                                    @endif
                                    @if ($order->accountant)
                                        <li class="flex-space">
                                            <span class="title">@lang('site.service_seeker_fee')</span>
                                            <span>{{ $order->accountant->service_seeker_fee }}
                                                {{ ' ' . setting('currency_atr') }}</span>
                                        </li>
                                    @endif
                                    <li class="flex-space">
                                        <span class="title">@lang('site.payment_method')</span>
                                        <span>{{ $order->paymentType?->name ?? '-' }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-6 col-12">
                                @if ($order->accountant)
                                    <ul class="shipment-details">
                                        <li class="flex-space">
                                            <span class="title">@lang('site.service_provider_commission')</span>
                                            <span>{{ $order->accountant->service_provider_commission }} %</span>
                                        </li>
                                        <li class="flex-space">
                                            <span class="title">@lang('site.operating_costs')</span>
                                            <span>{{ $order->accountant->operating_costs }}
                                                {{ ' ' . setting('currency_atr') }}</span>
                                        </li>
                                        <li class="flex-space">
                                            <span class="title">@lang('site.expenses')</span>
                                            <span>{{ $order->accountant->expenses }}
                                                {{ ' ' . setting('currency_atr') }}</span>
                                        </li>
                                        <li class="flex-space">
                                            <span class="title"> @lang('site.service_provider_amount')</span>
                                            <span>{{ $order->accountant->service_provider_amount }}
                                                {{ ' ' . setting('currency_atr') }}</span>
                                        </li>
                                    </ul>
                                    @if ($order->offer)
                                        <div class="order-summary my-4">
                                            <h4>@lang('site.checkout_summary')</h4>
                                            <ul class="mb-3">
                                                <li>
                                                    <span>@lang('site.shipping_price') (
                                                        {{ app()->getLocale() == 'ar' ? setting('app_name_ar') : setting('app_name_en') }})</span>
                                                    <span>{{ $order->offer->price ?? 0.0 }}
                                                        {{ ' ' . setting('currency_atr') }}</span>
                                                </li>
                                                <li>
                                                    <span>@lang('site.vat')</span>
                                                    <span>{{ $order->offer->sub_total - $order->offer->price ?? 0.0 }}
                                                        {{ ' ' . setting('currency_atr') }}</span>
                                                </li>
                                            </ul>
                                            <div class="flex-space subtotal">
                                                <span class="flex-align-center">
                                                    <img src="{{ asset('assets/images/svgs/money-circle.svg') }}"
                                                        alt="">
                                                    @lang('site.sub_total')
                                                </span>
                                                <span class="amount">{{ $order->offer->sub_total ?? 0.0 }}
                                                    {{ ' ' . setting('currency_atr') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                @if ($order->serviceProvider)
                                    @php
                                        $driver = $order->serviceProvider;
                                        $driverStats = $driver->evaluationsReceived()
                                            ->selectRaw('COALESCE(AVG(rate),0) as avg_rate, COUNT(*) as total_rates')
                                            ->first();
                                        $driverRating = round($driverStats?->avg_rate ?? 0, 2);
                                        $driverRatingCount = $driverStats?->total_rates ?? 0;
                                        $driverImage = $driver->userData && $driver->userData->image != '' ? asset($driver->userData->image) : asset('uploads/users/default.png');
                                        $driverPhone = $driver->phone ?? $driver->userData?->phone ?? '';
                                        $driverEmail = $driver->email ?? '';
                                        $driverRateRoute = null;
                                        if (Route::has('admin.drivers.evaluate') && $driver->type == 'driver') {
                                            $driverRateRoute = route('admin.drivers.evaluate', $driver->id);
                                        } elseif (Route::has('admin.companies.evaluate') && $driver->type == 'driversCompany') {
                                            $driverRateRoute = route('admin.companies.evaluate', $driver->id);
                                        } elseif (Route::has('admin.factories.evaluate') && $driver->type == 'factory') {
                                            $driverRateRoute = route('admin.factories.evaluate', $driver->id);
                                        }
                                    @endphp
                                    <div class="driver-card">
                                        <div class="flex-align-center">
                                            <img src="{{ $driverImage }}" alt="{{ $driver->name }}">
                                            <div class="flex-column">
                                                <span class="name">{{ $driver->name }}</span>
                                                @if ($driverRateRoute)
                                                    <a href="{{ $driverRateRoute }}" class="rate">
                                                        <div class="flex-align-center">
                                                            @for ($star = 1; $star <= 5; $star++)
                                                                <img src="{{ asset($star <= round($driverRating) ? 'assets/images/svgs/star-fill.svg' : 'assets/images/svgs/star.svg') }}"
                                                                    alt="">
                                                            @endfor
                                                        </div>
                                                        <span class="value">{{ number_format($driverRating, 2) }}</span>
                                                        <span class="count">({{ $driverRatingCount }})</span>
                                                    </a>
                                                @else
                                                    <span class="rate">
                                                        <div class="flex-align-center">
                                                            @for ($star = 1; $star <= 5; $star++)
                                                                <img src="{{ asset($star <= round($driverRating) ? 'assets/images/svgs/star-fill.svg' : 'assets/images/svgs/star.svg') }}"
                                                                    alt="">
                                                            @endfor
                                                        </div>
                                                        <span class="value">{{ number_format($driverRating, 2) }}</span>
                                                        <span class="count">({{ $driverRatingCount }})</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="driver-actions">
                                            <a href="{{ $driverPhone ? 'tel:' . $driverPhone : '#' }}">
                                                <img src="{{ asset('assets/images/svgs/call.svg') }}" alt="">
                                                Call
                                            </a>
                                            <a href="{{ $driverEmail ? 'mailto:' . $driverEmail : '#' }}">
                                                <img src="{{ asset('assets/images/svgs/message.svg') }}" alt="">
                                                Message
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <!-- Start OrderTracking Modal -->
    <div class="modal fade" id="TrackingModal" tabindex="-1" role="dialog" aria-labelledby="TrackingModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
            <div class="modal-content fog-background">
                <div class="bring-to-front">
                    <div class="modal-header flex-center">
                        <h4 class="modal-title text-navy mb-0">@lang('site.order_tracking')</h4>
                        <button type="button" id="TrackingModalClose" class="btn-close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                <path id="Exclusion_23" data-name="Exclusion 23"
                                      d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                      transform="translate(-0.384 0.422)" fill="#d27979" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body position-relative px-0 pb-0">
                        <div class="flex-column max-width-60 px-md-0 px-3 mb-4">
                            <div class="tracking-path">
                                <img src="{{asset('assets/images/track-path.png')}}" alt="">
                                <span class="flex-column">
                                    <span class="from">
                                        <span id="from">UAE ,Sharjah ,Jaber Bin Abdallah st</span>
                                    </span>
                                    <span class="to">
                                        <span id="to" >UAE ,Sharjah ,Jaber Bin Abdallah st</span>
                                    </span>
                                </span>
                            </div>
                            <ul class="tracking-informations">
                                <li>
                                    <img src="{{asset('assets/images/svgs/time-check.svg')}}" alt="">
                                    Estimated Time:&nbsp;<span id="duration"></span>
                                </li>
                                <li>
                                    <img src="{{asset('assets/images/svgs/road.svg')}}" alt="">
                                    Road Distance:&nbsp;<span id="distance" ></span>
                                </li>
                            </ul>
                        </div>
                        <div id="map" class="map" style="height: 460px; width: 100%;"></div>
                        <div class="flex-center">
                            <a href="#" class="btn btn-navy shadow-none" data-dismiss="modal" aria-label="Close">Back To Order</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
@section('scripts')
<!-- DateRangePicker JS -->
<script src="{{asset('assets/tiny/js/daterangepicker.js')}}"></script>
<!-- Data Tables -->
<script src='{{asset('assets/tiny/js/jquery.dataTables.min.js')}}'></script>
<script src='{{asset('assets/tiny/js/dataTables.bootstrap4.min.js')}}'></script>
<script>
    $('.datatables-active').DataTable({
        info: false,
        paging: false,
        searching: false,
        autoWidth: true,
        "bLengthChange": false,
    });
    const container = document.querySelector('.dataTables_wrapper .row:nth-child(2) [class*="col-"]');
    container.addEventListener('wheel', (event) => {
        if (container.scrollLeft === 0 && event.deltaY < 0) {
            return;
        } else if (container.scrollLeft === container.scrollWidth - container.clientWidth && event.deltaY > 0) {
            return;
        }
        event.preventDefault();
        container.scrollLeft += event.deltaY;
    });
</script>
<script>
    let req_start = "{{request()->start_date}}";
    let req_end = "{{request()->end_date}}";
    var start = moment().subtract(29, 'days');
    var end = moment();
    function cb(start, end) {
        if (req_start != '' && req_start != null && req_end != '' && req_end != null) {
            $('#reportrange span').html(start.format(req_start) + ' - ' + end.format(req_end));
        } else {
            $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        }
    }
    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
        $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
        $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));

        document.getElementById("changeStatistics").submit();
    });
    $('[data-range-key="Custom Range"]').html('Translated text');
    if (req_start != '' && req_start != null && req_end != '' && req_end != null) {
        $('#reportrange').daterangepicker({
            showDropdowns: true,
            startDate: moment(req_start).local(),
            endDate: moment(req_end).local(),
            ranges:
                {
                    "@lang('site.Today')": [moment(), moment()],
                    "@lang('site.Yesterday')": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    "@lang('site.Last 7 Days')": [moment().subtract(6, 'days'), moment()],
                    "@lang('site.Last 30 Days')": [moment().subtract(29, 'days'), moment()],
                    "@lang('site.This Month')": [moment().startOf('month'), moment().endOf('month')],
                    "@lang('site.Last Month')": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                }
        }, cb);
    } else {
        $('#reportrange').daterangepicker({
            showDropdowns: true,
            startDate: start,
            endDate: end,
            ranges:
                {
                    "@lang('site.Today')": [moment(), moment()],
                    "@lang('site.Yesterday')": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    "@lang('site.Last 7 Days')": [moment().subtract(6, 'days'), moment()],
                    "@lang('site.Last 30 Days')": [moment().subtract(29, 'days'), moment()],
                    "@lang('site.This Month')": [moment().startOf('month'), moment().endOf('month')],
                    "@lang('site.Last Month')": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                }
        }, cb);
    }
    cb(start, end);
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJf7AnrqzR7AVTi2uFIrT9UTfF6dLRqEM&callback=initMap&libraries=places&language={{app()->getLocale()}}">
</script>
<script>
    /*============= Google Map Scripts =============*/
    $('#TrackingModalClose, a[data-dismiss="modal"][aria-label="Close"]').on('click',function(e){
        e.preventDefault();
        $('#TrackingModal').modal('hide');
    });
    let map,map1,map2, activeInfoWindow, markers = [];
    var directionsService, directionsDisplay ;
    function drawPath(directionsService, directionsDisplay,start,end) {
        directionsService.route({
            origin: start,
            destination: end,
            optimizeWaypoints: true,
            travelMode:window.google.maps.DirectionsTravelMode.WALKING
        }, function(response, status) {
            if (status === 'OK') {
                directionsDisplay.setDirections(response);
                var infowindow = new window.google.maps.InfoWindow({
                    content: "@lang('site.drop_of_address')<br>"+" " +response.routes[0].legs[0].distance.text})
            } else {
                alert('Problem in showing direction due to ' + status);
            }
        });
    }

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: {
                lat: 30.036053390817127,
                lng: 31.236625493518176,
            },
            zoom: 16,
            disableDefaultUI: true,
            mapTypeId:'terrain'
        });
    }

    var previousMarker;
    function showModal(plat,plng,dlat,dlng,drlat,drlng,order){
        let order1=order;
        $('#TrackingModal').modal('show');

        // initMap();
        if(directionsDisplay != null) {
            directionsDisplay.setMap(null);
            directionsDisplay = null;
        }
        directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer;
        directionsDisplay.setMap(map);
        directionsDisplay.setOptions({
            polylineOptions: {
                strokeColor: 'red'
            }
        });
        let mylatelng={ lat:parseFloat(plat), lng:parseFloat(plng)};
        let myLatlng1={ lat:parseFloat(dlat), lng:parseFloat(dlng)};

        drawPath(directionsService, directionsDisplay,mylatelng,myLatlng1);
        if(drlat !=0 && drlng !=0){
            let myLatlng2={ lat:parseFloat(drlat), lng:parseFloat(drlng)};
            //console.log('myLatlng2',myLatlng2);
            if (previousMarker && previousMarker.setMap) {
                previousMarker.setMap(null);
            }
            previousMarker = new google.maps.Marker({
                position: myLatlng2,
                map,
                title: "@lang('site.driver')",
                animation: google.maps.Animation.BOUNCE,
                draggable: true,

            });
            var infowindow = new google.maps.InfoWindow({
                content:
                    "<h5  >{{__('site.order_number')}} : <span style='color:red;'>"+order1.id+"</span></h5>"+
                    "<h5  >{{__('site.customer')}} : <span style='color:red;'>"+order1.user.name+"</span></h5>"+
                    "<h5  >{{__('site.driver')}} : <span style='color:red;'>"+ order1.service_provider.name+"</span></h5>"+""
            });
            infowindow.open(map,previousMarker);
            map.addListener("center_changed", () => {
                // 3 seconds after the center of the map has changed, pan back to the
                // marker.
                window.setTimeout(() => {
                    map.panTo(previousMarker.getPosition());
                }, 3000);
            });

        }
            // set  pick up addraess & drop fo addraess
            $('#from').html(order.pick_up_address)
            $('#to').html(order.drop_of_address)
            // set destance & duration
            getDisAndDur({'lat':plat,'long':plng},{'lat':dlat,'long':dlng})
    }

    function getDisAndDur(ori,dest){
        var origin = new google.maps.LatLng(ori.lat, ori.long);
        var destination = new google.maps.LatLng(dest.lat, dest.long);
        // define spacing matrix options
        var options = {
            origins: [origin],
            destinations: [destination],
            travelMode: 'DRIVING',
            unitSystem:  google.maps.UnitSystem.METRIC
        };
        // define spacing matrix using DistanceMatrixService()
        var service = new google.maps.DistanceMatrixService();
        service.getDistanceMatrix(options, function(response, status) {
            if (status == 'OK') {
                var distance = response.rows[0].elements[0].distance.text ;
                var duration = response.rows[0].elements[0].duration.text;
                $("#distance").html(distance)
                console.log('Distance: ' + distance);
                $("#duration").html(duration)
                console.log('Duration: ' + duration);
            } else {
                console.log('Error: ' + ori +' '+ dest + ' ' +status);
            }
        });
    }
</script>
@endsection
