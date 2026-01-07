@extends('layouts.admin.app')
@section('title',' | ' . __('site.show') . ' '. __('site.the_order'))
@section('styles')
    <style>
        .order-cancelled-banner {
            background: #f2d447;
            border-radius: 14px;
            color: #d07c7c;
            font-size: 18px;
            font-weight: 600;
            padding: 20px 16px;
            text-align: center;
            width: 100%;
        }
    </style>
@endsection
@section('content')
    <h2 class="section-title mb-4">@lang('site.order') {{' # '.$order->id}}</h2>
    <div class="row col-gap-70 mb-4">
        @if($order->serviceProvider)
            <div class="col-lg-6 col-12 pt-lg-3 pt-0 mb-lg-0 mb-4">
                <div class="row">
                    <div class="col-xl-6 col-lg-12 col-sm-6 col-12 mb-lg-3 mb-md-0 mb-3">
                        <div class="participant">
                            <h5 class="title mb-4">@lang('site.Customer Type')/@lang('site.'.$order->user->type.'')</h5>
                            <div class="flex-align-center">
                                <img
                                    src="{{$order->user->userData->image !='' ? asset($order->user->userData->image) : asset('uploads/users/default.png')}}"
                                    alt="{{$order->user->name}}">
                                <div class="flex-column">
                                    <span class="name">{{$order->user->name}}</span>
                                    <span>@lang('site.id') : {{$order->user->id}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-12 col-sm-6 col-12">
                        <div class="participant">
                            <h5 class="title mb-4">
                                @lang('site.Driver Type')/
                                @if($order->offer)
                                    @lang('site.'.$order->offer->user->type.'')
                                @else
                                    @lang('site.'.$order->serviceProvider->type.'')
                                @endif
                            </h5>
                            <div class="flex-align-center">
                                <img
                                    src="{{$order->serviceProvider->userData->image !='' ? asset($order->serviceProvider->userData->image) : asset('uploads/users/default.png')}}"
                                    alt="{{$order->serviceProvider->name}}">
                                <div class="flex-column">
                                    <span class="name">{{$order->serviceProvider->name}}</span>
                                    <span>@lang('site.id') : {{$order->serviceProvider->id}}</span>
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
                                <img src="{{asset('assets/images/svgs/map-marker-solid.svg')}}" alt="">
                                @lang('site.sharjah')
                            </li>
                            <li>
                                <img src="{{asset('assets/images/svgs/dumbbells.svg')}}" alt="">
                                {{$order->car->frames}} @lang('site.axes')
                            </li>
                            <li>
                                <img src="{{asset('assets/images/svgs/weight-solid.svg')}}" alt="">
                                {{$order->car->weight}} @lang('site.weight')
                            </li>
                            <li>
                                {{app()->getLocale()=='ar' ? $order->car->name_ar : $order->car->name_en}}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="flex-center h-100">
                            <img
                                src="{{$order->car->image !='' ? asset($order->car->image) : asset('uploads/cars/default.png')}}"
                                alt="@lang('site.order')" class="truck-preview">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(!$order->serviceProvider)
            <div class="col-lg-6 col-12">
                <div class="row">
                    <div class="col-md-6 col-12 mb-md-0 mb-3">
                        <ul class="order-details">
                            <li>
                                <img src="{{asset('assets/images/svgs/map-marker-solid.svg')}}" alt="">
                                {{$order->pick_up_address}}
                            </li>
                            <li>
                                <img src="{{asset('assets/images/svgs/dumbbells.svg')}}" alt="">
                                {{$order->car->frames}} @lang('site.axes')
                            </li>
                            <li>
                                <img src="{{asset('assets/images/svgs/weight-solid.svg')}}" alt="">
                                {{$order->car->weight}} @lang('site.weight')
                            </li>
                            <li>
                                {{app()->getLocale()=='ar' ? $order->car->name_ar : $order->car->name_en}}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="flex-center h-100">
                            <img
                                src="{{$order->car->image !='' ? asset($order->car->image) : asset('uploads/cars/default.png')}}"
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
                <img src="{{asset('assets/images/order-path-1.png')}}" alt="">
                <span class="flex-column">
                                <span class="from gray">
                                    <span>{{$order->pick_up_address}}</span>
                                    <img src="{{asset('assets/images/svgs/check-circle-gray.svg')}}" alt="">
                                </span>
                                <span class="to navy">
                                    <span>{{$order->drop_of_address}}</span>
                                    <img src="{{asset('assets/images/svgs/check-circle-navy.svg')}}" alt="">
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
                        <td>{{date("Y-m-d",strtotime($order->shipping_date))}}</td>
                        <td>{{date("h:i A",strtotime($order->shipping_date))}}</td>
                        <td>{{$order->ton_price}} {{setting('currency_atr') !='' ? ''. setting('currency_atr') : ''}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="order-history">
                <h4>@lang('site.history')</h4>
                <ul>
                    <li class="step completed">
                        <span class="title">@lang('site.Create Order')</span>
                        <span class="time">{{date("h:i A",strtotime($order->created_at))}}</span>
                        <span class="checkmark"></span>
                    </li>
                    <li class="step @if($order->status =='approve' ||
                                        $order->status=='pick_up' ||
                                        $order->status=='delivered' ||
                                        $order->status=='complete'||
                                        $order->status=='cancel') completed @endif">
                        <span class="title">@lang('site.Accept Offer')</span>
                        <span class="time">
                                            @php $counter =1 @endphp
                            @forelse ($order->statuses as $stat )
                                @if($stat->status=='approve' && $counter==1)
                                    {{date("h:i A",strtotime($stat->created_at))}}
                                    @php $counter +=1 @endphp
                                @endif
                            @empty
                                --
                            @endforelse
                                        </span>
                        <span class="checkmark"></span>
                    </li>
                    <li class="step @if($order->status =='pick_up' ||
                                        $order->status=='delivered' ||
                                        $order->status=='complete') completed @endif">
                        <span class="title">@lang('site.Driver Pick Up')</span>
                        <span class="time">
                                            @php $counter =1 @endphp
                            @forelse ($order->statuses as $stat )
                                @if($stat->status=='pick_up' && $counter==1)
                                    {{date("h:i A",strtotime($stat->created_at))}}
                                    @php $counter +=1 @endphp
                                @endif
                            @empty
                                --
                            @endforelse
                                        </span>
                        <span class="checkmark"></span>
                    </li>
                    <li class="step @if($order->status =='delivered' ||
                                        $order->status=='complete' ) completed @endif">
                        <span class="title">@lang('site.Driver Delivered')</span>
                        <span class="time">
                                            @php $counter =1 @endphp
                            @forelse ($order->statuses as $stat )
                                @if($stat->status=='delivered' && $counter==1)
                                    {{date("h:i A",strtotime($stat->created_at))}}
                                    @php $counter +=1 @endphp
                                @endif
                            @empty
                                --
                            @endforelse
                                        </span>
                        <span class="checkmark"></span>
                    </li>
                    <li class="step @if($order->status =='complete' ) completed @endif">
                        <span class="title">@lang('site.Order Completed')</span>
                        <span class="time">
                                            @php $counter =1 @endphp
                            @forelse ($order->statuses as $stat )
                                @if($stat->status=='complete' && $counter==1)
                                    {{date("h:i A",strtotime($stat->created_at))}}
                                    @php $counter +=1 @endphp
                                @endif
                            @empty
                                --
                            @endforelse
                                        </span>
                        <span class="checkmark"></span>
                    </li>
                    @if($order->status=='cancel')
                        <li class="step @if($order->status =='cancel' ) completed @endif">
                            <span class="title">@lang('site.Order Cancel')</span>
                            <span class="time">
                                            @php $counter =1 @endphp
                                @forelse ($order->statuses as $index=>$stat)
                                    @if($stat->status=='cancel' && $index <1 && $counter==1)
                                        {{date(" h:i A",strtotime($stat->created_at))}}
                                        @php $counter +=1 @endphp
                                    @endif
                                @empty
                                    --
                                @endforelse
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
                    <span>{{app()->getLocale()=='ar' ? $order->shipmentType->name_ar : $order->shipmentType->name_en }}</span>
                </li>
                @if(!empty($order->shipment_details))
                    <li class="flex-space">
                        <span class="title">@lang('site.shipment_details')</span>
                        <span>{{$order->shipment_details }}</span>
                    </li>
                @endif
            </ul>
            <ul class="extra-precautions my-4">
                @if($order->spoil_quickly==1)
                    <li>@lang('site.spoil_quickly')</li>
                @endif
                @if($order->breakable==1)
                    <li>@lang('site.breakable')</li>
                @endif
            </ul>
            <ul class="shipment-details">
                <li class="flex-space">
                    <span class="title">@lang('site.Shipment Size')</span>
                    <span>{{$order->size}}</span>
                </li>
                <li class="flex-space">
                    <span class="title">@lang('site.weight')</span>
                    <span>{{$order->weight_ton}} @lang('site.ton')</span>
                </li>
                <li class="flex-space">
                    <span class="title">@lang('site.total_price')</span>
                    <span>{{ $order->total_price!=0.00 ? number_format($order->total_price,2, ".", "") : number_format( ( $order->ton_price * $order->weight_ton),2, ".", "")}} {{' '. setting('currency_atr')}}</span>
                </li>
                @if($order->accountant && $order->accountant->fine!='')
                    <li class="flex-space">
                        <span class="title">@lang('site.fine')</span>
                        <span>{{$order->accountant->fine}} {{' '.setting('currency_atr')}}</span>
                    </li>
                @endif
                @if($order->accountant)
                    <li class="flex-space">
                        <span class="title">@lang('site.service_seeker_fee')</span>
                        <span>{{$order->accountant->service_seeker_fee}} {{' '.setting('currency_atr')}}</span>
                    </li>
                @endif
                <li class="flex-space">
                    <span class="title">@lang('site.payment_method')</span>
                    <span>{{$order->paymentType->name}}</span>
                </li>
            </ul>
        </div>
        <div class="col-lg-6 col-12">
            @if($order->accountant)
                <ul class="shipment-details pb-2">
                    <li class="flex-space">
                        <span class="title">@lang('site.service_provider_commission')</span>
                        <span>{{$order->accountant->service_provider_commission}} %</span>
                    </li>
                    <li class="flex-space">
                        <span class="title">@lang('site.operating_costs')</span>
                        <span>{{$order->accountant->operating_costs}} {{' '.setting('currency_atr')}}</span>
                    </li>
                    <li class="flex-space">
                        <span class="title">@lang('site.expenses')</span>
                        <span>{{$order->accountant->expenses}} {{' '.setting('currency_atr')}}</span>
                    </li>
                    <li class="flex-space">
                        <span class="title"> @lang('site.service_provider_amount')</span>
                        <span>{{$order->accountant->service_provider_amount}} {{' '.setting('currency_atr')}}</span>
                    </li>
                </ul>
                @if($order->offer)
                    <div class="order-summary my-4">
                        <h4>@lang('site.checkout_summary')</h4>
                        <ul class="mb-3">
                            <li>
                                <span>@lang('site.shipping_price') ( {{app()->getLocale()=='ar' ? setting('app_name_ar') : setting('app_name_en')}})</span>
                                <span>{{$order->offer->price ?? 0.00}} {{' '.setting('currency_atr')}}</span>
                            </li>
                            <li>
                                <span>@lang('site.vat')</span>
                                <span>{{$order->offer->sub_total - $order->offer->price ?? 0.00}} {{' '.setting('currency_atr')}}</span>
                            </li>
                        </ul>
                        <div class="flex-space subtotal">
                                    <span class="flex-align-center">
                                        <img src="{{asset('assets/images/svgs/money-circle.svg')}}" alt="">
                                        @lang('site.sub_total')
                                    </span>
                            <span
                                class="amount">{{$order->offer->sub_total  ?? 0.00}} {{' '.setting('currency_atr')}}</span>
                        </div>
                    </div>
                @endif
            @endif
           @if($order->status != 'pend')
    @php
        $driver = $order->serviceProvider;
        $driverStats = $driver?->evaluationsReceived()
            ->selectRaw('COALESCE(AVG(rate),0) as avg_rate, COUNT(*) as total_rates')
            ->first();
        $driverRating = round($driverStats?->avg_rate ?? 0, 2);
        $driverRatingCount = $driverStats?->total_rates ?? 0;
        $driverRateRoute = null;
        if ($driver && Route::has('admin.drivers.evaluate') && $driver->type == 'driver') {
            $driverRateRoute = route('admin.drivers.evaluate', $driver->id);
        } elseif ($driver && Route::has('admin.companies.evaluate') && $driver->type == 'driversCompany') {
            $driverRateRoute = route('admin.companies.evaluate', $driver->id);
        } elseif ($driver && Route::has('admin.factories.evaluate') && $driver->type == 'factory') {
            $driverRateRoute = route('admin.factories.evaluate', $driver->id);
        }
    @endphp
    <div class="driver-card">
        <div class="flex-align-center">
            <img src="{{ optional(optional($order->serviceProvider)->userData)->image != '' ? asset(optional($order->serviceProvider->userData)->image) : asset('uploads/users/default.png') }}" alt="">

            <div class="flex-column">
                <span class="name">{{ optional($order->serviceProvider)->name }}</span>
                @if ($driverRateRoute)
                    <a href="{{ $driverRateRoute }}" class="rate">
                        <div class="flex-align-center">
                            @for ($star = 1; $star <= 5; $star++)
                                <img src="{{ asset($star <= round($driverRating) ? 'assets/images/svgs/star-fill.svg' : 'assets/images/svgs/star.svg') }}" alt="">
                            @endfor
                        </div>
                        <span class="value">{{ number_format($driverRating, 2) }}</span>
                        <span class="count">({{ $driverRatingCount }})</span>
                    </a>
                @else
                    <span class="rate">
                        <div class="flex-align-center">
                            @for ($star = 1; $star <= 5; $star++)
                                <img src="{{ asset($star <= round($driverRating) ? 'assets/images/svgs/star-fill.svg' : 'assets/images/svgs/star.svg') }}" alt="">
                            @endfor
                        </div>
                        <span class="value">{{ number_format($driverRating, 2) }}</span>
                        <span class="count">({{ $driverRatingCount }})</span>
                    </span>
                @endif
            </div>
        </div>
        <div class="driver-actions">
            <a href="tel:{{ optional($order->serviceProvider)->phone }}">
                <img src="{{asset('assets/images/svgs/call.svg')}}" alt="">
                @lang('site.call')
            </a>
            <a href="sms:{{ optional($order->serviceProvider)->phone }}?body={{ rawurlencode(__('site.sms_greeting')) }}">
                <img src="{{asset('assets/images/svgs/message.svg')}}" alt="">
                @lang('site.message')
            </a>
        </div>
    </div>
