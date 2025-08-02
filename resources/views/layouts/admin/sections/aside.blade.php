<aside class="sidebar-left bg-white shadow" id="leftSidebar" data-simplebar>
    <nav class="vertnav navbar navbar-light">
        <!-- nav bar -->
        <div class="sidebar-logo w-100 d-flex">
            <a class="navbar-brand flex-fill text-center" href="{{route('admin.index')}}">
                @if(!empty(setting('logo')))
                <img src="{{asset(setting('logo'))}}" alt="">
                @endif
            </a>
            <a href="#" class="btn collapseSidebar toggle-btn d-lg-none shadow-none ml-2 mt-3" data-toggle="toggle">
                <i class="fal fa-times"></i>
            </a>
        </div>
        <div class="sidebar-inner border-right">
            <p class="text-muted nav-heading">
                <span>@lang('site.analytics')</span>
            </p>
            <ul class="navbar-nav flex-fill w-100">
                <li class="nav-item w-100">
                    <a class="nav-link {{request()->routeIs('admin.index') ? 'active' : '' }}"
                       href="{{route('admin.index')}}">
                        <i class="fad fa-home-lg-alt"></i>
                        <span class="item-text">@lang('site.dashboard')</span>
                    </a>
                </li>
            </ul>
            @if (auth()->user()->hasPermission('orders_read') || auth()->user()->hasPermission('offers_read'))
            <p class="text-muted nav-heading">
                <span>@lang('site.orders_offers')</span>
            </p>
            <ul class="navbar-nav flex-fill w-100">
                @if (auth()->user()->hasPermission('orders_read') || auth()->user()->hasRole('superadministrator'))
                <li class="nav-item w-100">
                    <a class="nav-link {{request()->routeIs('admin.order*') && !request()->routeIs('admin.orders.pend') && !request()->routeIs('admin.orders.progress') && !request()->routeIs('admin.orders.complete') && !request()->routeIs('admin.orders.cancel') ? 'active' : '' }}"
                       href="{{route('admin.orders.index')}}">
                        <i class="fad fa-shopping-cart"></i>
                        <span class="item-text">@lang('site.all_orders')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\Order::count()}}</span>--}}
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasPermission('offers_read') || auth()->user()->hasRole('superadministrator'))
                <li class="nav-item w-100">
                    <a class="nav-link {{request()->routeIs('admin.offers*') ? 'active' : '' }}"
                       href="{{route('admin.offers.index')}}">
                        <i class="fad fa-bags-shopping"></i>
                        <span class="item-text">@lang('site.all_offers')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\Offer::where('status','pending')->count()}}</span>--}}
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasPermission('orders_read') || auth()->user()->hasRole('superadministrator'))
                <li class="nav-item w-100">
                    <a class="nav-link hover-warning text-warning {{ request()->routeIs('admin.orders.pend') ? 'active' : '' }}"
                       href="{{route('admin.orders.pend')}}">
                        <i class="fad fa-shopping-cart text-warning"></i>
                        <span class="item-text">@lang('site.pend_orders')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\Order::where('status','pending')->count()}}</span>--}}
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link hover-blue text-blue {{request()->routeIs('admin.orders.progress') ? 'active' : '' }}"
                       href="{{route('admin.orders.progress')}}">
                        <i class="fad fa-shopping-cart text-blue"></i>
                        <span class="item-text">@lang('site.aproved_orders')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\Order::where('status','approve')->orWhere('status','pick_up')->orWhere('status','delivered')->count()}}</span>--}}
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link hover-success text-success {{request()->routeIs('admin.orders.complete') ? 'active' : '' }}"
                       href="{{route('admin.orders.complete')}}">
                        <i class="fad fa-shopping-cart text-success"></i>
                        <span class="item-text">@lang('site.complete_orders')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\Order::where('status','complete')->count()}}</span>--}}
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link hover-red text-red {{request()->routeIs('admin.orders.cancel') ? 'active' : '' }}"
                       href="{{route('admin.orders.cancel')}}">
                        <i class="fad fa-shopping-cart text-red"></i>
                        <span class="item-text">@lang('site.cancel_orders')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\Order::where('status','cancel')->count()}}</span>--}}
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasPermission('transactions_read') || auth()->user()->hasRole('superadministrator'))
                <li class="nav-item w-100">
                    <a class="nav-link {{request()->routeIs('admin.chat*') ? 'active' : '' }}"
                       href="{{route('admin.chat.index')}}">
                        <i class="fas fa-envelope-open-text"></i>
                        <span class="item-text">@lang('site.chats')</span>
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasPermission('transactions_read') || auth()->user()->hasRole('superadministrator'))
                <li class="nav-item w-100">
                    <a class="nav-link {{request()->routeIs('admin.transactions*') ? 'active' : '' }}"
                       href="{{route('admin.transactions.index')}}">
                        <i class="fad fa-dollar-sign"></i>
                        <span class="item-text">@lang('site.transactions')</span>
                    </a>
                </li>
                @endif
            </ul>
            @endif
            @if (auth()->user()->hasPermission('customers_read') || auth()->user()->hasPermission('drivers_read') || auth()->user()->hasRole('superadministrator'))
            <p class="text-muted nav-heading">
                <span>@lang('site.users')</span>
            </p>
            <ul class="navbar-nav flex-fill w-100">
                @if (auth()->user()->hasPermission('customers_read') || auth()->user()->hasRole('superadministrator'))
                <li class="nav-item w-100">
                    <a class="nav-link {{request()->routeIs('admin.customers*') ? 'active' : '' }}"
                       href="{{route('admin.customers.index')}}">
                        <i class="fad fa-users"></i>
                        <span class="item-text">@lang('site.customers_list')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\User::where('type','user')->count()}}</span>--}}
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasPermission('drivers_read') || auth()->user()->hasRole('superadministrator'))
                <li class="nav-item w-100">
                    <a class="nav-link {{request()->routeIs('admin.drivers*') ? 'active' : '' }}"
                       href="{{route('admin.drivers.index')}}">
                        <i class="fad fa-person-dolly"></i>
                        <span class="item-text">@lang('site.drivers_list')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\User::where('type','driver')->count()}}</span>--}}
                    </a>
                </li>
                @endif
            </ul>
            @endif
            @if (auth()->user()->hasPermission('factories_read') || auth()->user()->hasRole('driverCompanies_read') || auth()->user()->hasRole('superadministrator'))
                <p class="text-muted nav-heading">
                    <span>@lang('site.companies')</span>
                </p>
                <ul class="navbar-nav flex-fill w-100">
                    @if (auth()->user()->hasPermission('driverCompanies_read') || auth()->user()->hasRole('superadministrator'))
                        <li class="nav-item w-100">
                            <a class="nav-link {{ request()->routeIs('admin.companies*') ? 'active' : '' }}"
                               href="{{route('admin.companies.index')}}">
                                <i class="fad fa-truck-loading"></i>
                                <span class="item-text">@lang('site.driversCompanies')</span>
                                {{--<span class="badge badge-pill badge-primary">{{\App\Models\User::where('type','driverCompany')->count()}}</span>--}}
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasPermission('factories_read') || auth()->user()->hasRole('superadministrator'))
                        <li class="nav-item w-100">
                            <a class="nav-link {{ request()->routeIs('admin.factories*') ? 'active' : '' }}"
                               href="{{route('admin.factories.index')}}">
                                <i class="fad fa-truck-moving"></i>
                                <span class="item-text">@lang('site.factories')</span>
                                {{--<span class="badge badge-pill badge-primary">{{\App\Models\User::where('type','factory')->count()}}</span>--}}
                            </a>
                        </li>
                    @endif
                </ul>
            @endif
            @if (auth()->user()->hasPermission('cars_read') || auth()->user()->hasPermission('shipments_types_read') || auth()->user()->hasRole('superadministrator'))
            <p class="text-muted nav-heading">
                <span>@lang('site.system_management')</span>
            </p>
            <ul class="navbar-nav flex-fill w-100">
                @if (auth()->user()->hasPermission('cars_read') || auth()->user()->hasRole('superadministrator'))
                <li class="nav-item w-100">
                    <a class="nav-link {{ request()->routeIs('admin.trucks*') ? 'active' : '' }}"
                       href="{{route('admin.trucks.index')}}">
                        <i class="fad fa-truck-container"></i>
                        <span class="item-text">@lang('site.cars')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\Car::count()}}</span>--}}
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasPermission('shipments_types_read') || auth()->user()->hasRole('superadministrator'))
                <li class="nav-item w-100">
                    <a class="nav-link {{ request()->routeIs('admin.shipment*') ? 'active' : '' }}"
                       href="{{route('admin.shipment.index')}}">
                        <i class="fad fa-box"></i>
                        <span class="item-text">@lang('site.shipments_types')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\ShipmentType::count()}}</span>--}}
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasPermission('articles_read') || auth()->user()->hasRole('superadministrator')) {{--add by mohammed--}}
                <li class="nav-item w-100">
                    <a class="nav-link {{ request()->routeIs('admin.career/index') ? 'active' : '' }}"
                        href="{{route('admin.articles.index')}}">
                        <i class="fad fa-newspaper"></i>
                        <span class="item-text">@lang('site.articles')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\Car::count()}}</span>--}}
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasPermission('careers_read') || auth()->user()->hasRole('superadministrator'))
                    <li class="nav-item w-100">
                        <a class="nav-link {{ request()->routeIs('admin.career/index') ? 'active' : '' }}"
                           href="{{route('admin.careers.index')}}">
                            <i class="fad fa-briefcase"></i>
                            <span class="item-text">@lang('site.careers')</span>
                            {{--<span class="badge badge-pill badge-primary">{{\App\Models\Car::count()}}</span>--}}
                        </a>
                    </li>
                @endif
                @if (auth()->user()->hasPermission('coupons_read') || auth()->user()->hasRole('superadministrator')) {{--add by mohammed--}}
                        <li class="nav-item w-100">
                            <a class="nav-link {{ request()->routeIs('admin.coupon/index') ? 'active' : '' }}"
                               href="{{route('admin.coupons.index')}}">
                                <i class="fad fa-ticket-alt"></i>
                                <span class="item-text">@lang('site.coupons')</span>
                                {{--<span class="badge badge-pill badge-primary">{{\App\Models\Car::count()}}</span>--}}
                            </a>
                        </li>
                @endif
            </ul>
            @endif
            @if (auth()->user()->hasPermission('users_read') || auth()->user()->hasRole('superadministrator'))
            <p class="text-muted nav-heading">
                <span>@lang('site.site_settings')</span>
            </p>
            <ul class="navbar-nav flex-fill w-100">
                @if (auth()->user()->hasPermission('users_read') ||auth()->user()->hasRole('superadministrator'))
                <li class="nav-item w-100">
                    <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                       href="{{route('admin.users.index')}}">
                        <i class="fad fa-users-cog"></i>
                        <span class="item-text">@lang('site.users')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\User::where('type','admin')->orWhere('type','emp')->count()}}</span>--}}
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasPermission('send_messages_read') || auth()->user()->hasPermission('support_center_read') || auth()->user()->hasRole('superadministrator'))
                <li class="nav-item w-100">
                    <a class="nav-link {{ request()->routeIs('admin.messages*') ? 'active' : '' }}"
                       href="{{route('admin.messages.customer_messages')}}">
                        <i class="fad fa-sms"></i>
                        <span class="item-text">@lang('site.messages')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\User::where('type','admin')->orWhere('type','emp')->count()}}</span>--}}
                    </a>
                </li>
                @endif
                @if(auth()->user()->hasRole('superadministrator'))
                <li class="nav-item w-100">
                    <a class="nav-link {{ request()->routeIs('admin.setting.general') ? 'active' : '' }}"
                       href="{{route('admin.setting.general')}}">
                        <i class="fad fa-cogs"></i>
                        <span class="item-text">@lang('site.general_settings')</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link {{ request()->routeIs('admin.setting.seo') ? 'active' : '' }}"
                       href="{{route('admin.setting.seo')}}">
                        <i class="fad fa-comments-dollar"></i>
                        <span class="item-text">@lang('site.seo_settings')</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link {{ request()->routeIs('admin.setting.social') ? 'active' : '' }}"
                       href="{{route('admin.setting.social')}}">
                        <i class="fad fa-tags"></i>
                        <span class="item-text">@lang('site.social_settings')</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link {{ request()->routeIs('admin.setting.costs') ? 'active' : '' }}"
                       href="{{route('admin.setting.costs')}}">
                        <i class="fad fa-money-check"></i>
                        <span class="item-text">@lang('site.costs_settings')</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link {{ request()->routeIs('admin.gateway*') ? 'active' : '' }}"
                       href="{{route('admin.gateway.index')}}">
                        <i class="fad fa-share-alt"></i>
                        <span class="item-text">@lang('site.external_gateway')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\PaymentMethod::count()}}</span></a>--}}
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link {{ request()->routeIs('admin.countries*') ? 'active' : '' }}"
                       href="{{route('admin.countries.index')}}">
                        <i class="fad fa-mobile-alt"></i>
                        <span class="item-text">@lang('site.countries_codes')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\Country::count()}}</span>--}}
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasPermission('ulists_read') ||auth()->user()->hasRole('superadministrator'))
                <li class="nav-item w-100">
                    <a class="nav-link {{ request()->routeIs('admin.ulists*') ? 'active' : '' }}"
                       href="{{route('admin.ulists.index')}}">
                        <i class="fad fa-list-ul"></i>
                        <span class="item-text">@lang('site.ulists')</span>
                        {{--<span class="badge badge-pill badge-primary">{{\App\Models\UList::count()}}</span>--}}
                    </a>
                </li>
                @endif
            </ul>
            @endif
        </div>
    </nav>
</aside>
