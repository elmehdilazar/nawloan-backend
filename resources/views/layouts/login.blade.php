<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@if(app()->getLocale()=='ar') {{setting('app_name_ar')!='' ? setting('app_name_ar') : __('site.app_name')}}
        @else
        {{setting('app_name_en')!='' ? setting('app_name_en') : __('site.app_name')}}@endif @yield('title')</title>
    <meta name="theme-color" content="#444">
    <!-- Favicon icon -->
    <link rel="shortcut icon" href="{{setting('favoico')!='' ? asset(setting('favoico')) : asset('uploads/img/logo.png')}}">
    <!-- FontAwesome CSS -->
    <link href="{{asset('assets/css/fontawesome.css')}}" rel="stylesheet">
    <!-- IntlTelInput -->
    <link rel="stylesheet" href="{{asset('assets/css/flag-icon-css/flag-icon.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/css/intlTelInput.css" rel="stylesheet">

    @if(app()->getLocale()=='ar')
        {{--<link rel="stylesheet" href="{{asset('assets/css/bootstrap-rtl.min.css')}}">--}}
        {{--<link rel="stylesheet" href="{{asset('assets/css/font-awesome-rtl.min.css')}}">--}}
    @else
        <link href="{{asset('assets/tiny/css/app-light.css')}}" rel="stylesheet" id="lightTheme">
        <link href="{{asset('assets/tiny/css/app-dark.css')}}" rel="stylesheet" id="darkTheme" disabled>
    @endif
    <!-- Custom CSS -->
    <link href="{{asset('assets/css/custom.css')}}?v={{ filemtime(public_path('assets/css/custom.css')) }}" rel="stylesheet">
    @yield('styles')
</head>

@php
    $themeClass = request()->cookie('mode') === 'dark' ? 'theme-dark dark' : 'theme-light light';
@endphp
<body class="vertical {{ $themeClass }} {{app()->getLocale()=='ar' ? 'rtl' : 'ltr'}}">
    @yield('content')

    <!--**********************************
    Scripts
    ***********************************-->
    @include('sweetalert::alert')

    <script src="{{asset('assets/tiny/js/jquery.min.js')}}"></script>
    <script src="{{asset('assets/tiny/js/popper.min.js')}}"></script>
    <script src="{{asset('assets/tiny/js/moment.min.js')}}"></script>
    <script src="{{asset('assets/tiny/js/bootstrap.min.js')}}"></script>
    @if(app()->getLocale()!='ar')
    <script src="{{asset('assets/tiny/js/tinycolor-min.js')}}"></script>
    <script src="{{asset('assets/tiny/js/config.js')}}?v={{ filemtime(public_path('assets/tiny/js/config.js')) }}"></script>
    <script>
        $(function () {
            $("#modeSwitcher").on("click", function (e) {
                e.preventDefault();
                if (typeof modeSwitch === "function") {
                    modeSwitch();
                    location.reload();
                }
            });
        });
    </script>
    @endif
    {{-- <script src="{{asset('assets/tiny/js/apps.js')}}"></script> --}}
    <script>
        // show the alert
        setTimeout(function() {
            $(".alert").alert('close');
        }, 300000);
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.7/js/intlTelInput.js"></script>
    <script>
        $(document).ready(function(){
            $('#intl_phone').val('') /*add this by mohammed v2*/
            let country_codes=<?php echo json_encode( \App\Models\Country::select('country_code')->get()); ?>;
            let countries=[];
            for(var i=0;i<country_codes.length;i++){
                countries.push(country_codes[i].country_code);
            }

            $(".phone").intlTelInput({
                initialCountry: "eg",
                autoHideDialCode:false,
                allowDropdown:false,
                nationalMode: true,
                numberType: "MOBILE",
                onlyCountries:countries,// ['us', 'gb', 'ch', 'ca', 'do'],
                preferredCountries:['eg','sa','ue'],// ['sa', 'ae', 'qa','om','bh','kw','ma'],
                preventInvalidNumbers: true,
                separateDialCode: true,

                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/js/utils.js"
            });

            $(".phone").on('change',function(e){
                e.preventDefault();
                $(".phone").intlTelInput("getNumber");
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
    @yield('scripts')
</body>

</html>
