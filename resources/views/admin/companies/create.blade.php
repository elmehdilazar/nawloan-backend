@extends('layouts.admin.app')
@section('title',' | ' . __('site.add') .' '. __('site.the_shipping_company'))
@section('styles')
    <!-- IntlTelInput -->
    <link rel="stylesheet" href="{{asset('assets/css/flag-icon-css/flag-icon.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/css/intlTelInput.css" rel="stylesheet">
    <!-- Uppy Dropzone -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/uppy.min.css')}}">
@endsection

@section('content')
    <h2 class="section-title mb-5">@lang('site.add') @lang('site.shipping_company')</h2>
    <form action="{{route('admin.companies.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('post')
        <div class="row">
            <div class="col-xl-7 col-lg-9 co-12">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="dropzone-field">
                            <label for="">Company logo</label>
                            <input type="file" class="" name="image" id="image">
                            <div class="drag-drop-area" id="drag-drop-area"></div>
                            @error('image')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="input-group">
                    <label for="name">@lang('site.company_name')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/person-fill-bland.svg')}}" alt="">
                        <input type="text" id="name" name="name"
                               placeholder="@lang('site.please_enter') @lang('site.company_name')" value="{{old('name')}}" required>
                    </div>
                    @error('name')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="intl_phone">@lang('site.phone_n')</label>
                    <div class="international-phone gray">
                        <div class="position-relative">
                            <input type="tel" id="intl_phone" class="form-control phone"
                                   placeholder="@lang('site.please_enter') @lang('site.phone_n')" value="{{old('phone')}}">
                            <img src="{{asset('assets/images/svgs/perm-phone.svg')}}" alt="" class="icon">
                        </div>
                    </div>
                    <input type="hidden" name="phone" id="phone" value="{{old('phone')}}">
                    @error('phone')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="email">@lang('site.email')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/envelope-solid.svg')}}" alt="">
                        <input type="email" name="email" id="email"
                               placeholder="@lang('site.please_enter') @lang('site.email')" value="{{old('email')}}">
                    </div>
                    @error('email')
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
                    <label for="commercial_record">@lang('site.commercial_record')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/record.svg')}}" alt="">
                        <input type="text" id="commercial_record" name="commercial_record"
                               placeholder="@lang('site.please_enter') @lang('site.commercial_record')"
                               value="{{old('commercial_record')}}" required>
                    </div>
                    @error('commercial_record')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="dropzone-field">
                            <label for="">@lang('site.commercial_record_image_f')</label>
                            <input type="file" class="" name="commercial_record_image_f"
                                   id="commercial_record_image_f">
                            <div class="drag-drop-area" id="drag-drop-area"></div>
                            @error('commercial_record_image_f')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="dropzone-field">
                            <label for="">@lang('site.commercial_record_image_b')</label>
                            <input type="file" class="" name="commercial_record_image_b"
                                   id="commercial_record_image_b">
                            <div class="drag-drop-area" id="drag-drop-area"></div>
                            @error('commercial_record_image_b')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="input-group">
                    <label for="tax_card">@lang('site.company_tax_card')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/commercial-filled-card.svg')}}" alt="">
                        <input type="text" id="tax_card" name="tax_card"
                               placeholder="@lang('site.please_enter') @lang('site.company_tax_card')"
                               value="{{old('tax_card')}}" required>
                    </div>
                    @error('tax_card')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="dropzone-field">
                            <label for="">@lang('site.tax_card_image_f')</label>
                            <input type="file" class="" name="tax_card_image_f"
                                   id="tax_card_image_f">
                            <div class="drag-drop-area" id="drag-drop-area"></div>
                            @error('tax_card_image_f')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="dropzone-field">
                            <label for="">@lang('site.tax_card_image_b')</label>
                            <input type="file" class="" name="tax_card_image_f"
                                   id="tax_card_image_f">
                            <div class="drag-drop-area" id="drag-drop-area"></div>
                            @error('tax_card_image_f')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <h4 class="inner-title mt-4 mb-4">@lang('site.company_location')</h4>
                <div class="input-group">
                    <label for="" class="">@lang('site.address')</label>
                    <a href="" class="open-modal position-relative before-icon" data-toggle="modal"
                       data-target="#LocationMapModal">
                        <img src="{{asset('assets/images/svgs/map-marker.svg')}}" alt="" class="icon">
                        <input type="text" id="location" name="location"
                               placeholder="@lang('site.please_enter') @lang('site.address')"
                               value="{{old('location')}}" readonly>
                    </a>
                    @error('location')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="input-group">
                            <label for="" class="">Longitude</label>
                            <div class="input-group">
                                <input class="" type="text" id="longitude" name="longitude"
                                       placeholder="Longitude" value="{{old('longitude')}}">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fad fa-map-marked-alt"></i>
                                    </div>
                                </div>
                            </div>
                            @error('longitude')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="input-group">
                            <label for="" class="">Latitude</label>
                            <div class="input-group">
                                <input class="" type="text" id="latitude" name="latitude"
                                       placeholder="Latitude" value="{{old('latitude')}}">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fad fa-map-marked-alt"></i>
                                    </div>
                                </div>
                            </div>
                            @error('latitude')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
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
                            @error('active')
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
                <h4 class="inner-title mt-5 mb-4">@lang('site.bank_info')</h4>
                <div class="input-group">
                    <label for="bank_name">@lang('site.bank_name')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/bank.svg')}}" alt="">
                        <input type="text" id="bank_name" name="bank_name"
                               placeholder="@lang('site.please_enter') @lang('site.bank_name')"
                               value="{{old('bank_name')}}">
                    </div>
                    @error('bank_name')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="branch_name">@lang('site.branch_name')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/bank.svg')}}" alt="">
                        <input type="text" id="branch_name" name="branch_name"
                               placeholder="@lang('site.please_enter') @lang('site.branch_name')"
                               value="{{old('branch_name')}}">
                    </div>
                    @error('branch_name')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.account_holder_name')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/account.svg')}}" alt="">
                        <input type="text" id="account_holder_name" name="account_holder_name"
                               placeholder="@lang('site.account_holder_name')" value="{{old('account_holder_name')}}">
                    </div>
                    @error('account_holder_name')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.account_number')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/credits.svg')}}" alt="">
                        <input type="text" id="account_number" name="account_number"
                               placeholder="@lang('site.please_enter') @lang('site.account_number')"
                               value="{{old('account_number')}}">
                    </div>
                    @error('account_number')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="soft_code">@lang('site.soft_code')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/credits.svg')}}" alt="">
                        <input type="text" id="soft_code" name="soft_code"
                               placeholder="@lang('site.please_enter') @lang('site.soft_code')"
                               value="{{old('soft_code')}}">
                    </div>
                    @error('soft_code')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="iban">@lang('site.iban')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/iban.svg')}}" alt="">
                        <input type="text" id="iban" name="iban"
                               placeholder="@lang('site.please_enter') @lang('site.iban')" value="{{old('iban')}}">
                    </div>
                    @error('iban')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="pay_date">@lang('site.pay_date')</label>
                    <div class="position-relative">
                        <input type="number" min="1" max="31" step="1"id="pay_date" name="pay_date"
                               placeholder="@lang('site.pay_date')"
                               value="{{old('pay_date')!='' ? old('pay_date') : 1}}">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    @error('pay_date')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-navy shadow-none min-width-170 mt-4">@lang('site.save') @lang('site.shipping_company')</button>
            </div>
        </div>
    </form>
    <!-- Start LocationMapModal Modal -->
    <div class="modal fade" id="LocationMapModal" tabindex="-1" role="dialog" aria-labelledby="LocationMapModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
            <div class="modal-content fog-background">
                <div class="bring-to-front">
                    <div class="modal-header flex-center">
                        <h4 class="modal-title text-navy mb-0">Select The Location</h4>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                <path id="Exclusion_23" data-name="Exclusion 23"
                                      d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                      transform="translate(-0.384 0.422)" fill="#d27979" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body position-relative px-0 pb-0">
                        <div class="map-search flex-col-center max-width-70 px-md-0 px-3 mb-4">
                            <div class="search-group position-relative w-100">
                                <input type="text" name="" id="" placeholder="Search For Location ...">
                                <img src="{{asset('assets/images/svgs/search.svg')}}" alt="" class="icon">
                            </div>
                        </div>
                        <div id="map" style="height: 460px; width: 100%;"></div>
                        <div class="flex-center">
                            <button type="submit" class="btn btn-navy shadow-none" data-dismiss="modal"
                                    aria-label="Close">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End LocationMapModal Modal -->

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
        let map, marker;

        function initMap() {
            const initialLatLng = { lat: 30.036053390817127, lng: 31.236625493518176 };
            const mapOptions = {
                center: initialLatLng,
                zoom: 16,
                mapTypeId: 'terrain'
            };
            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            marker = new google.maps.Marker({
                position: initialLatLng,
                map,
                title: "@lang('site.pick_up_address')",
                animation: google.maps.Animation.BOUNCE,
                draggable: true,
            });

            map.addListener('click', event => {
                placeMarker(event.latLng);
            });

            google.maps.event.addListener(marker, 'dragend', event => {
                placeMarker(event.latLng);
            });
        }

        function placeMarker(location) {
            marker.setPosition(location);
            map.panTo(location);

            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location }, (results, status) => {
                if (status === google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        const formattedAddress = results[0].formatted_address;
                        $('#location').val(formattedAddress);
                    }
                }
            });

            $('#latitude').val(location.lat());
            $('#longitude').val(location.lng());
        }
    </script>
@endsection
