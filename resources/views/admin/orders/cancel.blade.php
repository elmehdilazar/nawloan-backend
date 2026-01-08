@extends('layouts.admin.app')
@section('title',' | ' . __('site.cancel_orders'))
@section('styles')
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
<div class="flex-space mb-4 dash-head">
    <h2 class="section-title mb-0">@lang('site.cancel_orders')</h2>
    <div class="head-btns mb-0">
        <span id="checks-count" class="onchange-visible"></span>
        @if(auth()->user()->hasPermission('orders_export'))
            <a href="{{route('admin.orders.export')}}" class="btn btn-transparent navy">@lang('site.export')</a>
        @endif
        @if(auth()->user()->hasPermission('orders_delete'))
            <a href="#" class="btn btn-danger onchange-visible">@lang('site.delete')</a>
        @endif
    </div>
</div>
<table class="table datatables datatables-active" id="">
    <thead>
    <tr>
        <th>
            <div class="dt-checkbox">
                <input type="checkbox" name="select_all" value="1" id="selectAll">
                <label for="selectAll" class="visual-checkbox"></label>
            </div>
        </th>
        <th>@lang('site.num')</th>
        <th class="min-width-170">@lang('site.service_seeker')</th>
        <th>@lang('site.car')</th>
        <th>@lang('site.total_price')</th>
        <th>@lang('site.transaction_status')</th>
        <th>@lang('site.status')</th>
        <th>@lang('site.edit')</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($orders as $index=>$order )
        <tr>
            <td></td>
            <td>{{$order->id}}</td>
            <td>
                <div class="user-col">
                    <img src="{{$order->user->userData->image !='' ? asset($order->user->userData->image) : asset('uploads/users/default.png')}}"
                         alt="{{$order->user->name}}">
                    <span class="name">{{$order->user->name}}</span>
                </div>
            </td>
            <td>
                <a href="#" data-toggle="modal" data-target="#truckModal_{{$index}}">
                    {{ app()->getLocale() == 'ar' ? $order->car->name_ar : $order->car->name_en }}
                </a>
            </td>
            <td>{{number_format( ( $order->ton_price * $order->weight_ton),2, ".", "")}}{{' '. setting('currency_atr')}}</td>
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
                <span class="badge badge-pill
                    @if($order->status =='pending') badge-warning
                    @elseif($order->status=='approve' || $order->status=='pick_up' || $order->status=='delivered') badge-primary
                    @elseif ($order->status=='complete') badge-success
                    @elseif ($order->status=='cancel') badge-danger @endif">
                    @if($order->status=='pending')
                        @lang('site.pend')
                    @elseif($order->status=='approve')
                        @lang('site.approval')
                    @elseif($order->status=='pick_up')
                        @lang('site.Pick Up')
                    @elseif( $order->status=='delivered')
                        @lang('site.Delivered')
                    @elseif($order->status=='complete')
                        @lang('site.completed')
                    @elseif($order->status=='cancel')
                        @lang('site.canceled')
                    @endif
                </span>
            </td>
            <td>
                <ul class="actions">
                    @if(auth()->user()->hasPermission('orders_read') )
                        <li>
                            {{--<a href="#" data-toggle="modal" data-target="#orderModal_{{$index}}"--}}
                            {{--   title="@lang('site.show')" class="show">--}}
                            {{--    <i class="fad fa-eye"></i>--}}
                            {{--</a>--}}
                            <a href="{{route('admin.orders.show',$order->id)}}"
                               title="@lang('site.show')" class="show">
                                <i class="fad fa-eye"></i>
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasPermission('orders_update'))
                        <li><a href="{{route('admin.orders.edit',$order->id)}}" title="@lang('site.edit')" class="show"><i class="fad fa-edit"></i></a></li>
                        @if($order->status=='cancel')
                            <li>
                                <a href="#" class="check"
                                   onclick="event.preventDefault();document.getElementById('pend-form_{{$index}}').submit();">
                                    <i class="fad fa-check-double"></i>
                                </a>
                            </li>
                            <form action="{{route('admin.orders.changeStatus',$order->id)}}" method="POST"
                                  id="pend-form_{{$index}}">
                                @csrf
                                @method('put')
                                <input type="hidden" id="status" name="status" value="pend">
                            </form>
                        @endif
                        @if($order->status!='complete' && $order->status!='cancel' && $order->status!='pending')
                            <li>
                                <a href="#" class="success"
                                   onclick="event.preventDefault();document.getElementById('complete-form_{{$index}}').submit();">
                                    <i class="fad fa-clipboard-check"></i>
                                </a>
                            </li>
                            <form action="{{route('admin.orders.changeStatus',$order->id)}}" method="POST" id="complete-form_{{$index}}">
                                @csrf
                                @method('put')
                                <input type="hidden" id="status" name="status" value="complete">
                            </form>
                        @endif
                        @if($order->status!='complete' && $order->status!='cancel')
                            <li>
                                <a href="#" class="cancel"
                                   onclick="event.preventDefault();document.getElementById('cancel-form_{{$index}}').submit();">
                                    <i class="fad fa-times"></i>
                                </a>
                            </li>
                            <form action="{{route('admin.orders.changeStatus',$order->id)}}" method="POST"
                                  id="cancel-form_{{$index}}">
                                @csrf
                                @method('put')
                                <input type="hidden" id="status" name="status" value="cancel">
                            </form>
                        @endif
                    @endif
                    @if(auth()->user()->hasPermission('orders_read'))
                        <li>
                            <a href="#" class="show" title="@lang('site.order_track')"
                               onclick="showModal({{$order->pick_up_late}},{{$order->pick_up_long}},{{$order->drop_of_late}},{{$order->drop_of_long}},{{$order->serviceProvider->userData->latitude ?? '0'}},{{$order->serviceProvider->userData->longitude ?? '0'}},{{$order}});">
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
{{--<div class="d-flex justify-content-center">--}}
{{--    {{$orders->appends(request()->query())->links()}}--}}
{{--</div>--}}
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
                            <h4>@lang('site.no_offers_found_for_order_yet')</h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End DriversOffers Modal -->
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
                                    <span id="from">@lang('site.tracking_placeholder_address')</span>
                                </span>
                                <span class="to">
                                    <span id="to">@lang('site.tracking_placeholder_address')</span>
                                </span>
                            </span>
                        </div>
                        <ul class="tracking-informations">
                            <li>
                                <img src="{{asset('assets/images/svgs/time-check.svg')}}" alt="">
                                @lang('site.estimated_time'):&nbsp;<span id="duration"></span>
                            </li>
                            <li>
                                <img src="{{asset('assets/images/svgs/road.svg')}}" alt="">
                                @lang('site.road_distance'):&nbsp;<span id="distance" ></span>
                            </li>
                        </ul>
                    </div>
                    <div id="map" class="map" style="height: 460px; width: 100%;"></div>
                    <div class="flex-center">
                        <a href="#" class="btn btn-navy shadow-none" data-dismiss="modal" aria-label="Close">@lang('site.back_to_order')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Data Tables -->
    <script src='{{asset('assets/tiny/js/jquery.dataTables.min.js')}}'></script>
    <script src='{{asset('assets/tiny/js/dataTables.bootstrap4.min.js')}}'></script>
    <!-- DataTables Playground (Setups, Options, Actions) -->
    <script src='{{asset('assets/js/dataTables-init.js')}}'></script>
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
                    alert("@lang('site.direction_problem_due_to') " + status);
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
