@extends('layouts.admin.app')
@section('title',' | ' . __('site.edit') .' '. __('site.the_driver'))
@section('styles')
    <!-- IntlTelInput -->
    <link rel="stylesheet" href="{{asset('assets/css/flag-icon-css/flag-icon.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/css/intlTelInput.css" rel="stylesheet">
    <!-- Uppy Dropzone -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/uppy.min.css')}}">
@endsection
@section('content')
    <h2 class="section-title mb-5">@lang('site.edit') @lang('site.the_driver')</h2>
    @if(empty($user->userData->car))
        <div class="">
            <h4 class="badge badge-danger">
                @lang('site.data_not_complete')
            </h4>
        </div>
    @endif
    <form action="{{route('admin.drivers.update',$user->id)}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('put')
        <div class="row">
            <div class="col-xl-7 col-lg-9 co-12">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="image-input">
                            <label for="">Profile Picture</label>
                            <div class="imageUpload-wrapper"
                                 style="background-image: url({{asset('assets/images/no-image.jpg')}})">
                                <div id="imageUpload" style="background-image: url({{$user->userData->image !='' ? asset($user->userData->image) : asset('uploads/img/logo.png')}})">
                                    <input type="file" name="image" id="image" class="mediaFile">
                                    <label for="image"><i class="fad fa-pencil"></i></label>
                                    <button id="clear-input"><i class="fal fa-times"></i></button>
                                </div>
                            </div>
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
                               placeholder="@lang('site.please_enter') @lang('site.name')"
                               value="{{$user->name}}" required>
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
                                   placeholder="@lang('site.please_enter') @lang('site.phone')" value="{{$user->phone}}">
                            <img src="{{asset('assets/images/svgs/perm-phone.svg')}}" alt="" class="icon">
                        </div>
                    </div>
                    <input type="hidden" name="phone" id="phone" value="{{$user->phone}}">
                    @error('phone')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="password">@lang('site.password')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/lock.svg')}}" alt="">
                        <input type="password" id="password" name="password"
                               placeholder="@lang('site.please_enter') @lang('site.password')">
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
                               placeholder="@lang('site.please_enter') @lang('site.con_password')">
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
                               value="{{$user->userData->national_id}}" required>
                    </div>
                    @error('national_id')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="image-input wide">
                            <label for="">@lang('site.national_id_image_f')</label>
                            <div class="imageUpload-wrapper"
                                 style="background-image: url({{asset('assets/images/no-image.jpg')}})">
                                <div id="imageUpload" style="background-image: url({{$user->userData->national_id_image_f !='' ? asset($user->userData->national_id_image_f) : asset('uploads/img/logo.png')}})">
                                    <input type="file" name="national_id_image_f" id="national_id_image_f" class="mediaFile">
                                    <label for="national_id_image_f"><i class="fad fa-pencil"></i></label>
                                    <button id="clear-input"><i class="fal fa-times"></i></button>
                                </div>
                            </div>
                            @error('national_id_image_f')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="image-input wide">
                            <label for="">@lang('site.national_id_image_b')</label>
                            <div class="imageUpload-wrapper"
                                 style="background-image: url({{asset('assets/images/no-image.jpg')}})">
                                <div id="imageUpload" style="background-image: url({{$user->userData->national_id_image_b !='' ? asset($user->userData->national_id_image_b) : asset('uploads/img/logo.png')}})">
                                    <input type="file" name="national_id_image_b" id="national_id_image_b" class="mediaFile">
                                    <label for="national_id_image_b"><i class="fad fa-pencil"></i></label>
                                    <button id="clear-input"><i class="fal fa-times"></i></button>
                                </div>
                            </div>
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
                               value="{{$user->userData->driving_license_number}}" required>
                    </div>
                    @error('driving_license_number')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="car_id">@lang('site.car_type')</label>
                    <div class="position-relative">
                        <select id="car_id" name="car_id" class="select2 no-arrow">
                            <option value="" selected disabled>@lang('site.choose_car')</option>
                            @foreach ($cars as $car)
                                @if(!empty($user->userData->car))
                                    <option value="{{$car->id}}" {{$user->userData->car->id==$car->id? 'selected' : ''}}>
                                        @if(app()->getLocale()=='ar'){{$car->name_ar}}@else{{$car->name_en}}@endif
                                    </option>
                                @else
                                    <option value="{{$car->id}}" >
                                        @if(app()->getLocale()=='ar'){{$car->name_ar}}@else{{$car->name_en}}@endif
                                    </option>
                                @endif
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
                               value="{{$user->userData->track_number}}" required>
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
                               value="{{$user->userData->track_license_number}}" required>
                    </div>
                    @error('track_license_number')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="company_id">@lang('site.the_shipping_company')</label>
                    <div class="position-relative">
                        <select name="company_id" id="company_id" class="select2 no-search no-arrow">
                            <option value="" selected disabled>@lang('site.choose_driver_company')</option>
                            @foreach ($companies as $company)
                                <option value="{{$company->id}}" {{$user->userData->company_id==$company->id ? 'selected' : ''}}>{{$company->name}}</option>
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
                                <input class="form-check-input transparent" type="checkbox" id="active" name="active"
                                       @if($user->active==1) checked @endif>
                                <label class="form-check-label" for="active">@lang('site.enable')</label>
                            </div>
                            @error('active')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </li>
                        <li>
                            <div class="form-check">
                                <input class="form-check-input transparent" type="checkbox" id="revision" name="revision"
                                       @if($user->userData->revision==1) checked @endif> {{--edit by mohammed form @if($user->revision==1) --}}
                                <label class="form-check-label" for="revision">@lang('site.revision')</label>
                            </div>
                            @error('revision')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </li>
                        <li>
                            <div class="form-check">
                                <input class="form-check-input transparent" type="checkbox" id="vip" name="vip"
                                       @if($user->vip==1) checked @endif>
                                <label class="form-check-label" for="vip">@lang('site.vip')</label>
                            </div>
                            @error('vip')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </li>
                    </ul>
                </div>
                <button type="submit" class="btn btn-navy shadow-none min-width-170 mt-4">@lang('site.save')</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <!-- Custom ImageInput -->
    <script src="{{asset('assets/js/custom-imageInput.js')}}"></script>
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
@endsection
