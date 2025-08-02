@extends('layouts.admin.app')
@section('title',' | ' .  __('site.add') .' '. __('site.the_driver'))
@section('styles')
    <!-- IntlTelInput -->
    <link rel="stylesheet" href="{{asset('assets/css/flag-icon-css/flag-icon.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/css/intlTelInput.css" rel="stylesheet">
    <!-- Uppy Dropzone -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/uppy.min.css')}}">
@endsection

@section('content')
@if ($errors->any())
<div class="row mb-2">
    <div class="card card-light">
        <div class="card-body">
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endif
<h2 class="section-title mb-5">@lang('site.add') @lang('site.the_driver')</h2>
<form action="{{route('admin.drivers.store')}}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('post')
    <div class="row">
        <div class="col-xl-7 col-lg-9 co-12">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="dropzone-field">
                        <label for="">Profile Picture</label>
                        <input type="file" class="" name="image" id="image">
                        <div class="drag-drop-area" id="drag-drop-area"></div>
                        @error('image')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="input-group">
                <label for="name">@lang('site.name')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/person-fill-bland.svg')}}" alt="">
                    <input type="text" id="name" name="name"
                           placeholder="@lang('site.please_enter') @lang('site.name')" value="{{old('name')}}" required>
                </div>
                @error('name')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="intl_phone">@lang('site.phone')</label>
                <div class="international-phone gray">
                    <div class="position-relative">
                        <input type="tel" id="intl_phone" class="form-control phone"
                               placeholder="@lang('site.please_enter') @lang('site.phone')" value="{{old('phone')}}">
                        <img src="{{asset('assets/images/svgs/perm-phone.svg')}}" alt="" class="icon">
                    </div>
                </div>
                <input type="hidden" name="phone" id="phone" value="{{old('phone')}}">
                @error('phone')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="password">@lang('site.password')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/lock.svg')}}" alt="">
                    <input type="password" id="password" name="password"
                           placeholder="@lang('site.please_enter') @lang('site.password')"
                           value="{{old('password')}}" required>
                </div>
                @error('password')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="password_confirmation">@lang('site.con_password')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/lock.svg')}}" alt="">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           placeholder="@lang('site.please_enter') @lang('site.con_password')"
                           required value="{{old('password_confirmation')}}">
                </div>
                @error('password_confirmation')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="national_id">@lang('site.national_id')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/record.svg')}}" alt="">
                    <input type="text" id="national_id" name="national_id"
                           placeholder="@lang('site.please_enter') @lang('site.national_id')"
                           value="{{old('national_id')}}" required>
                </div>
                @error('national_id')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="dropzone-field">
                        <label for="">@lang('site.national_id_image_f')</label>
                        <input type="file" class="" name="national_id_image_f"
                               id="national_id_image_f">
                        <div class="drag-drop-area" id="drag-drop-area"></div>
                        @error('national_id_image_f')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="dropzone-field">
                        <label for="">@lang('site.national_id_image_b')</label>
                        <input type="file" class="" name="national_id_image_b"
                               id="national_id_image_b">
                        <div class="drag-drop-area" id="drag-drop-area"></div>
                        @error('national_id_image_b')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="input-group">
                <label for="driving_license_number">@lang('site.driving_license_number')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/commercial-filled-card.svg')}}" alt="">
                    <input type="text" id="driving_license_number" name="driving_license_number"
                           placeholder="@lang('site.please_enter') @lang('site.driving_license_number')"
                           value="{{old('driving_license_number')}}" required>
                </div>
                @error('driving_license_number')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="dropzone-field">
                        <label for="">Driver's License (Front)</label>
                        <input type="file" class="" name="" id="">
                        <div class="drag-drop-area" id="drag-drop-area"></div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="dropzone-field">
                        <label for="">Driver's License (Back)</label>
                        <input type="file" class="" name="" id="">
                        <div class="drag-drop-area" id="drag-drop-area"></div>
                    </div>
                </div>
            </div>
            <div class="input-group">
                <label for="car_id">@lang('site.car_type')</label>
                <div class="position-relative">
                    <select id="car_id" name="car_id" class="select2 no-arrow">
                        <option value="" selected disabled>@lang('site.choose_car')</option>
                        @foreach ($cars as $car)
                            <option value="{{$car->id}}"{{old('car_id')==$car->id? 'selected' : ''}}>
                                @if(app()->getLocale()=='ar'){{$car->name_ar}}@else{{$car->name_en}}@endif
                            </option>
                        @endforeach
                    </select>
                    <img src="{{asset('assets/images/svgs/car-solid.svg')}}" alt="" class="icon">
                </div>
                @error('car_id')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="track_number">@lang('site.track_number')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/commercial-filled-card.svg')}}" alt="">
                    <input type="text" id="track_number" name="track_number"
                           placeholder="@lang('site.please_enter') @lang('site.track_number')"
                           value="{{old('track_number')}}" required>
                </div>
                @error('track_number')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="track_number">@lang('site.track_license_number')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/commercial-filled-card.svg')}}" alt="">
                    <input type="text" id="track_license_number" name="track_license_number"
                           placeholder="@lang('site.please_enter') @lang('site.track_license_number')"
                           value="{{old('track_license_number')}}" required>
                </div>
                @error('track_license_number')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="dropzone-field">
                        <label for="">Truck License (Front)</label>
                        <input type="file" class="" name="" id="">
                        <div class="drag-drop-area" id="drag-drop-area"></div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="dropzone-field">
                        <label for="">Truck License (Back)</label>
                        <input type="file" class="" name="" id="">
                        <div class="drag-drop-area" id="drag-drop-area"></div>
                    </div>
                </div>
            </div>
            <h4 class="inner-title mt-2 mb-4">Truck Images</h4>
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="dropzone-field">
                        <label for="">Front Picture</label>
                        <input type="file" class="" name="" id="">
                        <div class="drag-drop-area" id="drag-drop-area"></div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="dropzone-field">
                        <label for="">Back Picture</label>
                        <input type="file" class="" name="" id="">
                        <div class="drag-drop-area" id="drag-drop-area"></div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="dropzone-field">
                        <label for="">Side Picture</label>
                        <input type="file" class="" name="" id="">
                        <div class="drag-drop-area" id="drag-drop-area"></div>
                    </div>
                </div>
            </div>
            <div class="input-group">
                <label for="company_id">@lang('site.the_shipping_company')</label>
                <div class="position-relative">
                    <select name="company_id" id="company_id" class="select2 no-search no-arrow">
                        <option value="" selected disabled>@lang('site.choose_driver_company')</option>
                        @foreach ($companies as $company)
                            <option value="{{$company->id}}" {{old('company_id')==$company->id ? 'selected' : ''}}>
                                {{$company->name}}
                            </option>
                        @endforeach
                    </select>
                    <i class="fad fa-truck-loading"></i>
                </div>
                @error('company_id')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="checkbox-group">
                <label for="">@lang('site.status')</label>
                <ul class="checkbox-list">
                    <li>
                        <div class="form-check">
                            <input class="form-check-input transparent" type="checkbox" id="active" name="active" checked>
                            <label class="form-check-label" for="active">@lang('site.enable')</label>
                        </div>
                        @error('active')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </li>
                    <li>
                        <div class="form-check">
                            <input class="form-check-input transparent" type="checkbox" id="revision" name="revision">
                            <label class="form-check-label" for="revision">@lang('site.revision')</label>
                        </div>
                        @error('revision')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </li>
                    <li>
                        <div class="form-check">
                            <input class="form-check-input transparent" type="checkbox" id="vip" name="vip">
                            <label class="form-check-label" for="vip">@lang('site.vip')</label>
                        </div>
                        @error('vip')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </li>
                </ul>
            </div>
            <button type="submit" class="btn btn-navy shadow-none min-width-170 mt-4">@lang('site.save') @lang('site.driver')</button>
        </div>
    </div>