@endif

        </div>
    </div>
    @php
        $isPendingOrder = in_array($order->status, ['pend', 'pending'], true);
    @endphp
    @if($isPendingOrder)
        <div class="flex-center flex-wrap mt-5 gap-20">
            <button class="btn btn-navy shadow-none min-width-230" data-toggle="modal"
                    data-target="#driversOffers">@lang('site.offers')
            </button>
            <button class="btn btn-danger shadow-none min-width-230">@lang('site.cancel_order')</button>
        </div>
    @endif
    @if($order->status == 'approve')
        <div class="flex-center flex-wrap mt-5 gap-20">
            <button type="button" class="btn btn-navy shadow-none min-width-230" data-toggle="modal"
                    data-target="#PickUpQrModal">
                @lang('site.pick_up_code')
            </button>
            <button class="btn btn-navy shadow-none min-width-230"
                    onclick="event.preventDefault(); showTrackingModal();">
                @lang('site.follow_order')
            </button>
            <form action="{{ route('admin.orders.changeStatus', $order->id) }}" method="POST">
                @csrf
                @method('put')
                <input type="hidden" name="status" value="cancel">
                <button type="submit" class="btn btn-danger shadow-none min-width-230">@lang('site.cancel_order')</button>
            </form>
        </div>
    @endif
    @if($order->status == 'pick_up')
        <div class="flex-center flex-wrap mt-5 gap-20">
            <button type="button" class="btn btn-navy shadow-none min-width-230" data-toggle="modal"
                    data-target="#ReceiveQrModal">
                @lang('site.receiving_code')
            </button>
            <button class="btn btn-navy shadow-none min-width-230"
                    onclick="event.preventDefault(); showTrackingModal();">
                @lang('site.follow_order')
            </button>
            <form action="{{ route('admin.orders.changeStatus', $order->id) }}" method="POST">
                @csrf
                @method('put')
                <input type="hidden" name="status" value="cancel">
                <button type="submit" class="btn btn-danger shadow-none min-width-230">@lang('site.cancel_order')</button>
            </form>
        </div>
    @endif
    @if($order->status == 'delivered')
        <div class="flex-center flex-wrap mt-5 gap-20">
            <form action="{{ route('admin.orders.changeStatus', $order->id) }}" method="POST">
                @csrf
                @method('put')
                <input type="hidden" name="status" value="complete">
                <input type="hidden" name="service_provider" value="{{ $order->service_provider }}">
                <button type="submit" class="btn btn-navy shadow-none min-width-230">@lang('site.complete_order')</button>
            </form>
            <form action="{{ route('admin.orders.changeStatus', $order->id) }}" method="POST">
                @csrf
                @method('put')
                <input type="hidden" name="status" value="cancel">
                <input type="hidden" name="service_provider" value="{{ $order->service_provider }}">
                <button type="submit" class="btn btn-danger shadow-none min-width-230">@lang('site.cancel_order')</button>
            </form>
        </div>
    @endif
    @if($order->status == 'complete')
        <div class="flex-center flex-wrap mt-5 gap-20">
            <form action="{{ route('admin.orders.changeStatus', $order->id) }}" method="POST">
                @csrf
                @method('put')
                <input type="hidden" name="status" value="cancel">
                <input type="hidden" name="service_provider" value="{{ $order->service_provider }}">
                <button type="submit" class="btn btn-danger shadow-none min-width-230">@lang('site.cancel_order')</button>
            </form>
        </div>
    @endif
    @if($order->status == 'cancel' || $order->status == 'cancelled')
        <div class="flex-center mt-5">
            <div class="order-cancelled-banner">@lang('site.Order Cancel')</div>
        </div>
    @endif
    <!-- Start DriversOffers Modal -->
    <div class="modal fade" id="driversOffers" tabindex="-1" role="dialog" aria-labelledby="driversOffersLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
            <div class="modal-content fog-background">
                <div class="bring-to-front">
                    <div class="modal-header flex-center">
                        <h4 class="modal-title text-navy mb-0">@lang('site.drivers_offers')</h4>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                <path id="Exclusion_23" data-name="Exclusion 23"
                                      d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                      transform="translate(-0.384 0.422)" fill="#d27979"/>
                            </svg>
                        </button>
                    </div>

                    <div class="modal-body">
                        <ul class="offers-list max-width-80">
                            @foreach($offers as $offers)
                                <form class="pt-2" action="{{ route('admin.orders.changeStatus', $order->id) }}"
                                      method="put">
                                    @method('put')
                                    @csrf
                                    <li class="offer-item">
                                        <div class="flex-align-center">
                                            <a href="" class="flex-col-center">
                                                <img src="{{asset('uploads/users/default.png')}}" alt="" class="avatar">
                                                <span class="rate flex-col-center">
                                                <span class="total">
                                                    <img src="{{asset('assets/images/svgs/star-fill.svg')}}" alt="">
                                                    <img src="{{asset('assets/images/svgs/star-fill.svg')}}" alt="">
                                                    <img src="{{asset('assets/images/svgs/star-fill.svg')}}" alt="">
                                                    <img src="{{asset('assets/images/svgs/star-fill.svg')}}" alt="">
                                                    <img src="{{assert('assets/images/svgs/star-fill.svg')}}" alt="">
                                                </span>
                                                <span class="brief">5.00 <span>(340)</span></span>
                                            </span>
                                            </a>
                                            <div class="flex-space">
                                                <div class="flex-column">
                                                    <span class="name">{{$offers->user->name}}</span>
                                                    <input type="hidden" name="offers_user_name"
                                                           value="{{$offers->user->id}}">
                                                    @if($order->status == 'pend')
                                                        <input type="hidden" name="status" value="approve">
                                                    @elseif($order->status == 'approve')
                                                        <input type="hidden" name="status" value="complete">
                                                    @endif
                                                    <input type="hidden" name="service_provider"
                                                           value="{{$offers->user->id}}">
                                                    <ul>
                                                        <li>
                                                            <img src="{{asset('assets/images/svgs/map-marker.svg')}}"
                                                                 alt="">
                                                            {{$offers->user->userData->location}}
                                                        </li>
                                                        <li>
                                                            <img src="{{asset('assets/images/svgs/truck-fill.svg')}}"
                                                                 alt="">
                                                            {{ app()->getLocale() == 'ar'
                                                                ? @$offers->user->userData->car->name_ar
                                                                : @$offers->user->userData->car->name_en }}

                                                        </li>
                                                    </ul>
                                                </div>
                                                <h4 class="offer-cost special">
                                                    {{$offers->price}}
                                                    {{ setting('currency_atr') != '' ? setting('currency_atr') : '' }}
                                                </h4>
                                            </div>
                                        </div>
                                        <button type="submit" class="offer-action" data-mdb-target="#orderSummary"
                                                data-mdb-toggle="modal"
                                                data-mdb-dismiss="modal">
                                            @lang('site.accept')
                                        </button>
                                    </li>
                                </form>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End DriversOffers Modal -->
    <!-- Start OrderTracking Modal -->
    <div class="modal fade" id="TrackingModal" tabindex="-1" role="dialog" aria-labelledby="TrackingModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
            <div class="modal-content fog-background">
                <div class="bring-to-front">
                    <div class="modal-header flex-center">
                        <h4 class="modal-title text-navy mb-0">@lang('site.order_tracking')</h4>
                        <button type="button" id="TrackingModalClose" class="btn-close" data-dismiss="modal"
                                aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                <path id="Exclusion_23" data-name="Exclusion 23"
                                      d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                      transform="translate(-0.384 0.422)" fill="#d27979"/>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body position-relative px-0 pb-0">
                        <div class="flex-column max-width-60 px-md-0 px-3 mb-4">
                            <div class="tracking-path">
                                <img src="{{ asset('assets/images/track-path.png') }}" alt="">
                                <span class="flex-column">
                                    <span class="from">
                                        <span id="from">-</span>
                                    </span>
                                    <span class="to">
                                        <span id="to">-</span>
                                    </span>
                                </span>
                            </div>
                            <ul class="tracking-informations">
                                <li>
                                    <img src="{{ asset('assets/images/svgs/time-check.svg') }}" alt="">
                                    @lang('site.estimated_time'):&nbsp;<span id="duration"></span>
                                </li>
                                <li>
                                    <img src="{{ asset('assets/images/svgs/road.svg') }}" alt="">
                                    @lang('site.road_distance'):&nbsp;<span id="distance"></span>
                                </li>
                            </ul>
                        </div>
                        <div id="map" class="map" style="height: 460px; width: 100%;"></div>
                        <div class="flex-center">
                            <a href="#" class="btn btn-navy shadow-none" data-dismiss="modal"
                               aria-label="Close">@lang('site.back_to_order')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End OrderTracking Modal -->
    <!-- Start PickUp QR Modal -->
    <div class="modal fade" id="PickUpQrModal" tabindex="-1" role="dialog" aria-labelledby="PickUpQrModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
            <div class="modal-content fog-background">
                <div class="bring-to-front">
                    <div class="modal-header flex-center">
                        <h4 class="modal-title text-navy mb-0">@lang('site.pick_up_confirmation')</h4>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                <path id="Exclusion_23" data-name="Exclusion 23"
                                      d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                      transform="translate(-0.384 0.422)" fill="#d27979"/>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="flex-column flex-center text-center">
                            <p class="mb-4">
                                @lang('site.pick_up_confirmation_text')
                            </p>
                            <div id="pickup-qr" class="mb-4"></div>
                            <div id="pickup-qr-message" class="mb-3"></div>
                            <div class="flex-center flex-wrap gap-20">
                                <button type="button" id="pickup-qr-share" class="btn btn-transparent navy min-width-230">
                                    @lang('site.share_link')
                                </button>
                                <form action="{{ route('admin.orders.changeStatus', $order->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" name="status" value="pick_up">
                                    <input type="hidden" name="service_provider" value="{{ $order->service_provider }}">
                                    <button type="submit" class="btn btn-navy min-width-230">
                                        @lang('site.manual_pickup')
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End PickUp QR Modal -->
    <!-- Start Receive QR Modal -->
    <div class="modal fade" id="ReceiveQrModal" tabindex="-1" role="dialog" aria-labelledby="ReceiveQrModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
            <div class="modal-content fog-background">
                <div class="bring-to-front">
                    <div class="modal-header flex-center">
                        <h4 class="modal-title text-navy mb-0">@lang('site.receive_qr_title')</h4>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                <path id="Exclusion_23" data-name="Exclusion 23"
                                      d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                      transform="translate(-0.384 0.422)" fill="#d27979"/>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="flex-column flex-center text-center">
                            <p class="mb-4">
                                @lang('site.receive_qr_text')
                            </p>
                            <div id="receive-qr" class="mb-4"></div>
                            <div id="receive-qr-message" class="mb-3"></div>
                            <div class="flex-center flex-wrap gap-20">
                                <button type="button" id="receive-qr-share" class="btn btn-transparent navy min-width-230">
                                    @lang('site.share_link')
                                </button>
                                <form action="{{ route('admin.orders.changeStatus', $order->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" name="status" value="delivered">
                                    <input type="hidden" name="service_provider" value="{{ $order->service_provider }}">
                                    <button type="submit" class="btn btn-navy min-width-230">
                                        @lang('site.manual_delivery')
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Receive QR Modal -->
@endsection


