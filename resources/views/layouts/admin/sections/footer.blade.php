<div class="footer">
    <div class="copyright">
        <p>@lang('site.copyright') <span style="color:#000">&copy; {{date('Y')}}</span> @lang('site.rights_reserved')
            <a href="{{route('home')}}">@if(setting('app_name_ar')!=null)
            @if(app()->getLocale()=='ar')
            {{setting('app_name_ar')}}
            @else
            @lang('site.app_name')
            @endif
            @elseif(setting('app_name_en')!=null)
            {{setting('app_name_en')}}
            @else
            @lang('site.app_name')
            @endif</a>
    </div>
</div>
  <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging-compat.js"></script>

<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('https://nawloan.net/public/firebase-messaging-sw.js')
            .then(function(registration) {
                // alert('Registration successful, scope is:', registration.scope);
                console.log('Registration successful, scope is:', registration.scope);
            }).catch(function(err) {
            console.log('Service worker registration failed, error:', err);
        });
    }
    firebase.initializeApp({
    apiKey: "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w",
  authDomain: "nawloan-eff12.firebaseapp.com",
     projectId: "nawloan-eff12",
  messagingSenderId: "997400731253",
 appId: "1:997400731253:web:d0ae522e19b8fce924a23c",
    });
          await navigator.serviceWorker.register('/firebase-messaging-sw.js');
    const messaging = firebase.messaging();
     messaging.onMessage((payload) => {  
      console.log('Foreground message:', payload);
      alert(payload.notification.body);
      

      writeStatus('Foreground push received.', true);
      // Optionally show a toast or update UI
    });
</script>
