<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->user()->id }}">
        <meta name="user-type" content="{{ auth()->user()->type }}">
        <meta name="user-name" content="{{ auth()->user()->name }}">
    @endauth
    <title>
        @if(app()->getLocale()=='ar')
            {{setting('app_name_ar')!='' ? setting('app_name_ar') : __('site.app_name')}}
            
        @else
            {{setting('app_name_en')!='' ? setting('app_name_en') : __('site.app_name')}}@endif @yield('title')
    </title>
    <!-- Favicon icon -->
    <meta name="theme-color" content="#768dea">
    <link rel="shortcut icon" href="{{setting('favoico') !='' ? asset(setting('favoico')) : asset('assets/images/favicon.png')}}">
  @if(app()->getLocale()=='ar')
            <link rel="stylesheet" href="{{asset('assets/css/app-rtl.css')}}">
              <link rel="stylesheet" href="{{asset('assets/css/app-rtl.css')}}">
       @endif
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/simplebar.css')}}">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/fontawesome.css')}}">
    <link rel="stylesheet" href="{{asset('assets/tiny/css/select2.css')}}">
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dropzone.css')}}">
    <link rel="stylesheet" href="{{asset('assets/tiny/css/jquery.steps.css')}}">
    <link rel="stylesheet" href="{{asset('assets/tiny/css/jquery.timepicker.css')}}">
    @yield('styles')
    <!-- App CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/app-light.css')}}" id="lightTheme">
    <link rel="stylesheet" href="{{asset('assets/tiny/css/app-dark.css')}}" id="darkTheme" disabled>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">

    {{--<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css">--}}
    {{--<link rel="stylesheet" href="{{asset('assets/css/flag-icon-css/flag-icon.min.css')}}">--}}
    {{--<link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/css/intlTelInput.css" rel="stylesheet">--}}
    <script src="https://cdn.tiny.cloud/1/mnj29klrtnj3dl6wh238jbr4bxbq3c4iqtnyfelx3974syvy/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    {{--@if(app()->getLocale()=='ar' )--}}
    {{--@elseif(app()->getLocale()== 'en')--}}
    {{--@endif--}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.27.2/axios.min.js"
            integrity="sha512-odNmoc1XJy5x1TMVMdC7EMs3IVdItLPlCeL5vSUPN2llYKMJ2eByTTAIiiuqLg+GdNr9hF6z81p27DArRFKT7A=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- SweetAlert Script -->
  
    @vite(['resources/js/app.js'])
</head>

<body class="vertical  light {{ app()->isLocale('ar') ? 'rtl' : '' }} ">
<!--=== Start Wrapper ===-->
<div class="wrapper">
    <!--=== Start TopNav ===-->
    @include('layouts.admin.sections.navbar')
    <!--=== End TopNav ===-->
    <!--=== Start SideBar ===-->
    @include('layouts.admin.sections.aside')
    <!--=== End SideBar ===-->
    <!--=== Start Main ===-->
    <main role="main" class="main-content {{request()->routeIs('admin.orders.show') ? 'yellow-background' : '' }}">
        <div class="container-fluid {{request()->routeIs('admin.orders.show') ? 'order-card' : '' }}">
        
            @yield('content')
        </div>
    </main>
    <!--=== End Main ===-->
    <div id="fcmPing" style="position:fixed;right:12px;bottom:12px;background:#16a34a;color:#fff;padding:8px 12px;border-radius:8px;display:none;z-index:9999"></div>

</div>
{{--@include('layouts.admin.sections.footer') --}}

@include('sweetalert::alert')
<script src="{{asset('assets/tiny/js/jquery.min.js')}}"></script>
<script src="{{asset('assets/tiny/js/popper.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
        integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{asset('assets/tiny/js/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/tiny/js/simplebar.min.js')}}"></script>
{{--    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>--}}
<script src="{{asset('assets/tiny/js/jquery.stickOnScroll.js')}}"></script>
<script src="{{asset('assets/tiny/js/tinycolor-min.js')}}"></script>
{{--<script src="{{asset('assets/tiny/js/config.js')}}"></script>--}}
<script src="{{asset('assets/tiny/js/d3.min.js')}}"></script>
<script src="{{asset('assets/tiny/js/topojson.min.js')}}"></script>
<script src="{{asset('assets/tiny/js/datamaps.all.min.js')}}"></script>
<script src="{{asset('assets/tiny/js/datamaps-zoomto.js')}}"></script>
<script src="{{asset('assets/tiny/js/datamaps.custom.js')}}"></script>
<script src="{{asset('assets/tiny/js/Chart.min.js')}}"></script>
<script src="{{asset('assets/tiny/js/gauge.min.js')}}"></script>
<script src="{{asset('assets/tiny/js/jquery.sparkline.min.js')}}"></script>
<script src="{{asset('assets/tiny/js/jquery.mask.min.js')}}"></script>
<script src="{{asset('assets/tiny/js/select2.min.js')}}"></script>
<script src="{{asset('assets/tiny/js/jquery.steps.min.js')}}"></script>
<script src="{{asset('assets/tiny/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/tiny/js/jquery.timepicker.js')}}"></script>
<script src="{{asset('assets/tiny/js/apps.js')}}"></script>

<script>
  // Reusable SweetAlert2 toast
  window.saToast = (icon = 'success', title = '', text = '') => {
    if (typeof Swal === 'undefined') return; // safety
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 2800,
      timerProgressBar: true,
      didOpen: (t) => {
        t.addEventListener('mouseenter', Swal.stopTimer);
        t.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });
    Toast.fire({ icon, title, text });
  };
</script>
<script>
    $(document).ready(function () {
        StickyHeader();
        $(window).on("scroll", function () {
            StickyHeader();
        });
    });
    function StickyHeader() {
        // Sticky Header
        if ($(".vertical .topnav").length) {
            var windowpos = $(this).scrollTop();
            if (windowpos >= 10) {
                $(".vertical .topnav").addClass("sticky");
            } else {
                $(".vertical .topnav").removeClass("sticky");
            }
        }
    }
</script>
<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    });

    $('.select2-multi').select2({
        multiple: true,
        theme: 'bootstrap4',
        placeholder: "Select",
        allowClear: true
    });

    // $('.drgpicker').daterangepicker({
    //     singleDatePicker: true,
    //     timePicker: true,
    //     showDropdowns: true,
    //     locale: {
    //         format: 'YYYY/MM/DD HH:mm:ss'
    //     }
    // });

    // image preview
    $(".image").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.image-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    $(".image1").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.image-preview1').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    $(".image2").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.image-preview2').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    $(".image3").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.image-preview3').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    $(".image4").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.image-preview4').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    setInterval(function() {
        // $("#notifications_count").load(window.location.href + " #notifications_count");
        $("#unreadNotifications").load(window.location.href + " #unreadNotifications");
        $("#Notifications").load(window.location.href + " #Notifications");
    }, 5000);

    // show the alert
    setTimeout(function() {
        $(".alert").alert('close');
    }, 300000);
