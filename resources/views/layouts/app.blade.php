<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@if(app()->getLocale()=='ar') {{setting('app_name_ar')!='' ? setting('app_name_ar') : __('site.app_name')}} @else
            {{setting('app_name_en')!='' ? setting('app_name_en') : __('site.app_name')}}@endif @yield('title')</title>
    <meta name="theme-color" content="#444">
    <link rel="shortcut icon" href="{{setting('favoico')!='' ? asset(setting('favoico')) : asset('assets/images/favicon.png')}}">

    @if(app()->getLocale()=='ar')
        {{--<link href="{{asset('assets/css/bootstrap-rtl.min.css')}}" rel="stylesheet">--}}
        {{--<link href="{{ asset('assets/css/font-awesome-rtl.min.css') }}" rel="stylesheet">--}}
    @else
        {{--<link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">--}}
        {{--<link href="{{ asset('assets/css/font-awesome.min.css') }}" rel="stylesheet">--}}
        <link href="{{asset('assets/tiny/css/app-light.css')}}" rel="stylesheet">
        <link href="{{asset('assets/tiny/css/app-dark.css')}}" rel="stylesheet" disabled>
    @endif
    <link href="{{asset('assets/css/custom.css')}}" rel="stylesheet">
    <!-- Scripts -->
    {{--@vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">
                </ul>
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item {{ request()->routeIs('login') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif
                        {{--
                                                    @if (Route::has('register'))
                                                        <li class="nav-item">
                                                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                                        </li>
                                                    @endif --}}
                        {{--    @else
                               <li class="nav-item dropdown">
                                   <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                       {{ Auth::user()->name }}
                                   </a>

                                   <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                       <a class="dropdown-item" href="{{ route('logout') }}"
                                          onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                           {{ __('Logout') }}
                                       </a>

                                       <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                           @csrf
                                       </form>
                                   </div>
                               </li> --}}
                    @endguest
                    @auth
                        <li class="nav-item dropdown {{ request()->routeIs('front.account*')  ||
                                                request()->routeIs('verification*') ? 'active' : ''   }}">
                            <a id="navbarDropdown" class="nav-link waves-effect waves-light " href="#" role="button" data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false" v-pre style="padding: 0;">
                                <img src="{{Auth::user()->userData->image !='' ? asset(Auth::user()->userData->image) : asset('uploads/users/desfault.png')}}"
                                     alt="{{ Auth::user()->name }}" class="img-circle" style="height: 3rem;width: 3rem;    border-radius: 50%;">
                                {{ Auth::user()->name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-center" aria-labelledby="navbarDropdown">
                                {{--
                                <a href="#" class="dropdown-item"
                                    style="text-align: start;font-size: 1rem;font-weight: 600;direction:{{ LaravelLocalization::getCurrentLocaleDirection() }}"
                                    title="@lang('site.account')">
                                    <i class="fas fa-user fa-lg"></i> @lang('site.account')
                                </a> --}}
                                <a href="{{route('admin.index')}}" class="dropdown-item"
                                   style="text-align: start;font-size: 1rem;font-weight: 600;direction:{{ LaravelLocalization::getCurrentLocaleDirection() }}"
                                   title="@lang('site.dashboard')">
                                    <i class="fas fa-user fa-lg"></i> @lang('site.dashboard')
                                </a>
                                {{-- <a href="#" class="dropdown-item"
                                    style="text-align: start;font-size: 1rem;font-weight: 600;direction:{{ LaravelLocalization::getCurrentLocaleDirection() }}"
                                    title="@lang('site.reset_password')">
                                    <i class="fas fa-key fa-lg"></i> @lang('site.reset_password')
                                </a> --}}
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                                             document.getElementById('logout-form').submit();"
                                   style="text-align: start;font-size: 1rem;font-weight: 600;direction:{{ LaravelLocalization::getCurrentLocaleDirection() }}"
                                   title="@lang('site.logout')">
                                    <i class="fas fa-sign-out-alt fa-lg"></i> @lang('site.logout')
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endauth
                    <!-- Language Dropdown Menu -->
                    <li class="nav-item dropdown ">
                        <a class="nav-link waves-effect waves-light" data-toggle="dropdown" href="#" id="navbarDropdownLang"
                           aria-haspopup="true" aria-expanded="false" v-pre>
                            @if (app()->getLocale() == 'en')
                                <i class="flag-icon flag-icon-gb " style="font-size: 1.2rem;"></i>
                            @elseif (app()->getLocale()=='ar')
                                <i class="flag-icon flag-icon-sa " style="font-size: 1.2rem;"></i>
                            @endif
                        </a>
                        <div class="dropdown-menu @if (app()->getLocale() == 'ar') dropdown-menu-left @else dropdown-menu-right @endif p-0 "
                             style=" @if (app()->getLocale() == 'ar')
                                                   right: inherit; left: 0px; text-align:right; @else text-align:left;left: inherit; right:
                                                0px;@endif" aria-labelledby="navbarDropdownLang">
                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                <a rel="alternate" hreflang="{{ $localeCode }}" class="dropdown-item"
                                   href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                    @if ($localeCode == 'en')
                                        <i class="flag-icon flag-icon-gb "></i>
                                    @elseif ($localeCode=='ar')
                                        <i class="flag-icon flag-icon-sa  "></i>
                                    @endif
                                    @lang('site.'.$properties['name'].'')
                                </a>
                            @endforeach
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="py-4">
        @yield('content')
    </main>
</div>
@include('sweetalert::alert')
<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.3/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.3/firebase-messaging-compat.js"></script>

<script>
  // Firebase config
firebase.initializeApp({
  apiKey: "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w",
  authDomain: "nawloan-eff12.firebaseapp.com",
  projectId: "nawloan-eff12",
  messagingSenderId: "997400731253",
  appId: "1:997400731253:web:d0ae522e19b8fce924a23c"
});

  const messaging = firebase.messaging();

  // Register SW + ask for permission
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/firebase-messaging-sw.js')
      .then(reg => {
        console.log("[FCM] SW registered", reg);
        return Notification.requestPermission();
      })
      .then(permission => {
        if (permission === "granted") {
          return messaging.getToken();
        }
      })
      .then(token => {
        if (token) {
          console.log("[FCM] token:", token);
          // send token to backend
          axios.post("{{ url('/admin/fcm-subscribe') }}", { token })
            .then(res => console.log("[FCM] subscribed:", res.data))
            .catch(err => console.error("[FCM] subscribe failed", err));
        }
      });
  }

  // Foreground
  messaging.onMessage(payload => {
    console.log("[FCM] Foreground msg:", payload);
    showNotification(payload.notification);
  });

  // From SW
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.addEventListener("message", (event) => {
      if (event.data && event.data.__fcm) {
        showNotification(event.data.payload.notification);
      }
    });
  }

  function showNotification(data) {
    const title = data.title;
    const body = data.body;

    // Browser notification
    new Notification(title, { body, icon: data.icon || "/favicon.ico" });

    // Update badge count
    const badge = document.getElementById("notificationCount");
    if (badge) badge.textContent = (parseInt(badge.textContent || "0") + 1);
  }
</script>

</body>
</html>



