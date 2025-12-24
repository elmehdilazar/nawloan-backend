@extends('layouts.admin.app')
@section('title',' | ' . __('site.show') . ' '. __('site.the_order'))
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
                                Sharjah
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
                Call
            </a>
            <a href="sms:{{ optional($order->serviceProvider)->phone }}?body=Hello%20there!">
                <img src="{{asset('assets/images/svgs/message.svg')}}" alt="">
                Message
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
                    data-target="#driversOffers">Offers
            </button>
            <button class="btn btn-danger shadow-none min-width-230">Cancel</button>
        </div>
    @endif
    <!-- Start DriversOffers Modal -->
    <div class="modal fade" id="driversOffers" tabindex="-1" role="dialog" aria-labelledby="driversOffersLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
            <div class="modal-content fog-background">
                <div class="bring-to-front">
                    <div class="modal-header flex-center">
                        <h4 class="modal-title text-navy mb-0">Drivers Offers</h4>
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
                                                            {{@$offers->user->userData->car->name_en}}

                                                        </li>
                                                    </ul>
                                                </div>
                                                <h4 class="offer-cost special">{{$offers->price}} EGP</h4>
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
@endsection


@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCWsYnE6Jsdi4SGqw50cYLDcSYI8eAYL7k&callback=initMap"
            async></script>
    <script>
        let map, activeInfoWindow, markers = [];

        /* ----------------------------- Initialize Map ----------------------------- */
        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 28.626137,
                    lng: 79.821603,
                },
                zoom: 15
            });

            map.addListener("click", function (event) {
                mapClicked(event);
            });

            initMarkers();
        }

        /* --------------------------- Initialize Markers --------------------------- */
        function initMarkers() {
            const initialMarkers = <?php echo json_encode($initialMarkers); ?>;

            for (let index = 0; index < initialMarkers.length; index++) {

                const markerData = initialMarkers[index];
                const marker = new google.maps.Marker({
                    position: markerData.position,
                    label: markerData.label,
                    draggable: markerData.draggable,
                    map
                });
                markers.push(marker);

                const infowindow = new google.maps.InfoWindow({
                    content: `<b>${markerData.position.lat}, ${markerData.position.lng}</b>`,
                });
                marker.addListener("click", (event) => {
                    if (activeInfoWindow) {
                        activeInfoWindow.close();
                    }
                    infowindow.pend({
                        anchor: marker,
                        shouldFocus: false,
                        map
                    });
                    activeInfoWindow = infowindow;
                    markerClicked(marker, index);
                });

                marker.addListener("dragend", (event) => {
                    markerDragEnd(event, index);
                });
            }
        }

        /* ------------------------- Handle Map Click Event ------------------------- */
        function mapClicked(event) {
            console.log(map);
            console.log(event.latLng.lat(), event.latLng.lng());
        }

        /* ------------------------ Handle Marker Click Event ----------------------- */
        function markerClicked(marker, index) {
            console.log(map);
            console.log(marker.position.lat());
            console.log(marker.position.lng());
        }

        /* ----------------------- Handle Marker DragEnd Event ---------------------- */
        function markerDragEnd(event, index) {
            console.log(map);
            console.log(event.latLng.lat());
            console.log(event.latLng.lng());
        }
    </script>
@endsection
