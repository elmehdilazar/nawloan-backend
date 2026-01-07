@extends('layouts.admin.app')
@section('title',' | ' . __('site.add') .' ' . __('site.the_order'))
@section('styles')
     <style>
.pac-container {
    z-index: 1051 !important; /* Bootstrap modal is 1050 */
}
</style>
    <link rel="stylesheet" href="{{asset('assets/tiny/css/daterangepicker.css')}}">
@endsection

@section('content')
<h2 class="section-title mb-5">@lang('site.add_new_order')</h2>
<!--<div>
    <h3>Pickup Location</h3>
<input id="pickup" placeholder="Search pickup...">
<input id="pickup_lat" placeholder="Latitude" readonly>
<input id="pickup_lng" placeholder="Longitude" readonly>
<div id="map1"></div>

<h3>Dropoff Location</h3>
<input id="dropoff" placeholder="Search dropoff...">
<input id="dropoff_lat" placeholder="Latitude" readonly>
<input id="dropoff_lng" placeholder="Longitude" readonly>
<div id="map2"></div>

<h3>Route and Distance</h3>
<p id="distance_info">Distance: </p>
<div id="map"></div>
    
</div>-->
<form action="{{route('admin.orders.store')}}" method="POST">
    @csrf
    @method('post')
    <div class="row">
        <div class="col-xl-7 col-lg-9 co-12">
            <div class="select-group">
                <label for="simple-select2" class="">@lang('site.customer')</label>
                <select class="form-control select2" id="user_id" name="user_id">
                    <option value="" selected disabled>@lang('site.choose_customer')</option>
                    @foreach ($users as $user)
                    <option value="{{$user->id}}" {{$user->id == old('user_id') ? 'selected' : ''}} >
                        {{$user->name}}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="select-group">
                <label for="" class="">@lang('site.car')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/car.svg')}}" alt="" class="icon">
                    <select class="form-control select2" id="car_id" name="car_id">
                        <option value="" selected disabled>@lang('site.choose_car')</option>
                        @foreach ($cars as $car)
                            <option value="{{$car->id}}" {{ old('car_id')== $car->id ? 'selected' : '' }}>
                                {{app()->getLocale()=='ar' ? $car->name_ar : $car->name_en}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="input-group">
                <label for="" class="">@lang('site.pick_up_address')</label>
                <a href="" class="open-modal position-relative before-icon" data-toggle="modal"
                   data-target="#PickupMapModal">
                    <img src="{{asset('assets/images/svgs/loacated.svg')}}" alt="" class="icon">
                    <input type="text" id="pick_up_address" name="pick_up_address"
                           placeholder="@lang('site.pick_up_address')" value="{{old('pick_up_address')}}" readonly>
                </a>
                @error('pick_up_address')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="input-group">
                        <label for="" class="">@lang('site.pick_up_late')</label>
                        <div class="input-group">
                            <input class="" type="text" id="pickup_lat" name="pick_up_late"
                                   placeholder="@lang('site.pick_up_late')" value="{{old('pick_up_late')}}">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fad fa-map-marked-alt"></i>
                                </div>
                            </div>
                        </div>
                        @error('pick_up_late')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="input-group">
                        <label for="" class="">@lang('site.pick_up_long')</label>
                        <div class="input-group">
                            <input class="" type="text" id="pickup_lng" name="pick_up_long"
                                   placeholder="@lang('site.pick_up_long')" value="{{old('pick_up_long')}}">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fad fa-map-marked-alt"></i>
                                </div>
                            </div>
                        </div>
                        @error('pick_up_long')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="input-group">
                <label for="" class="">@lang('site.drop_of_address')</label>
                <a href="" class="open-modal position-relative before-icon" data-toggle="modal"
                   data-target="#DropofMapModal">
                    <img src="{{asset('assets/images/svgs/map-marker.svg')}}" alt="" class="icon">
                    <input type="text" id="drop_of_address" name="drop_of_address" readonly
                           placeholder="@lang('site.drop_of_address')" value="{{old('drop_of_address')}}">
                </a>
                @error('drop_of_address')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="input-group">
                        <label for="" class="">@lang('site.drop_of_late')</label>
                        <div class="input-group">
                            <input class="" type="text" id="dropoff_lat" name="drop_of_late"
                                   placeholder="@lang('site.drop_of_late')" value="{{old('drop_of_late')}}">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fad fa-map-marked-alt"></i>
                                </div>
                            </div>
                        </div>
                        @error('drop_of_late')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="input-group">
                        <label for="" class="">@lang('site.drop_of_long')</label>
                        <div class="input-group">
                            <input class="" type="text" id="dropoff_lng" name="drop_of_long"
                                   placeholder="@lang('site.drop_of_long')" value="{{old('drop_of_long')}}">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fad fa-map-marked-alt"></i>
                                </div>
                            </div>
                        </div>
                        @error('drop_of_long')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="select-group">
                <label for="" class="">@lang('site.shipment_type')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/type.svg')}}" alt="" class="icon">
                    <select class="form-control select2" id="shipment_type_id" name="shipment_type_id">
                        <option value="" selected>@lang('site.choose_shipment_type')</option>
                        @foreach ($shipments as $shipment)
                            <option value="{{$shipment->id}}" {{ old('shipment_type_id')== $shipment->id ? 'selected' :'' }}>
                                {{app()->getLocale()=='ar' ? $shipment->name_ar : $shipment->name_en}}</option>
                        @endforeach
                    </select>
                </div>
                @error('shipment_type_id')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="textarea-group">
                <label for="" class="">@lang('site.shipment_details')</label>
                <textarea id="shipment_details" name="shipment_details" cols="" rows="4"
                          placeholder="@lang('site.shipment_details')">
                    {{old('shipment_details')}}
                </textarea>
                @error('shipment_details')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="checkbox-group">
                <label for="">@lang('site.Other Specification For The Shipment')</label>
                <ul class="checkbox-list">
                    <li>
                        <div class="form-check">
                            <input class="form-check-input transparent" type="checkbox" id="spoil_quickly" name="spoil_quickly">
                            <label class="form-check-label" for="spoil_quickly">@lang('site.spoil_quickly')</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-check">
                            <input class="form-check-input transparent" type="checkbox" id="breakable" name="breakable">
                            <label class="form-check-label" for="breakable">@lang('site.breakable')</label>
                        </div>
                    </li>
                </ul>
                @error('spoil_quickly')
                <span class="text-danger">{{$message}}</span>
                @enderror
                @error('breakable')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="radio-group">
                <label for="">@lang('site.The Shipment Size')</label>
                <ul class="radio-list">
                    <li>
                        <div class="form-radio">
                            <input class="radio-input" type="radio" id="large" name="size" value="large"
                                   @if(old('size')=='large' || old('size') =='') checked @endif>
                            <label class="radio-label" for="large">@lang('site.large')</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-radio">
                            <input class="radio-input" type="radio" id="medium" name="size" value="medium"
                                   @if(old('size')=='medium') checked @endif>
                            <label class="radio-label" for="medium">@lang('site.medium')</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-radio">
                            <input class="radio-input" type="radio" name="size" id="small" value="small"
                                   @if(old('size')=='small') checked @endif>
                            <label class="radio-label" for="small">@lang('site.small')</label>
                        </div>
                    </li>
                </ul>
                @error('size')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.Shipment Weight')</label>
                <div class="position-relative before-icon">
                    <img src="{{asset('assets/images/svgs/weight.svg')}}" alt="" class="icon">
                    <input type="number" step="1" min="1" id="weight_ton" name="weight_ton"
                           value="{{old('weight_ton') !='' ? old('weight_ton') : 1}}" placeholder="@lang('site.Shipment Weight')">
                </div>
                @error('weight_ton')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.ton_price')</label>
                <div class="position-relative before-icon">
                    <img src="{{asset('assets/images/svgs/cost-ticket.svg')}}" alt="" class="icon size-auto">
                    <div class="input-group">
                        <input type="number" step="1" min="1" id="ton_price" name="ton_price"
                               value="{{old('ton_price') !='' ? old('ton_price') : 1.00}}" placeholder="@lang('site.ton_price')">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{setting('currency_atr') !='' ? setting('currency_atr') :''}}
                            </div>
                        </div>
                    </div>
                </div>
                @error('ton_price')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.total_price')</label>
                <div class="position-relative before-icon">
                    <img src="{{asset('assets/images/svgs/cost-ticket.svg')}}" alt="" class="icon size-auto">
                    <div class="input-group">
                        <input type="number" step="1" min="1" id="total_price" name="total_price"
                               value="1.00" readonly>
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                {{setting('currency_atr') !='' ? setting('currency_atr') :''}}
                            </div>
                        </div>
                    </div>
                </div>
                @error('total_price')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.shipping_date')</label>
                <div class="position-relative before-icon">
                    <img src="{{asset('assets/images/svgs/calendar.svg')}}" alt="" class="icon">
                    <input type="text" class="form-control drgpicker" id="shipping_date" name="shipping_date"
                           value="{{now()->format('Y-m-d')}}">
                </div>
                @error('shipping_date')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="select-group">
                <label for="" class="">@lang('site.payment_method')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/type.svg')}}" alt="" class="icon">
                    <select class="form-control select2" id="payment_method_id" name="payment_method_id">
                        <option value="" selected disabled>@lang('site.choose_payment_method')</option>
                        @foreach ($payTypes as $payType)
                            <option value="{{$payType->id}}" {{$payType->id == old('payment_method_id') ? 'selected' : ''}} >
                                {{$payType->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('payment_method_id')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="total-ticket flex-space mb-4">
                <span class="title">
                    <img src="{{asset('assets/images/svgs/money-circle.svg')}}" alt="">
                    @lang('site.expected_final_total_price')
                </span>
                <span class="total">
    {{ old('ton_price') && old('weight_ton') 
        ? number_format(old('ton_price') * old('weight_ton', 1), 2, ".", "") 
        : '0.00' 
    }} @lang('site.currency_egp')
</span>

            </div>
            <div id="map" style="height: 360px; width: 100%; border-radius: 16px;"></div>
            <button type="submit" class="btn btn-navy mt-4" title="@lang('site.add_new_order')">
                @lang('site.add_new_order')
            </button>
        </div>
    </div>
</form>

<div class="modal fade" id="PickupMapModal" tabindex="-1" role="dialog" aria-labelledby="PickupMapModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
        <div class="modal-content fog-background">
            <div class="bring-to-front">
                <div class="modal-header flex-center">
                    <h4 class="modal-title text-navy mb-0">@lang('site.pick_up_address')</h4>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                            <path id="Exclusion_23" data-name="Exclusion 23"
                                  d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                  transform="translate(-0.384 0.422)" fill="#d27979" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body position-relative px-0 pb-0">
                    <div class="map-search flex-col-center max-width-70 px-md-0 px-3">
                        <div class="search-group position-relative w-100">
                           <input id="pickup" placeholder="@lang('site.search_pickup')">
                            <img src="{{asset('assets/images/svgs/search.svg')}}" alt="" class="icon">
                        </div>
                        <div class="flex-space yellow mt-3 mb-4">
                            <div class="flex-align-center">
                                <img src="{{asset('assets/images/svgs/loacated.svg')}}" alt="">
                                <span id="pickup_label">@lang('site.cairo')</span>
                            </div>
                            <img src="{{asset('assets/images/svgs/check-circle-yellow.svg')}}" alt="">
                        </div>
                    </div>
                    <div id="map1" style="height: 460px; width: 100%;"></div>
                    <div class="flex-center">
                        <button type="submit" class="btn btn-navy shadow-none" data-dismiss="modal"
                                aria-label="Close">@lang('site.add_pickup_location')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="DropofMapModal" tabindex="-1" role="dialog" aria-labelledby="DropofMapModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
        <div class="modal-content fog-background">
            <div class="bring-to-front">
                <div class="modal-header flex-center">
                    <h4 class="modal-title text-navy mb-0">@lang('site.drop_of_address')</h4>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                            <path id="Exclusion_23" data-name="Exclusion 23"
                                  d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                  transform="translate(-0.384 0.422)" fill="#d27979" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body position-relative px-0 pb-0">
                    <div class="map-search flex-col-center max-width-70 px-md-0 px-3">
                        <div class="search-group position-relative w-100">
                            <input type="text" name="" id="dropoff" placeholder="@lang('site.search_dropoff')">
                            <img src="{{asset('assets/images/svgs/search.svg')}}" alt="" class="icon">
                        </div>
                        <div class="flex-space blue mt-3 mb-4">
                            <div class="flex-align-center">
                                <img src="{{asset('assets/images/svgs/map-marker.svg')}}" alt="">
                                <span id="dropoff_label">@lang('site.cairo')</span>
                            </div>
                            <img src="{{asset('assets/images/svgs/check-circle-navy.svg')}}" alt="">
                        </div>
                    </div>
                    <div id="map2" style="height: 460px; width: 100%;"></div>
                    <div class="flex-center">
                        <button type="submit" class="btn btn-navy shadow-none" data-dismiss="modal"
                                aria-label="Close">@lang('site.add_dropoff_location')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let pickupMap, dropoffMap, routeMap;
    let pickupMarker, dropoffMarker;
    let directionsService, directionsRenderer;

    function initMaps() {
        const defaultCenter = { lat: 30.0444, lng: 31.2357 };

        pickupMap = new google.maps.Map(document.getElementById("map1"), { center: defaultCenter, zoom: 13 });
        dropoffMap = new google.maps.Map(document.getElementById("map2"), { center: defaultCenter, zoom: 13 });
        routeMap = new google.maps.Map(document.getElementById("map"), { center: defaultCenter, zoom: 13 });

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({ map: routeMap });
    }

    function tryRoute() {
        const lat1 = parseFloat(document.getElementById("pickup_lat").value);
        const lng1 = parseFloat(document.getElementById("pickup_lng").value);
        const lat2 = parseFloat(document.getElementById("dropoff_lat").value);
        const lng2 = parseFloat(document.getElementById("dropoff_lng").value);

        if (!isNaN(lat1) && !isNaN(lat2)) {
            const origin = { lat: lat1, lng: lng1 };
            const destination = { lat: lat2, lng: lng2 };

            directionsService.route({
                origin,
                destination,
                travelMode: google.maps.TravelMode.DRIVING
            }, function(result, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsRenderer.setDirections(result);
                    const distance = result.routes[0].legs[0].distance.text;
                    const distanceInfo = document.getElementById("distance_info");
                    if (distanceInfo) {
                        distanceInfo.innerText = `@lang('site.distance_label'): ${distance}`;
                    }
                } else {
                    const distanceInfo = document.getElementById("distance_info");
                    if (distanceInfo) {
                        distanceInfo.innerText = "@lang('site.route_not_found')";
                    }
                }
            });
        }
    }

    $('#PickupMapModal').on('shown.bs.modal', function () {
        const input = document.getElementById("pickup");
        const input_label = document.getElementById("pickup_label");
        if (!input.dataset.autocompleteAttached) {
            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;

                const loc = place.geometry.location;
               
                document.getElementById("pickup_lat").value = loc.lat();
                document.getElementById("pickup_lng").value = loc.lng();
                document.getElementById("pick_up_address").value = place.formatted_address;
 pickup_label.innerHTML= place.formatted_address;
                if (pickupMarker) pickupMarker.setMap(null);
                pickupMarker = new google.maps.Marker({ position: loc, map: pickupMap, label: "P" });
                pickupMap.setCenter(loc);
                tryRoute();
            });
            input.dataset.autocompleteAttached = true;
        }
    });

    $('#DropofMapModal').on('shown.bs.modal', function () {
        const input = document.getElementById("dropoff");
        const dropoff_label = document.getElementById("dropoff_label");
        if (!input.dataset.autocompleteAttached) {
            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;

                const loc = place.geometry.location;
                document.getElementById("dropoff_lat").value = loc.lat();
                document.getElementById("dropoff_lng").value = loc.lng();
                document.getElementById("drop_of_address").value = place.formatted_address;
                 dropoff_label.innerHTML= place.formatted_address;

                if (dropoffMarker) dropoffMarker.setMap(null);
                dropoffMarker = new google.maps.Marker({ position: loc, map: dropoffMap, label: "D" });
                dropoffMap.setCenter(loc);
                tryRoute();
            });
            input.dataset.autocompleteAttached = true;
        }
    });
</script>


   <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCWsYnE6Jsdi4SGqw50cYLDcSYI8eAYL7k&libraries=places&callback=initMaps&language={{ app()->getLocale() }}">
</script>
    <script src="{{asset('assets/tiny/js/daterangepicker.js')}}"></script>
    <script>
        $('.drgpicker').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    </script>
    <script>
        $(document).ready(function(e){
            $('#ton_price').on('change',function(e){
                $('#total_price').val(($('#ton_price').val() * $('#weight_ton').val()).toFixed(2));
            });
            $('#weight_ton').on('change',function(e){
                $('#total_price').val(($('#ton_price').val() * $('#weight_ton').val()).toFixed(2));
            });
        });
    </script>
    <script>
        function selected_car(id ){
            $('#truck').val(id);
        }
        $('#choose_car').on('click',function(e){
            e.preventDefault();
            let id=$('#truck').val();
            $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'GET',
                url: "{{ route('admin.trucks.getById') }}",
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': id},
                success: function(response) {
                    //console.log(response.data);
                    // $('#car_id').val(car.id);
                    // $('#car_name').val(car.name);
                },
                error: function(data) {
                   //console.log(data);
                }
            });
        });
    </script>




@endsection
