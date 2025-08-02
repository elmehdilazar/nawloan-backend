@extends('layouts.admin.app')
@section('title',' | ' .  __('site.add') .' '. __('site.the_customer'))
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
<h2 class="section-title mb-5">@lang('site.add') @lang('site.the_customer')</h2>
<div class="row">
    <div class="col-xl-7 col-lg-9 co-12">
        <form action="{{route('admin.customers.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('post')
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="dropzone-field">
                        <label for="">Profile picture</label>
                        <input type="file" class="" name="image" id="image">
                        <div class="drag-drop-area" id="drag-drop-area"></div>
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
                    <input type="text" id="name" name="name" placeholder="@lang('site.name')" value="{{old('name')}}" required>
                </div>
                @error('name')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="intl_phone">{{ __('site.phone') }}</label>
                <div class="international-phone gray">
                    <div class="position-relative">
                        <input type="tel" id="intl_phone" class="form-control phone"
                               placeholder="@lang('site.phone')" value="{{old('phone')}}">
                        <img src="{{asset('assets/images/svgs/perm-phone.svg')}}" alt="" class="icon">
                    </div>
                </div>
                <input type="hidden" name="phone" id="phone" value="{{old('phone')}}">
                @error('phone')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.password')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/lock.svg')}}" alt="">
                    <input type="password" id="password" name="password"
                           placeholder="@lang('site.password')" value="{{old('password')}}" required>
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
                           placeholder="@lang('site.con_password')" value="{{old('password_confirmation')}}" required>
                </div>
                @error('password_confirmation')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="checkbox-group">
                <label for="">@lang('site.status')</label>
                <ul class="checkbox-list">
                    <li>
                        <div class="form-check">
                            <input class="form-check-input transparent" type="checkbox" id="active" name="active"
                                   checked>
                            <label class="form-check-label" for="active">@lang('site.enable')</label>
                        </div>
                        @error('active')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </li>
                    <li>
                        <div class="form-check">
                            <input class="form-check-input transparent" type="checkbox" id="revision" name="">
                            <label class="form-check-label" for="revision">@lang('site.revision')</label>
                        </div>
                    </li>
                    <li>
                        <div class="form-check">
                            <input class="form-check-input transparent" type="checkbox"
                                   id="vip" name="vip" @if(old('vip')==1) checked @endif>
                            <label class="form-check-label" for="vip">@lang('site.vip')</label>
                        </div>
                        @error('vip')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </li>
                </ul>
            </div>
            <button type="submit" class="btn btn-navy shadow-none min-width-170 mt-4">@lang('site.add') @lang('site.customer')</button>
        </form>
    </div>
</div>

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
