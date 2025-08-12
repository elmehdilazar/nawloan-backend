<nav class="topnav navbar navbar-light">
    <button type="button" class="navbar-toggler text-muted p-0 mr-3 collapseSidebar">
        <i class="far fa-bars"></i>
    </button>
    <form class="form-inline mr-auto searchform" action="{{route('admin.search')}}">
        <input class="form-control mr-sm-2 bg-transparent border-0 pl-4" type="search" name="search"
            placeholder="@lang('site.search_ufdc')" aria-label="Search" value="{{request()->search !='' ? request()->search : ''}}">
    </form>
    <ul class="nav">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#"
               id="langMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true"
               aria-expanded="false">
                <i class="far fa-globe-americas"></i>
                @lang(app()->getLocale())
                {{--@lang('site.'.LaravelLocalization::getCurrentLocaleName().'')--}}
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="langMenuLink">
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                <a class="dropdown-item {{($localeCode=='ar') ? 'text-arabic' : '' }}" rel="alternate" hreflang="{{ $localeCode }}"
                   href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                    @if ($localeCode == 'en')
                        <img src="{{asset('assets/images/flags/united-kingdom.svg')}}" alt="">
                    @elseif ($localeCode=='ar')
                        <img src="{{asset('assets/images/flags/saudi-arabia.svg')}}" alt="">
                    @endif
                    @lang('site.'.$properties['name'].'')
                </a>
                @endforeach
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="" href="#">
                <svg xmlns="http://www.w3.org/2000/svg" width="13.319" height="13.32"
                     viewBox="0 0 13.319 13.32">
                    <path id="Path_209570" data-name="Path 209570"
                          d="M8.253,2.019a5,5,0,1,0,7.066,7.066A6.666,6.666,0,1,1,8.253,2.019Z"
                          transform="translate(-2 -2.019)" fill="#bcbccb">
                </svg>
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle user-menu refreshing text-muted" id="navbarDropdownMenuLink" href="#" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="far fa-sync-alt"></i>
            </a>
            <div class="dropdown-menu {{app()->getLocale() =='ar' ? 'dropdown-menu-left' : 'dropdown-menu-right' }}"
                aria-labelledby="navbarDropdownMenuLink">
                <a href="{{route('admin.clear.views')}}" class="dropdown-item"
                    title="@lang('site.clear_views')">
                    <i class="far fa-sync-alt"></i> @lang('site.clear_views')
                </a>
                <a href="{{route('admin.clear.config')}}" class="dropdown-item"
                    title="@lang('site.clear_contig')">
                    <i class="far fa-sync-alt"></i> @lang('site.clear_config')
                </a>
                <a href="{{route('admin.clear.routes')}}" class="dropdown-item"
                    title="@lang('site.clear_routes')">
                    <i class="far fa-sync-alt"></i> @lang('site.clear_routes')
                </a>
                <a href="{{route('admin.clear.cache')}}" class="dropdown-item"
                    title="@lang('site.clear_cache')">
                    <i class="far fa-sync-alt"></i> @lang('site.clear_cache')
                </a>
                <a href="{{route('admin.clear.optimize')}}" class="dropdown-item"
                    title="@lang('site.clear_optimize')">
                    <i class="far fa-sync-alt"></i> @lang('site.clear_optimize')
                </a>
            </div>
        </li>
        <li class="nav-item dropdown d-lg-flex">
            <a class="nav-link dropdown-toggle messages-toggle text-muted" href="#" id="messagesDropdownMenu" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="15.999" height="15.999"
                     viewBox="0 0 15.999 15.999">
                    <path id="Chat_Icon" data-name="Chat Icon"
                          d="M-1234.693.89A7.944,7.944,0,0,1-1236,1,7.286,7.286,0,0,1-1239.8-.021c.1,0,.2.021.305.021,4.16,0,7.624-2.652,8.353-6.139A3.788,3.788,0,0,1-1230-3.5a3.739,3.739,0,0,1-1.012,2.5h.012V3ZM-1245-4.578a4.872,4.872,0,0,1-1-2.921c0-3.038,2.91-5.5,6.5-5.5s6.5,2.462,6.5,5.5-2.91,5.5-6.5,5.5a7.454,7.454,0,0,1-2.8-.542L-1245-1Z"
                          transform="translate(1246 13)" fill="#bcbccb">
                </svg>
            </a>
            <div class="dropdown-menu messages-menu {{app()->getLocale() =='ar' ? 'dropdown-menu-left' : 'dropdown-menu-right' }}"
                aria-labelledby="messagesDropdownMenu">
                <div class="menu-inner">
                    <h5>@lang('site.messages')</h5>
                    <ul>
                        @foreach (\App\Models\SupportCenter::latest()->get()->take(5) as $msg)
                        <li>
                            <a href="{{route('admin.messages.customer_messages')}}">
                                <img src="{{$msg->user->image != '' ? asset($$msg->user->image) : asset('uploads/users/default.png')}}" alt="">
                                <div class="flex-column">
                                    <span class="flex-align-center">
                                        <strong>{{$msg->user->name}}</strong>
                                        {{--&nbsp;sent you a message.--}}
                                    </span>
                                    {{--<span style="padding: 5px 14px;">{{$msg->title}}</span>--}}
                                    <small class="flex-space">
                                        <span>{{$msg->title}}</span>
                                        <span>{{$msg->created_at->diffForHumans()}}</span>
                                    </small>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{route('admin.messages.customer_messages')}}">@lang('site.view_all_messages')</a>
                </div>
            </div>
        </li>
        <li class="nav-item dropdown nav-notif">
            <a class="nav-link text-muted notification-toggle"  role="button" data-toggle="dropdown" href="#" id="notifDropdownMenu"
            aria-haspopup="true" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                    <path id="Notification_Icon" data-name="Notification Icon"
                          d="M-1371,81h4a2.006,2.006,0,0,1-2,2A2.006,2.006,0,0,1-1371,81Zm-5-1a.945.945,0,0,1-1-1,.945.945,0,0,1,1-1h.5a4.354,4.354,0,0,0,1.5-3V72a4.952,4.952,0,0,1,5-5,4.951,4.951,0,0,1,5,5v3a4.351,4.351,0,0,0,1.5,3h.5a.945.945,0,0,1,1,1,.945.945,0,0,1-1,1Z"
                          transform="translate(1377 -67)" fill="#bcbccb">
                </svg>
                @if (auth()->user()->unreadNotifications->count()>0)
                <span id="notificationCount" class="dot dot-md bg-warning">0</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-right notification-menu {{app()->getLocale() =='ar' ? 'dropdown-menu-left' : 'dropdown-menu-right' }}"
                 aria-labelledby="notifDropdownMenu">
                <div class="menu-inner">
                    <h5>{{auth()->user()->unreadNotifications->count()}}&nbsp;@lang('site.notifications')</h5>
                    <ul>
                        @foreach (auth()->user()->unreadNotifications()->take(5)->get() as $notification)
                        <li>

                            <a href="{{route('admin.showAndRead',['id'=>$notification->id])}}" class="flex-column">

                                <small class="flex-space">
                                    <span>
                                        @lang('site.'. $notification->data['title'] )
                                        &nbsp;@lang('site.'. $notification->data['target'] )
                                        &nbsp;{{$notification->data['target_id'] }}
                                    </span>
                                    <span>{{$notification->created_at->diffForHumans()}}</span>
                                </small>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    {{--<a href="{{route('admin.MarkAsRead_all')}}">@lang('site.read_all')</a>--}}
                    <a href="{{route('admin.allNotifications')}}">@lang('site.all_notifications')</a>
                </div>
            </div>
        </li>
        <li class="nav-item d-lg-flex">
            <a class="nav-link text-muted" href="{{route('admin.setting.general')}}" title="@lang('site.general_settings')">
                <svg xmlns="http://www.w3.org/2000/svg" width="14.664" height="16.98"
                     viewBox="0 0 14.664 16.98">
                    <path id="Path_209562" data-name="Path 209562"
                          d="M9.832,1l7.332,4.245v8.49L9.832,17.98,2.5,13.735V5.245Zm0,10.805A2.315,2.315,0,1,0,7.517,9.49,2.315,2.315,0,0,0,9.832,11.805Z"
                          transform="translate(-2.5 -1)" fill="#bcbccb">
                </svg>
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle user-menu text-muted pr-0" href="#" id="userMenuLink" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="username">{{ auth()->user()->name }}</span>
                <i class="far fa-angle-down"></i>
                <span class="avatar avatar-sm">
                    @if (auth()->user()->userData && auth()->user()->userData->image)
                        <img src="{{ asset(auth()->user()->userData->image) }}"
                             alt="{{ auth()->user()->name }}" class="avatar-img rounded-circle">
                    @else
                        <img src="{{ asset('uploads/users/default.png') }}"
                             alt="{{ auth()->user()->name }}" class="avatar-img rounded-circle">
                    @endif
                </span>
            </a>
            <div class="dropdown-menu {{app()->getLocale() =='ar' ? 'dropdown-menu-left' : 'dropdown-menu-right' }}"
                aria-labelledby="userMenuLink">
                <a class="dropdown-item" href="{{route('admin.account.show')}}">
                    <i class="fad fa-user-circle"></i>
                    @lang('site.account')
                </a>
                <a class="dropdown-item d-md-block" href="{{route('admin.messages.customer_messages')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15.999" height="15.999"
                         viewBox="0 0 15.999 15.999">
                        <path id="Chat_Icon" data-name="Chat Icon"
                              d="M-1234.693.89A7.944,7.944,0,0,1-1236,1,7.286,7.286,0,0,1-1239.8-.021c.1,0,.2.021.305.021,4.16,0,7.624-2.652,8.353-6.139A3.788,3.788,0,0,1-1230-3.5a3.739,3.739,0,0,1-1.012,2.5h.012V3ZM-1245-4.578a4.872,4.872,0,0,1-1-2.921c0-3.038,2.91-5.5,6.5-5.5s6.5,2.462,6.5,5.5-2.91,5.5-6.5,5.5a7.454,7.454,0,0,1-2.8-.542L-1245-1Z"
                              transform="translate(1246 13)" fill="#bcbccb">
                    </svg>
                    Messages
                </a>
                <a class="dropdown-item d-md-block" href="{{route('admin.setting.general')}}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14.664" height="16.98"
                         viewBox="0 0 14.664 16.98">
                        <path id="Path_209562" data-name="Path 209562"
                              d="M9.832,1l7.332,4.245v8.49L9.832,17.98,2.5,13.735V5.245Zm0,10.805A2.315,2.315,0,1,0,7.517,9.49,2.315,2.315,0,0,0,9.832,11.805Z"
                              transform="translate(-2.5 -1)" fill="#bcbccb">
                    </svg>
                    Settings
                </a>
                <a class="dropdown-item" href="{{route('admin.account.edit.password')}}">
                    <i class="fad fa-redo-alt"></i>
                    @lang('site.reset_password')
                </a>
                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                    onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                    title="@lang('site.logout')">
                    <i class="fad fa-sign-out-alt"></i>
                    @lang('site.logout')
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>
<audio id="notif-audio" src="/sounds/notify.mp3" preload="auto"></audio>
<script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.3/firebase-app.js";
  import { getMessaging, getToken, onMessage, isSupported } from "https://www.gstatic.com/firebasejs/10.12.3/firebase-messaging.js";

  const firebaseConfig = {
    apiKey: "AIzaSyDxTycXHWx6hMnpx90fSo2Y8SOFGXomA-w",
    authDomain: "nawloan-eff12.firebaseapp.com",
    databaseURL: "https://nawloan-eff12-default-rtdb.firebaseio.com",
    projectId: "nawloan-eff12",
    storageBucket: "nawloan-eff12.appspot.com",
    messagingSenderId: "997400731253",
    appId: "1:997400731253:web:d0ae522e19b8fce924a23c",
    measurementId: "G-8GEL2Y9LVZ"
  };
  const VAPID_KEY = "BKcLwEjrAedWHYKxK8yaxKIvOqGysObPboROGhiWEO8Kae1cBYooFWY7_Ghf_-wnO8tpmNkYc5_MaApffWQLmAw";

  const app = initializeApp(firebaseConfig);
  const audio = document.getElementById('notif-audio');

  // Prime audio once (autoplay)
  window.addEventListener('click', () => {
    if (!audio) return;
    audio.muted = false;
    audio.play().then(() => audio.pause()).catch(()=>{});
  }, { once: true });

  function toast(text) {
    const node = document.createElement('div');
    node.textContent = text;
    node.style.cssText = "position:fixed;right:16px;top:16px;z-index:9999;background:#111;color:#fff;padding:10px 14px;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.25);font-family:sans-serif";
    document.body.appendChild(node);
    setTimeout(() => node.remove(), 4000);
  }

  async function registerServiceWorker() {
    const supported = await isSupported().catch(() => false);
    console.log('[FCM] isSupported:', supported);
    if (!('serviceWorker' in navigator) || !supported) return null;
    const reg = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
    console.log('[FCM] SW registered at', reg.scope);
    return reg;
  }

  async function saveTokenToBackend(token) {
    console.log('[FCM] Saving token…', token.slice(0,16)+'…');
    const res = await fetch('/admin/fcm-token', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      credentials: 'same-origin',
      body: JSON.stringify({ token, platform: 'web' })
    });
    if (!res.ok) {
      const text = await res.text();
      console.error('[FCM] saveToken failed', res.status, text);
    } else {
      console.log('[FCM] Token saved OK');
    }
  }

  async function initFCM() {
    try {
      const reg = await registerServiceWorker();
      if (!reg) {
        console.warn('[FCM] Not supported or SW failed to register');
        return;
      }

      const messaging = getMessaging(app);

      const permission = await Notification.requestPermission();
      console.log('[FCM] Permission:', permission);
      if (permission !== 'granted') {
        toast('Please allow notifications in your browser');
        return;
      }

      let token;
      try {
        token = await getToken(messaging, { vapidKey: VAPID_KEY, serviceWorkerRegistration: reg });
        console.log('[FCM] getToken returned:', token ? token.slice(0,16)+'…' : token);
      } catch (e) {
        console.error('[FCM] getToken error:', e);
        toast('Failed to get push token. Check console.');
        return;
      }

      if (token) await saveTokenToBackend(token);
      else {
        console.warn('[FCM] No token (maybe permission blocked?)');
        toast('No push token. Allow notifications.');
      }

      // Foreground messages
      onMessage(messaging, (payload) => {
        console.log('[FCM] onMessage payload:', payload);
        const title = payload?.notification?.title || 'New notification';
        const body  = payload?.notification?.body  || '';
        toast(`${title}: ${body}`);
        audio?.play().catch(()=>{});
        const badge = document.getElementById('notificationCount');
        if (badge) badge.textContent = ((+badge.textContent || 0) + 1).toString();
      });

      // From SW (background → page)
      navigator.serviceWorker.addEventListener('message', (event) => {
        if (!event.data?.__fcm) return;
        const payload = event.data.payload;
        console.log('[FCM] SW->page payload:', payload);
        const title = payload?.notification?.title || 'New notification';
        const body  = payload?.notification?.body  || '';
        toast(`${title}: ${body}`);
        audio?.play().catch(()=>{});
        const badge = document.getElementById('notificationCount');
        if (badge) badge.textContent = ((+badge.textContent || 0) + 1).toString();
      });
    } catch (e) {
      console.error('[FCM] initFCM fatal:', e);
      toast('FCM init failed. Check console.');
    }
  }

  initFCM();
</script>