</form>

@endsection

@section('scripts')
    <!-- Uppy Dropzone -->
    <script src="{{asset('assets/tiny/js/uppy.min.js')}}"></script>
    <!-- Uppy Dropzone Playground(Config, Options, ...ect) -->
    <script src="{{asset('assets/js/uppy-init.js')}}"></script>
    <!-- IntlTelInput -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.7/js/intlTelInput.js"></script>
    <script>
        $(document).ready(function(){
            let country_codes= <?php echo json_encode( \App\Models\Country::select('country_code')->get()); ?>;
            let countries=[];
            for(var i=0;i<country_codes.length;i++){
                countries.push(country_codes[i].country_code);
            }
            $(".phone").intlTelInput({
                rtl: true,
                initialCountry: "eg",
                autoHideDialCode:false,
                allowDropdown:false,
                nationalMode: true,
                numberType: "MOBILE",
                onlyCountries:countries,// ['us', 'gb', 'ch', 'ca', 'do'],
                preferredCountries:['eg','sa','ue'],// ['sa', 'ae', 'qa','om','bh','kw','ma'],
                preventInvalidNumbers: true,
                separateDialCode: true ,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/js/utils.js",
            });

            $(".phone").on('change',function(e){
                e.preventDefault();
                $('#phone').val($(".phone").intlTelInput("getNumber"));
            });
            $(".intl-tel-input .country-list").on('click',function(e){
                e.preventDefault();
                $('#phone').val($(".phone").intlTelInput("getNumber"));
            });

            $(".intl-tel-input.allow-dropdown .flag-container").on('click',function(e){
                // e.stopPropagation();
                $(this).toggleClass("dropdown-opened");
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.intl-tel-input.allow-dropdown .flag-container').length) {
                    $('.intl-tel-input.allow-dropdown .flag-container').removeClass('dropdown-opened');
                }
            });
        });
    </script>

    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCWsYnE6Jsdi4SGqw50cYLDcSYI8eAYL7k&callback=initMap&language={{app()->getLocale()}}">
    </script>
    <script>
        //import { faBus } from "@fortawesome/free-solid-svg-icons";
        let map, map1, map2, activeInfoWindow, markers = [];
        var previousMarker, previousMarker1;
        var directionsService, directionsDisplay;
        /* ----------------------------- Initialize Map ----------------------------- */
        function drawPath(directionsService, directionsDisplay, start, end) {
            directionsService.route({
                origin: start,
                destination: end,
                optimizeWaypoints: true,
                travelMode: google.maps.DirectionsTravelMode.WALKING
            }, function (response, status) {
                if (status === 'OK') {
                    directionsDisplay.setDirections(response);
                    //console.log(response);
                    var infowindow = new google.maps.InfoWindow({
                        content: "@lang('site.drop_of_address')<br>" + " " + response.routes[0].legs[0].distance.text

                    });//window.alert('drawed');
                } else {
                    window.alert('Problem in showing direction due to ' + status);
                }
            });
        }
        /* ----------------------------- Initialize Map ----------------------------- */
        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 30.036053390817127,
                    lng: 31.236625493518176,
                },
                zoom: 16,
                mapTypeId: 'terrain'
            });
            map1 = new google.maps.Map(document.getElementById("map1"), {
                center: {
                    lat: 30.036053390817127,
                    lng: 31.236625493518176,
                },
                zoom: 16,
                mapTypeId: 'terrain'
            });
            map1.addListener("click", function (event) {
                mapClicked1(event);
            });
            map2 = new google.maps.Map(document.getElementById("map2"), {
                center: {
                    lat: 30.036053390817127,
                    lng: 31.236625493518176,
                },
                zoom: 16,
                mapTypeId: 'terrain'
            });
            map2.addListener("click", function (event) {
                mapClicked2(event);
            });

            initMarkers();
        }

        /* --------------------------- Initialize Markers --------------------------- */
        /*  */

        /* ------------------------- Handle Map Click Event -------------------------
        function mapClicked(event) {
        //                //console.log(map);
            let myLatlng={lat:event.latLng.lat(),  lng:event.latLng.lng()};
            //console.log(myLatlng);
            const marker = new google.maps.Marker({
                position: myLatlng,
                map,
                title: "Click to zoom",
                animation: google.maps.Animation.BOUNCE,
                draggable: true,

              });
               map.addListener("center_changed", () => {
                // 3 seconds after the center of the map has changed, pan back to the
                // marker.
                window.setTimeout(() => {
                  map.panTo(marker.getPosition());
                }, 3000);
              });
        }*/

        function mapClicked1(event) {
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'latLng': event.latLng
            }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        //console.log(results[0].formatted_address);
                        $('#pick_up_address').val(results[0].formatted_address);
                    }
                }
            });
            let myLatlng1 = { lat: event.latLng.lat(), lng: event.latLng.lng() };
            $('#pick_up_late').val(myLatlng1.lat);
            $('#pick_up_long').val(myLatlng1.lng);
            //console.log(myLatlng1);
            var infowindow = new google.maps.InfoWindow({
                content: "@lang('site.pick_up_address')"
            });
            if (previousMarker && previousMarker.setMap) {
                previousMarker.setMap(null);
            }
            previousMarker = new google.maps.Marker({
                position: myLatlng1,
                map1,
                title: "@lang('site.pick_up_address')",
                animation: google.maps.Animation.BOUNCE,
                draggable: true,

            });
            google.maps.event.addListener(map1, 'click', function (event) {
                placeMarker(event.latLng);
            });

            function placeMarker(location) {
                if (previousMarker && previousMarker.setMap) {
                    previousMarker.setMap(null);
                }
                previousMarker = new google.maps.Marker({
                    position: location,
                    map: map1,
                    title: "@lang('site.pick_up_address')",
                    animation: google.maps.Animation.BOUNCE,
                    draggable: true,
                });
                infowindow.open(map1, previousMarker);
            }
            //console.log('add markers');
            map2.addListener("center_changed", () => {
                // 3 seconds after the center of the map has changed, pan back to the
                // marker.
                window.setTimeout(() => {
                    map2.panTo(marker3.getPosition());
                }, 3000);
            });
            map1.addListener("center_changed", () => {
                // 3 seconds after the center of the map has changed, pan back to the
                // marker.
                window.setTimeout(() => {
                    map1.panTo(marker1.getPosition());
                }, 3000);
            });
            /* map.addListener("center_changed", () => {
              // 3 seconds after the center of the map has changed, pan back to the
              // marker.
              window.setTimeout(() => {
                map.panTo(marker2.getPosition());
              }, 3000);
            });*/
        }
        function mapClicked2(event) {
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'latLng': event.latLng
            }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        //console.log(results[0].formatted_address);
                        $('#drop_of_address').val(results[0].formatted_address);
                    }
                }
            });
            let myLatlng1 = { lat: event.latLng.lat(), lng: event.latLng.lng() };
            $('#drop_of_late').val(myLatlng1.lat);
            $('#drop_of_long').val(myLatlng1.lng);
            //console.log(myLatlng1);
            var infowindow = new google.maps.InfoWindow({
                content: "@lang('site.drop_of_address')"
            });
            if (previousMarker1 && previousMarker1.setMap) {
                previousMarker1.setMap(null);
            }
            previousMarker1 = new google.maps.Marker({
                position: myLatlng1,
                map2,
                title: "@lang('site.drop_of_address')",
                animation: google.maps.Animation.BOUNCE,
                draggable: true,

            });
            google.maps.event.addListener(map2, 'click', function (event) {
                placeMarker(event.latLng);
            });

            function placeMarker(location) {
                if (previousMarker1 && previousMarker1.setMap) {
                    previousMarker1.setMap(null);
                }
                previousMarker1 = new google.maps.Marker({
                    position: location,
                    map: map2,
                    title: "@lang('site.drop_of_address')",
                    animation: google.maps.Animation.BOUNCE,
                    draggable: true,
                });
                infowindow.open(map2, previousMarker1);
            }
            /*map2.addListener("center_changed", () => {
             // 3 seconds after the center of the map has changed, pan back to the
             // marker.
             window.setTimeout(() => {
               map2.panTo(marker3.getPosition());
             }, 3000);
           });*/
            map1.addListener("center_changed", () => {
                // 3 seconds after the center of the map has changed, pan back to the
                // marker.
                window.setTimeout(() => {
                    map1.panTo(marker1.getPosition());
                }, 3000);
            });

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
            let mylatelng = { lat: parseFloat($('#pick_up_late').val()), lng: parseFloat($('#pick_up_long').val()) };
            //console.log(myLatlng1)
            //console.log(mylatelng)
            drawPath(directionsService, directionsDisplay, mylatelng, myLatlng1);

        }
        /* ------------------------ Handle Marker Click Event -----------------------
    function markerClicked(marker, index) {
    //console.log(map);
    //console.log(marker.position.lat());
    //console.log(marker.position.lng());
      const marker3 = new google.maps.Marker({
        position: myLatlng1,
        map2,
        title: "@lang('site.drop_of_address')",
            animation: google.maps.Animation.BOUNCE,
            draggable: true,

          });
          infowindow.open(map2,marker3);
          //console.log('add markers');
    }*/

        /* ----------------------- Handle Marker DragEnd Event ----------------------
        function markerDragEnd(event, index) {
            //console.log(map);
            //console.log(event.latLng.lat());
            //console.log(event.latLng.lng());
        }


        var directionsService = new google.maps.DirectionsService();
        function plotDirections(start, end) {
          var TravelMode = 'DRIVING';
          var request = {
            origin: start,
            destination: end,
            travelMode: google.maps.DirectionsTravelMode[TravelMode],
            provideRouteAlternatives: false
          };

          directionsService.route(request, function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
              var routes = response.routes;
              var colors = ['green', 'blue'];
              var directionsDisplays = [];

              // Reset the start and end variables to the actual coordinates
              start = response.routes[0].legs[0].start_location;
              end = response.routes[0].legs[0].end_location;

              // Loop through each route
              for (var i = 0; i < routes.length; i++) {
                var directionsDisplay = new google.maps.DirectionsRenderer({
                  map: map,
                  directions: response,
                  routeIndex: i,
                  draggable: true,
                  polylineOptions: {
                    strokeColor: colors[i],
                    strokeWeight: 4,
                    strokeOpacity: .3
                  }
                });
                // Push the current renderer to an array
                directionsDisplays.push(directionsDisplay);

                // Listen for the directions_changed event for each route
                google.maps.event.addListener(directionsDisplay, 'directions_changed', (function(directionsDisplay, i) {
                  return function() {
                    var directions = directionsDisplay.getDirections();
                    //alert(JSON.stringify(directions));
                    //alert(directions.routes[0].legs[0].start_location.lat());

                    var new_start = directions.routes[0].legs[0].start_location;
                    var new_end = directions.routes[0].legs[0].end_location;
                    //alert("new_start : "+new_start+ "start :" +start);
                    if ((new_start.toString() !== start.toString()) || (new_end.toString() !== end.toString())) {
                    // Remove every route from map
                      for (var j = 0; j < directionsDisplays.length; j++) {
                            directionsDisplays[j].setMap(null);
                      }
                    // Redraw routes with new start/end coordinates
                      plotDirections(new_start, new_end);
                    }
                  }
                })(directionsDisplay, i)); // End listener
              } // End route loop
            }
          });
        }*/
    </script>
@endsection
