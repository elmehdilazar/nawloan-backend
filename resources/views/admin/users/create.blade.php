@extends('layouts.admin.app')
@section('title',' | ' .  __('site.add') .' '. __('site.the_user'))
@section('styles')
    <!-- IntlTelInput -->
    <link rel="stylesheet" href="{{asset('assets/css/flag-icon-css/flag-icon.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/css/intlTelInput.css" rel="stylesheet">
    <!-- Uppy Dropzone -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/uppy.min.css')}}">
@endsection
@section('content')
    <h2 class="section-title mb-5">@lang('site.add') @lang('site.the_user')</h2>
    <form action="{{route('admin.users.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('post')
        <div class="row">
            <div class="col-xl-7 col-lg-9 co-12">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="dropzone-field">
                            <label for="">Profile picture</label>
                            <div class="drag-drop-area" id=""></div>
                            <input type="hidden" class="" name="image" id="image">
                        </div>
                        @error('image')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="input-group">
                    <label for="">@lang('site.name')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/person-fill-bland.svg')}}" alt="">
                        <input type="text" id="name" name="name"
                               placeholder="@lang('site.name')" value="{{old('name')}}" required>
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
                                   placeholder="@lang('site.phone')" value="{{old('phone')}}">
                            <img src="{{asset('assets/images/svgs/perm-phone.svg')}}" alt="" class="icon">
                        </div>
                    </div>
                    <input type="hidden" name="phone" id="phone">
                    @error('phone')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.password')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/lock.svg')}}" alt="">
                        <input type="password" id="password" name="password" placeholder="@lang('site.password')"
                               value="{{old('password')}}" required>
                    </div>
                    @error('password')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.con_password')</label>
                    <div class="position-relative">
                        <img src="{{asset('assets/images/svgs/lock.svg')}}" alt="">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               placeholder="@lang('site.con_password')" required value="{{old('password_confirmation')}}">
                    </div>
                    @error('password_confirmation')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="radio-group">
                    <label for="">Role</label>
                    <ul class="radio-list">
                        {{--<li>--}}
                        {{--<div class="form-radio">--}}
                        {{--    <input class="radio-input" name="type" type="radio" id="flexRadio1">--}}
                        {{--    <label class="radio-label" for="flexRadio1">Super Admin</label>--}}
                        {{--</div>--}}
                        {{--</li>--}}
                        <li>
                            <div class="form-radio">
                                <input class="radio-input" name="type" type="radio" id="admin" value="admin" checked>
                                <label class="radio-label" for="admin">@lang('site.admin')</label>
                            </div>
                        </li>
                        <li>
                            <div class="form-radio">
                                <input class="radio-input" name="type" type="radio" id="emp" value="emp">
                                <label class="radio-label" for="emp">@lang('site.employee')</label>
                            </div>
                        </li>
                    </ul>
                    @error('type')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="checkbox-group">
                    <label for="">@lang('site.status')</label>
                    <ul class="checkbox-list">
                        <li>
                            <div class="form-check">
                                <input class="form-check-input transparent" type="checkbox" id="active"
                                       name="active" checked>
                                <label class="form-check-label" for="active">@lang('site.enable')</label>
                            </div>
                        </li>
                    </ul>
                    @error('active')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
        </div>
        <h4 class="inner-title mt-3 mb-4">@lang('site.permissions')</h4>
        @php
            $models = ['users', 'customers','factories','drivers','driverCompanies','countries','cars','shipments_types','orders','offers','messages','transactions'];
            $titles = ['users','customers','factories','drivers','driverCompanies','countries_codes','cars','shipments_types','orders','offers','messages','transactions'];
            $maps = ['create', 'read', 'update','enable','disable','export'];
        @endphp
        <ul class="nav nav-pills gap-15 nav-fill mb-4" id="pills-tab" role="tablist">
            @foreach ($models as $index => $model)
                <li class="nav-item">
                    <a class="nav-link {{ $index == 0 ? ' active ' : ''}}" id="Users-tab" data-toggle="pill" href="#{{ $model }}" role="tab"
                       aria-controls="tab_{{ $index }}" aria-selected="true">@lang('site.'.$titles[$index])</a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content" id="pills-tabContent">
            @foreach ($models as $index => $model)
                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="{{$model}}" role="tabpanel" aria-labelledby="tab_{{ $index }}">
                    <div class="checkbox-group">
                        <ul class="checkbox-list">
                            @foreach ($maps as $index1=>$map)
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input transparent" type="checkbox" name="permissions[]" id="permission_{{$model.'_'. $index1}}"
                                               value="{{ $model . '_' . $map }}" checked>
                                        <label class="form-check-label" for="permission_{{$model.'_'. $index1}}">@lang('site.'.$map)</label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
            @error('permissions')
            <span class="text-danger"> {{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn btn-navy shadow-none min-width-170 mt-4">@lang('site.save')</button>
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
@endsection
