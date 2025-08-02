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