</script>
<!-- Keep only ONE pair of Firebase compat scripts -->
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js"></script>

<script>
(function () {
  const firebaseConfig = {
    apiKey: "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w",
    authDomain: "nawloan-eff12.firebaseapp.com",
    projectId: "nawloan-eff12",
    messagingSenderId: "997400731253",
    appId: "1:997400731253:web:d0ae522e19b8fce924a23c",
  };
  const VAPID = "BKcLwEjrAedWHYKxK8yaxKIvOqGysObPboROGhiWEO8Kae1cBYooFWY7_Ghf_-wnO8tpmNkYc5_MaApffWQLmAw";

  const toast = (msg) => {
    const el = document.getElementById('fcmPing');
    if (!el) return;
    el.textContent = msg; el.style.display = 'block';
    setTimeout(()=> el.style.display='none', 3500);
  };

  (async () => {
    try {
      if (!('serviceWorker' in navigator)) throw new Error('No ServiceWorker support');
      if (!('Notification' in window)) throw new Error('No Notification API');

      // 1) Init Firebase once
      if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);
      const messaging = firebase.messaging();

      if (typeof firebase.messaging.isSupported === 'function' && !firebase.messaging.isSupported()) {
        console.warn('[FCM] Messaging not supported in this browser');
        return;
      }
  
      // 2) Bind foreground listener ASAP (before awaiting anything else)
      if (!window.__fcmBound) {
        window.__fcmBound = true;
        messaging.onMessage((payload) => {
        var audio = new Audio('{{url("public/audio_file.wav")}}');
audio.play();
           const t = payload.notification?.title || payload.data?.title || 'New notification';
    const b = payload.notification?.body  || payload.data?.body  || '';
    saToast('success', t, b);
          toast(payload.notification?.title +payload.notification?.body ? ' - '+payload.notification?.body : '');
        });
      }

      // 3) Register SW at the ROOT scope (no "/public" in the URL)
      const reg = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
      console.log('[SW] registered:', reg.scope);

      // 4) Ask permission
      const perm = await Notification.requestPermission();
      console.log('[FCM] permission:', perm);
      if (perm !== 'granted') return;

      // 5) Get token (use the SAME reg + your VAPID)
    const token = await messaging.getToken({ vapidKey: VAPID, serviceWorkerRegistration: reg });
      console.log('[FCM] token:', token);
      window._fcm_token = token; // handy for Postman tests
    
   if (token) {
    // 1) save to cookie so it persists across refreshes
    document.cookie = `fcm_token=${token}; path=/; max-age=${60*60*24*365}`;

    // 2) post it to the server to update the user record
    await fetch('/admin/fcm-token', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ token: token })
    });
}

    } catch (e) {
      console.error('[FCM] setup error:', e);
      toast('FCM setup error (see console)');
    }
  })();
})();

</script>


@yield('scripts')
</body>

</html>