@section('scripts')
    @php
        $pageMessages = [
            'loading' => __('site.loading'),
            'qr_generate_failed' => __('site.qr_generate_failed'),
            'qr_generate_first' => __('site.qr_generate_first'),
            'share_canceled' => __('site.share_canceled'),
            'link_copied' => __('site.link_copied'),
            'copy_failed' => __('site.copy_failed'),
            'copy_not_supported' => __('site.copy_not_supported'),
            'pick_up_code' => __('site.pick_up_code'),
            'receiving_code' => __('site.receiving_code'),
            'directions_error' => __('site.directions_error'),
        ];
    @endphp
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCWsYnE6Jsdi4SGqw50cYLDcSYI8eAYL7k&callback=initMap"
            async></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        const pickUpQrEndpoint = "{{ url('/api/orders/' . $order->id . '/generate-qr') }}";
        const receiveQrEndpoint = "{{ url('/api/orders/' . $order->id . '/generate-qr') }}";
        const pageMessages = @json($pageMessages);
        let pickUpQrPayload = '';
        let receiveQrPayload = '';
        let pickUpQrInstance = null;
        let receiveQrInstance = null;

        $('#PickUpQrModal').on('shown.bs.modal', function () {
            generatePickUpQr();
        });
        $('#pickup-qr-share').on('click', function (e) {
            e.preventDefault();
            sharePickUpQr();
        });
        $('#ReceiveQrModal').on('shown.bs.modal', function () {
            generateReceiveQr();
        });
        $('#receive-qr-share').on('click', function (e) {
            e.preventDefault();
            shareReceiveQr();
        });

        const trackingOrder = {!! json_encode([
            'id' => $order->id,
            'pickup_lat' => $order->pick_up_late,
            'pickup_lng' => $order->pick_up_long,
            'dropoff_lat' => $order->drop_of_late,
            'dropoff_lng' => $order->drop_of_long,
            'driver_lat' => $order->serviceProvider?->userData?->latitude ?? '0',
            'driver_lng' => $order->serviceProvider?->userData?->longitude ?? '0',
            'pickup_address' => $order->pick_up_address,
            'dropoff_address' => $order->drop_of_address,
            'customer_name' => $order->user?->name,
            'driver_name' => $order->serviceProvider?->name
        ]) !!};

        $('#TrackingModalClose, a[data-dismiss="modal"][aria-label="Close"]').on('click', function (e) {
            e.preventDefault();
            $('#TrackingModal').modal('hide');
        });
        $('#TrackingModal').on('shown.bs.modal', function () {
            if (map) {
                google.maps.event.trigger(map, 'resize');
            }
        });
        let map, map1, map2, activeInfoWindow, markers = [];
        var directionsService, directionsDisplay;

        function renderPickUpQr(payload) {
            const container = document.getElementById('pickup-qr');
            if (!container) {
                return;
            }
            container.innerHTML = '';
            pickUpQrPayload = payload;
            if (window.QRCode) {
                pickUpQrInstance = new QRCode(container, {
                    text: payload,
                    width: 220,
                    height: 220,
                    typeNumber: -1,
                    correctLevel: QRCode.CorrectLevel.L
                });
            }
        }

        function renderReceiveQr(payload) {
            const container = document.getElementById('receive-qr');
            if (!container) {
                return;
            }
            container.innerHTML = '';
            receiveQrPayload = payload;
            if (window.QRCode) {
                receiveQrInstance = new QRCode(container, {
                    text: payload,
                    width: 220,
                    height: 220,
                    typeNumber: -1,
                    correctLevel: QRCode.CorrectLevel.L
                });
            }
        }

        function setPickUpQrMessage(message) {
            const messageEl = document.getElementById('pickup-qr-message');
            if (messageEl) {
                messageEl.textContent = message || '';
            }
        }

        function setReceiveQrMessage(message) {
            const messageEl = document.getElementById('receive-qr-message');
            if (messageEl) {
                messageEl.textContent = message || '';
            }
        }

        function generatePickUpQr() {
            setPickUpQrMessage(pageMessages.loading);
            fetch(pickUpQrEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: 'pick_up' })
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { ok: response.ok, data: data };
                    });
                })
                .then(function (result) {
                    if (!result.ok) {
                        throw new Error(result.data.error || pageMessages.qr_generate_failed);
                    }
                    renderPickUpQr(result.data.qr_payload);
                    setPickUpQrMessage('');
                })
                .catch(function (error) {
                    setPickUpQrMessage(error.message || pageMessages.qr_generate_failed);
                });
        }

        function generateReceiveQr() {
            setReceiveQrMessage(pageMessages.loading);
            fetch(receiveQrEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: 'receive' })
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { ok: response.ok, data: data };
                    });
                })
                .then(function (result) {
                    if (!result.ok) {
                        throw new Error(result.data.error || pageMessages.qr_generate_failed);
                    }
                    renderReceiveQr(result.data.qr_payload);
                    setReceiveQrMessage('');
                })
                .catch(function (error) {
                    setReceiveQrMessage(error.message || pageMessages.qr_generate_failed);
                });
        }

        function sharePickUpQr() {
            if (!pickUpQrPayload) {
                setPickUpQrMessage(pageMessages.qr_generate_first);
                return;
            }
            if (navigator.share) {
                navigator.share({
                    title: pageMessages.pick_up_code,
                    text: pickUpQrPayload
                }).catch(function () {
                    setPickUpQrMessage(pageMessages.share_canceled);
                });
                return;
            }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(pickUpQrPayload).then(function () {
                    setPickUpQrMessage(pageMessages.link_copied);
                }).catch(function () {
                    setPickUpQrMessage(pageMessages.copy_failed);
                });
                return;
            }
            setPickUpQrMessage(pageMessages.copy_not_supported);
        }

        function shareReceiveQr() {
            if (!receiveQrPayload) {
                setReceiveQrMessage(pageMessages.qr_generate_first);
                return;
            }
            if (navigator.share) {
                navigator.share({
                    title: pageMessages.receiving_code,
                    text: receiveQrPayload
                }).catch(function () {
                    setReceiveQrMessage(pageMessages.share_canceled);
                });
                return;
            }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(receiveQrPayload).then(function () {
                    setReceiveQrMessage(pageMessages.link_copied);
                }).catch(function () {
                    setReceiveQrMessage(pageMessages.copy_failed);
                });
                return;
            }
            setReceiveQrMessage(pageMessages.copy_not_supported);
        }

        function drawPath(directionsService, directionsDisplay, start, end) {
            directionsService.route({
                origin: start,
                destination: end,
                optimizeWaypoints: true,
                travelMode: window.google.maps.DirectionsTravelMode.DRIVING
            }, function (response, status) {
                if (status === 'OK') {
                    directionsDisplay.setDirections(response);
                    var leg = response.routes?.[0]?.legs?.[0];
                    if (leg) {
                        $('#distance').html(leg.distance?.text || '');
                        $('#duration').html(leg.duration?.text || '');
                    }
                    var infowindow = new window.google.maps.InfoWindow({
                        content: "@lang('site.drop_of_address')<br>" + " " + response.routes[0].legs[0].distance.text
                    });
                } else {
                    alert(pageMessages.directions_error.replace(':status', status));
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
                mapTypeId: 'terrain'
            });
        }

        var previousMarker;

        function showTrackingModal() {
            showModal(
                trackingOrder.pickup_lat,
                trackingOrder.pickup_lng,
                trackingOrder.dropoff_lat,
                trackingOrder.dropoff_lng,
                trackingOrder.driver_lat,
                trackingOrder.driver_lng,
                trackingOrder
            );
        }

        function showModal(plat, plng, dlat, dlng, drlat, drlng, order) {
            let order1 = order;
            $('#TrackingModal').modal('show');
            $('#distance').html('');
            $('#duration').html('');

            if (directionsDisplay != null) {
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
            directionsDisplay.addListener('directions_changed', function () {
                const current = directionsDisplay.getDirections();
                if (!current || !current.routes || !current.routes[0] || !current.routes[0].legs || !current.routes[0].legs[0]) {
                    return;
                }
                const leg = current.routes[0].legs[0];
                $('#distance').html(leg.distance ? leg.distance.text : '');
                $('#duration').html(leg.duration ? leg.duration.text : '');
            });
            let mylatelng = {
                lat: parseFloat(plat),
                lng: parseFloat(plng)
            };
            let myLatlng1 = {
                lat: parseFloat(dlat),
                lng: parseFloat(dlng)
            };

            drawPath(directionsService, directionsDisplay, mylatelng, myLatlng1);
            var bounds = new google.maps.LatLngBounds();
            bounds.extend(mylatelng);
            bounds.extend(myLatlng1);
            if (drlat != 0 && drlng != 0) {
                let myLatlng2 = {
                    lat: parseFloat(drlat),
                    lng: parseFloat(drlng)
                };
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
                    content: "<h5>{{ __('site.order_number') }} : <span style='color:red;'>" + order1.id +
                        "</span></h5>" +
                        "<h5>{{ __('site.customer') }} : <span style='color:red;'>" + (order1.customer_name || '-') +
                        "</span></h5>" +
                        "<h5>{{ __('site.driver') }} : <span style='color:red;'>" + (order1.driver_name || '-') +
                        "</span></h5>"
                });
                infowindow.open(map, previousMarker);
                map.addListener("center_changed", () => {
                    window.setTimeout(() => {
                        map.panTo(previousMarker.getPosition());
                    }, 3000);
                });
                bounds.extend(myLatlng2);
            }
            map.fitBounds(bounds);
            const fromLabel = order.pickup_address || (Number.isFinite(mylatelng.lat) ? (mylatelng.lat + ', ' + mylatelng.lng) : '-');
            const toLabel = order.dropoff_address || (Number.isFinite(myLatlng1.lat) ? (myLatlng1.lat + ', ' + myLatlng1.lng) : '-');
            $('#from').html(fromLabel);
            $('#to').html(toLabel);
            getDisAndDur({
                'lat': parseFloat(plat),
                'long': parseFloat(plng)
            }, {
                'lat': parseFloat(dlat),
                'long': parseFloat(dlng)
            });
        }

        function getDisAndDur(ori, dest) {
            var origin = new google.maps.LatLng(ori.lat, ori.long);
            var destination = new google.maps.LatLng(dest.lat, dest.long);
            var options = {
                origins: [origin],
                destinations: [destination],
                travelMode: 'DRIVING',
                unitSystem: google.maps.UnitSystem.METRIC
            };
            var service = new google.maps.DistanceMatrixService();
            service.getDistanceMatrix(options, function (response, status) {
                if (status == 'OK') {
                    var distance = response.rows[0].elements[0].distance.text;
                    var duration = response.rows[0].elements[0].duration.text;
                    $("#distance").html(distance);
                    $("#duration").html(duration);
                } else {
                    console.log('Error: ' + ori + ' ' + dest + ' ' + status);
                }
            });
        }
    </script>
@endsection
