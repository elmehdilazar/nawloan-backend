@extends('layouts.admin.app')
@section('title', ' | ' . __('site.orders'))
@section('styles')
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{ asset('assets/tiny/css/dataTables.bootstrap4.css') }}">
@endsection
@section('content')
    <div class="flex-space mb-4 dash-head">
        <h2 class="section-title mb-0">@lang('site.all_orders')</h2>
        <div class="head-btns mb-0">
            <span id="checks-count" class="onchange-visible"></span>
            @if (auth()->user()->hasPermission('orders_export'))
                <a href="{{ route('admin.orders.export') }}" class="btn btn-transparent navy">@lang('site.export')</a>
            @endif
            @if (auth()->user()->hasPermission('orders_delete'))
                <a href="#" class="btn btn-danger onchange-visible">@lang('site.delete')</a>
            @endif
            @if (auth()->user()->hasPermission('orders_create'))
                <a href="{{ route('admin.orders.create') }}" class="btn btn-navy onchange-hidden">@lang('site.add_new_order')</a>
            @endif
        </div>
    </div>
    <form action="{{ route('admin.orders.index') }}" method="GET">
        <div class="row search-by-group mb-5">
            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
                <div class="input-group mb-0">
                    <label for="" class="fe-14">@lang('site.number')</label>
                    <div class="input-group">
                        <input type="text" class="fe-14" name="number" id="number" value="{{ request()->number }}"
                            placeholder="@lang('site.number')">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
                <div class="select-group mb-0">
                    <label for="simple-select2" class="fe-14">@lang('site.customer')</label>
                    <select class="form-control select2" id="user_id" name="user_id">
                        <option value="0" selected>@lang('site.view_all')</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request()->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name . ' - ' }} @lang('site.the_' . $user->type . '')
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
                <div class="select-group mb-0">
                    <label for="simple-select2" class="fe-14">@lang('site.car')</label>
                    <select class="form-control select2" id="car_id" name="car_id">
                        <option value="0" selected>@lang('site.view_all')</option>
                        @foreach ($cars as $car)
                            <option value="{{ $car->id }}" {{ request()->car_id == $car->id ? 'selected' : '' }}>
                                {{ app()->getLocale() == 'ar' ? $car->name_ar : $car->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
                <div class="select-group mb-0">
                    <label for="simple-select2" class="fe-14">@lang('site.shipment_type')</label>
                    <select class="form-control select2" id="shipment_type_id" name="shipment_type_id">
                        <option value="0" selected>@lang('site.view_all')</option>
                        @foreach ($shipments as $shipment)
                            <option value="{{ $shipment->id }}"
                                {{ request()->shipment_type_id == $shipment->id ? 'selected' : '' }}>
                                {{ app()->getLocale() == 'ar' ? $shipment->name_ar : $shipment->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
                <div class="input-group mb-0">
                    <label for="" class="fe-14">@lang('site.ton_price')</label>
                    <div class="input-group">
                        <input type="text" class="fe-14 input-money" name="ton_price" id="ton_price"
                            value="{{ request()->ton_price }}" placeholder="0.00">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                @if (setting('currency_atr') != '')
                                    {{ setting('currency_atr') }}
                                @else
                                    <i class="fad fa-dollar-sign"></i>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
                <div class="input-group mb-0">
                    <label for="" class="fe-14">@lang('site.weight_ton')</label>
                    <div class="input-group">
                        <input type="text" class="fe-14" name="weight_ton" id="weight_ton"
                            value="{{ request()->weight_ton }}" placeholder="0.00">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fad fa-weight-hanging"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
                <div class="select-group mb-0">
                    <label for="simple-select2" class="fe-14">@lang('site.size')</label>
                    <select class="form-control select2 no-search" id="size" name="size">
                        <option value="" selected>@lang('site.view_all')</option>
                        <option value="small" {{ request()->size == 'small' ? 'selected' : '' }}>
                            @lang('site.small')
                        </option>
                        <option value="medium" {{ request()->size == 'medium' ? 'selected' : '' }}>
                            @lang('site.medium')
                        </option>
                        <option value="large" {{ request()->size == 'large' ? 'selected' : '' }}>
                            @lang('site.large')
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
                <div class="select-group mb-0">
                    <label for="simple-select2" class="fe-14">@lang('site.status')</label>
                    <select class="form-control select2 no-search" id="status" name="status">
                        <option value="0" selected>@lang('site.view_all')</option>
                        <option value="pend"
                            {{ request()->status == 'pend' or (request()->status == 'pending' ? 'selected' : '') }}>
                            @lang('site.pend')
                        </option>
                        <option value="approve" {{ request()->status == 'approve' ? 'selected' : '' }}>
                            @lang('site.approval')
                        </option>
                        <option value="pick_up" {{ request()->status == 'pick_up' ? 'selected' : '' }}>
                            @lang('site.Pick Up')
                        </option>
                        <option value="delivered" {{ request()->status == 'delivered' ? 'selected' : '' }}>
                            @lang('site.Delivered')
                        </option>
                        <option value="complete" {{ request()->status == 'complete' ? 'selected' : '' }}>
                            @lang('site.completed')
                        </option>
                        <option value="cancel" {{ request()->status == 'cancel' ? 'selected' : '' }}>
                            @lang('site.canceled')
                        </option>
                    </select>
                </div>
            </div>
            <div class="flex-center mt-3">
                <button type="submit" class="btn btn-navy">@lang('site.search')</button>
            </div>
        </div>
    </form>
    <table class="table datatables datatables-active" id="">
        <thead>
            <tr>
                <th>
                    <div class="dt-checkbox">
                        <input type="checkbox" name="select_all" value="1" id="selectAll">
                        <label for="selectAll" class="visual-checkbox"></label>
                    </div>
                </th>
                <th>@lang('site.num')</th>
                <th class="min-width-170">@lang('site.service_seeker')</th>
                <th>@lang('site.car')</th>
                <th>@lang('site.offers')</th>
                <th>@lang('site.total_price')</th>
                <th>@lang('site.transaction_status')</th>
                <th>@lang('site.status')</th>
                <th>@lang('site.edit')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $index => $order)
                <tr>
                    <td></td>
                    <td>{{ $order->id }}</td>
                    <td>
                        <div class="user-col">
                            <img src="{{ asset($order->user?->userData?->image ?: 'uploads/users/default.png') }}"
                                alt="{{ $order->user?->name }}">
                            <span class="name">{{ $order->user?->name }}</span>
                        </div>
                    </td>
                    <td>
                        <a href="#" data-toggle="modal" data-target="#truckModal_{{ $index }}">
                            {{ app()->getLocale() == 'ar' ? $order->car?->name_ar : $order->car?->name_en }}
                        </a>
                    </td>
                    <td>
                        @if (!empty($order->offers))
                            <a href="#" class="act-btn" data-toggle="modal"
                                data-target="#offersModal_{{ $index }}">
                                <i class="fad fa-paper-plane"></i>
                            </a>
                        @endif
                    </td>
                    <td>{{ number_format($order->ton_price * $order->weight_ton, 2, '.', '') }}{{ ' ' . setting('currency_atr') }}
                    </td>
                    @php
                        $transactionStatus = $order->transaction?->status;
                        $transactionStatusKey = $transactionStatus ? strtolower(trim($transactionStatus)) : null;
                        $transactionStatusTranslationKey = $transactionStatusKey ? 'site.' . $transactionStatusKey : null;
                        $transactionStatusLabel = $transactionStatusTranslationKey ? __($transactionStatusTranslationKey) : '';
                        if (!$transactionStatusTranslationKey || $transactionStatusLabel === $transactionStatusTranslationKey) {
                            $transactionStatusLabel = $transactionStatus ?: __('site.Delayed Payment');
                        }
                    @endphp
                    <td>{{ $transactionStatusLabel }}</td>
                    <td>
                        <span
                            class="badge badge-pill
                            @if ($order->status == 'pend' || $order->status == 'pending') badge-warning
                            @elseif($order->status == 'approve' || $order->status == 'pick_up' || $order->status == 'delivered') badge-primary
                            @elseif ($order->status == 'complete' || $order->status == 'completed') badge-success
                            @elseif ($order->status == 'cancel' || $order->status == 'cancelled') badge-danger @endif">
                            @if ($order->status == 'pend' || $order->status == 'pending')
                                @lang('site.pend')
                            @elseif($order->status == 'approve')
                                @lang('site.approval')
                            @elseif($order->status == 'pick_up')
                                @lang('site.Pick Up')
                            @elseif($order->status == 'delivered')
                                @lang('site.Delivered')
                            @elseif($order->status == 'complete' || $order->status == 'completed')
                                @lang('site.completed')
                            @elseif($order->status == 'cancel' || $order->status == 'cancelled')
                                @lang('site.canceled')
                            @endif
                        </span>
                    </td>
                    <td>
                        <ul class="actions">
                            @if (auth()->user()->hasPermission('orders_read'))
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#orderModal_{{ $index }}"
                                        title="@lang('site.show')" class="show">
                                        <i class="fad fa-eye"></i>
                                    </a>
                                    {{-- <a href="{{route('admin.orders.show',$order->id)}}" --}}
                                    {{-- title="@lang('site.show')" class="show"> --}}
                                    {{-- <i class="fad fa-eye"></i> --}}
                                    {{-- </a> --}}
                                </li>
                            @endif
                            @if (auth()->user()->hasPermission('orders_update'))
                                <li><a href="{{ route('admin.orders.edit', $order->id) }}" title="@lang('site.edit')"
                                        class="show"><i class="fad fa-edit"></i></a></li>
                                @if ($order->status == 'cancel')
                                    <li>
                                        <a href="#" class="check"
                                            onclick="event.preventDefault();document.getElementById('pend-form_{{ $index }}').submit();">
                                            <i class="fad fa-check-double"></i>
                                        </a>
                                    </li>
                                    <form action="{{ route('admin.orders.changeStatus', $order->id) }}" method="POST"
                                        id="pend-form_{{ $index }}">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" id="status" name="status" value="pend">
                                    </form>
                                @endif
                                @if ($order->status != 'complete' && $order->status != 'cancel' && $order->status != 'pend')
                                    <li>
                                        <a href="#" class="success"
                                            onclick="event.preventDefault();document.getElementById('complete-form_{{ $index }}').submit();">
                                            <i class="fad fa-clipboard-check"></i>
                                        </a>
                                    </li>
                                    <form action="{{ route('admin.orders.changeStatus', $order->id) }}" method="POST"
                                        id="complete-form_{{ $index }}">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" id="status" name="status" value="complete">
                                    </form>
                                @endif
                                @if ($order->status != 'complete' && $order->status != 'cancel')
                                    <li>
                                        <a href="#" class="cancel"
                                            onclick="event.preventDefault();document.getElementById('cancel-form_{{ $index }}').submit();">
                                            <i class="fad fa-times"></i>
                                        </a>
                                    </li>
                                    <form action="{{ route('admin.orders.changeStatus', $order->id) }}" method="POST"
                                        id="cancel-form_{{ $index }}">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" id="status" name="status" value="cancel">
                                    </form>
                                @endif
                            @endif
                            @if (auth()->user()->hasPermission('orders_read'))
                                <li>
                                    <a href="#" class="show" title="@lang('site.order_track')"
                                        onclick="showModal({{ $order->pick_up_late }},{{ $order->pick_up_long }},{{ $order->drop_of_late }},{{ $order->drop_of_long }},{{ $order->serviceProvider?->userData?->latitude ?? '0' }},{{ $order->serviceProvider?->userData?->longitude ?? '0' }},{{ $order }});">
                                        <i class="fad fa-map-marked-alt"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="flex-end mt-4">
        {{ $orders->appends(request()->query())->links() }}
    </div>
    @foreach ($orders as $index => $order)
        <!-- Start driverTruck Modal -->
        <div class="modal fade" id="truckModal_{{ $index }}" tabindex="-1" role="dialog"
            aria-labelledby="verticalModalTitle_{{ $index }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
                <div class="modal-content fog-background">
                    <div class="bring-to-front">
                        <div class="modal-header flex-center">
                            <h4 class="modal-title text-navy mb-0" id="verticalModalTitle_{{ $index }}"></h4>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38"
                                    viewBox="0 0 38 38">
                                    <path id="Exclusion_23" data-name="Exclusion 23"
                                        d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                        transform="translate(-0.384 0.422)" fill="#d27979" />
                                </svg>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="max-width-70">
                                <div class="truck-box driver">
                                    <img
                                        src="{{ $order->car && $order->car->image != '' ? asset($order->car->image) : asset('uploads/cars/default.png') }}">
                                    <span class="name">
                                        @if (app()->getLocale() == 'ar')
                                            {{ $order->car?->name_ar }}
                                        @else
                                            {{ $order->car?->name_en }}
                                        @endif
                                    </span>
                                </div>
                                <ul class="truck-info mt-4">
                                    <li>
                                        <span>@lang('site.track_number')</span>
                                        <span>{{ $order->track_number }}</span>
                                    </li>
                                    <li>
                                        <span>@lang('site.track_license_number')</span>
                                        <span>{{ $order->track_license_number }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End selectTruck Modal -->
        <!-- Start DriversOffers Modal -->
        <div class="modal fade" id="offersModal_{{ $index }}" tabindex="-1" role="dialog"
            aria-labelledby="verticalModalTitle_{{ $index }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
                <div class="modal-content fog-background">
                    <div class="bring-to-front">
                        <div class="modal-header flex-center">
                            <h4 class="modal-title text-navy mb-0" id="verticalModalTitle_{{ $index }}">
                                @lang('site.offers')</h4>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38"
                                    viewBox="0 0 38 38">
                                    <path id="Exclusion_23" data-name="Exclusion 23"
                                        d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                        transform="translate(-0.384 0.422)" fill="#d27979" />
                                </svg>
                            </button>
                        </div>
                        <div class="modal-body">
                            @if ($order->offers->count())
                                <ul class="offers-list max-width-80">
                                    @foreach ($order->offers as $offer)
                                        <li class="offer-item">
                                            <div class="flex-align-center">
                                                @php
                                                    $offerDriver = $offer->driver ?? $offer->user;
                                                    $offerRateRoute = null;
                                                    if (Route::has('admin.drivers.evaluate') && $offerDriver?->type == 'driver') {
                                                        $offerRateRoute = route('admin.drivers.evaluate', $offerDriver->id);
                                                    } elseif (Route::has('admin.companies.evaluate') && $offerDriver?->type == 'driversCompany') {
                                                        $offerRateRoute = route('admin.companies.evaluate', $offerDriver->id);
                                                    } elseif (Route::has('admin.factories.evaluate') && $offerDriver?->type == 'factory') {
                                                        $offerRateRoute = route('admin.factories.evaluate', $offerDriver->id);
                                                    }
                                                @endphp
                                                <a href="{{ $offerRateRoute ?? '#' }}" class="flex-col-center">
                                                    <img src="{{ asset($offer->user?->userData?->image ?: 'uploads/users/default.png') }}"
                                                        class="avatar">

                                                    <span class="rate flex-col-center">
                                                        <span class="total">
                                                            @php
                                                                $driver = $offer->driver ?? $offer->user;
                                                                $driverData = $driver?->userData;
                                                                $car = $driverData?->car;
                                                                $avgValue =
                                                                    (float) ($driver?->evaluates?->avg('rate') ?? 0);
                                                                $avgRate = (int) floor($avgValue);
                                                                $emptyStars = 5 - $avgRate;
                                                                if ($emptyStars < 0) {
                                                                    $emptyStars = 0;
                                                                }
                                                                $ratingsCount =
                                                                    (int) ($driver?->evaluates?->count() ?? 0);
                                                            @endphp
                                                            @for ($i = 0; $i < $avgRate; $i++)
                                                                <img src="{{ asset('assets/images/svgs/star-fill.svg') }}"
                                                                    alt="">
                                                            @endfor
                                                            @for ($i = 0; $i < $emptyStars; $i++)
                                                                <img src="{{ asset('assets/images/svgs/star.svg') }}"
                                                                    alt="">
                                                            @endfor
                                                        </span>
                                                        <span class="brief">
                                                            {{ $avgValue }}
                                                            <span>({{ $ratingsCount }})</span>
                                                        </span>
                                                    </span>
                                                </a>
                                                <div class="flex-space">
                                                    <div class="flex-column">
                                                        <span class="name">{{ $offer->user?->name }}</span>
                                                        <ul>
                                                            <li>
                                                                <img src="{{ asset('assets/images/svgs/map-marker.svg') }}"
                                                                    alt="">
                                                                <span
                                                                    class="val">{{ $driverData?->location ?? '-' }}</span>
                                                            </li>
                                                            <li>
                                                                <img src="{{ asset('assets/images/svgs/truck-fill.svg') }}"
                                                                    alt="">
                                                                @if ($car)
                                                                    @if (app()->getLocale() == 'ar')
                                                                        {{ $car->name_ar }}
                                                                    @else
                                                                        {{ $car->name_en }}
                                                                    @endif
                                                                @else
                                                                    -
                                                                @endif
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <h4 class="offer-cost special mb-0">
                                                        {{ $offer->price }}
                                                        {{ setting('currency_atr') != '' ? ' ' . setting('currency_atr') : '' }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="no-offer flex-col-center max-width-80">
                                    <img src="{{ asset('assets/images/no-data.png') }}" alt="">
                                    <h4>@lang('site.no_offers_found_for_order_yet')</h4>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End DriversOffers Modal -->
        <div class="modal fade order-modal" id="orderModal_{{ $index }}" tabindex="-1" role="dialog"
            aria-labelledby="verticalModalTitle_{{ $index }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content yellow-background border-0">
                    <div class="modal-header border-bottom-0 flex-space">
                        <h4 class="modal-title mb-0" id="verticalModalTitle_{{ $index }}">@lang('site.order')
                            {{ ' # ' . $order->id }}</h4>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                <path id="Exclusion_23" data-name="Exclusion 23"
                                    d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                    transform="translate(-0.384 0.422)" fill="#d27979" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row col-gap-70 mb-4">
                            @if ($order->serviceProvider)
                                <div class="col-lg-6 col-12 pt-lg-3 pt-0 mb-lg-0 mb-4">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-12 col-sm-6 col-12 mb-lg-3 mb-md-0 mb-3">
                                            <div class="participant">
                                                <h5 class="title mb-4">@lang('site.customer')/@lang('site.' . $order->user->type . '')</h5>
                                                <div class="flex-align-center">
                                                    <img src="{{ asset($order->user?->userData?->image ?: 'uploads/users/default.png') }}"
                                                        alt="{{ $order->user?->name }}">
                                                    <div class="flex-column">
                                                        <span class="name">{{ $order->user?->name }}</span>
                                                        <span>@lang('site.id') : {{ $order->user?->id }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-12 col-sm-6 col-12">
                                            <div class="participant">
                                                <h5 class="title mb-4">
                                                    @lang('site.driver')/
                                                    @if ($order->offer)
                                                        @lang('site.' . $order->offer->user->type . '')
                                                    @else
                                                        @lang('site.' . $order->serviceProvider->type . '')
                                                    @endif
                                                </h5>
                                                <div class="flex-align-center">
                                                    <img src="{{ $order->serviceProvider &&
                                                    $order->serviceProvider->userData &&
                                                    $order->serviceProvider->userData->image != ''
                                                        ? asset($order->serviceProvider->userData->image)
                                                        : asset('uploads/users/default.png') }}"
                                                        alt="{{ $order->serviceProvider->name }}">

                                                    <div class="flex-column">
                                                        <span class="name">{{ $order->serviceProvider->name }}</span>
                                                        <span>@lang('site.id') : {{ $order->serviceProvider->id }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="row">
                                        <div class="col-md-6 col-12 mb-md-0 mb-3">
                                            <ul class="order-details">
                                                <li>
                                                    <img src="{{ asset('assets/images/svgs/map-marker-solid.svg') }}"
                                                        alt="">
                                                    @lang('site.sharjah')
                                                </li>
                                                <li>
                                                    <img src="{{ asset('assets/images/svgs/dumbbells.svg') }}"
                                                        alt="">
                                                    {{ $order->car?->frames }} @lang('site.axes')
                                                </li>
                                                <li>
                                                    <img src="{{ asset('assets/images/svgs/weight-solid.svg') }}"
                                                        alt="">
                                                    {{ $order->car?->weight }} @lang('site.weight')
                                                </li>
                                                <li>
                                                    {{ app()->getLocale() == 'ar' ? $order->car?->name_ar : $order->car?->name_en }}
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="flex-center h-100">
                                                <img src="{{ $order->car && $order->car->image != '' ? asset($order->car->image) : asset('uploads/cars/default.png') }}"
                                                    alt="@lang('site.order')" class="truck-preview">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (!$order->serviceProvider)
                                <div class="col-lg-6 col-12">
                                    <div class="row">
                                        <div class="col-md-6 col-12 mb-md-0 mb-3">
                                            <ul class="order-details">
                                                <li>
                                                    <img src="{{ asset('assets/images/svgs/map-marker-solid.svg') }}"
                                                        alt="">
                                                    @lang('site.sharjah')
                                                </li>
                                                <li>
                                                    <img src="{{ asset('assets/images/svgs/dumbbells.svg') }}"
                                                        alt="">
                                                    {{ $order->car?->frames }} @lang('site.axes')
                                                </li>
                                                <li>
                                                    <img src="{{ asset('assets/images/svgs/weight-solid.svg') }}"
                                                        alt="">
                                                    {{ $order->car?->weight }} @lang('site.weight')
                                                </li>
                                                <li>
                                                    {{ app()->getLocale() == 'ar' ? $order->car?->name_ar : $order->car?->name_en }}
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="flex-center h-100">
                                                <img src="{{ $order->car && $order->car->image != '' ? asset($order->car->image) : asset('uploads/cars/default.png') }}"
                                                    alt="" class="truck-preview">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row col-gap-70 mb-4">
                            <div class="col-lg-6 col-12 mb-lg-0 mb-4">
                                <div class="order-path mb-4">
                                    <img src="{{ asset('assets/images/order-path-1.png') }}" alt="">
                                    <span class="flex-column">
                                        <span class="from gray">
                                            <span>{{ $order->pick_up_address }}</span>
                                            <img src="{{ asset('assets/images/svgs/check-circle-gray.svg') }}"
                                                alt="">
                                        </span>
                                        <span class="to navy">
                                            <span>{{ $order->drop_of_address }}</span>
                                            <img src="{{ asset('assets/images/svgs/check-circle-navy.svg') }}"
                                                alt="">
                                        </span>
                                    </span>
                                </div>
                                <div class="table-responsive shipment-datetime">
                                    <table class="">
                                        <thead>
                                            <tr>
                                                <th>@lang('site.date')</th>
                                                <th>@lang('site.time')</th>
                                                <th>@lang('site.expected_ton_price')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ date('Y-m-d', strtotime($order->shipping_date)) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($order->shipping_date)->locale(app()->getLocale())->translatedFormat('h:i A') }}</td>
                                                <td>{{ $order->ton_price }}
                                                    {{ setting('currency_atr') != '' ? '' . setting('currency_atr') : '' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="order-history">
                                    <h4>@lang('site.history')</h4>
                                    @php
                                        $statusTimes = [];
                                        foreach ($order->statuses?->sortBy('created_at') ?? [] as $stat) {
                                            $key = strtolower($stat->status);
                                            if (!isset($statusTimes[$key])) {
                                                $statusTimes[$key] = $stat->created_at;
                                            }
                                        }
                                        $currentStatus = strtolower($order->status ?? '');
                                        $reached = function (array $aliases) use ($currentStatus) {
                                            return in_array($currentStatus, array_map('strtolower', $aliases), true);
                                        };
                                        $firstTime = function (array $aliases) use ($statusTimes) {
                                            foreach ($aliases as $alias) {
                                                $key = strtolower($alias);
                                                if (isset($statusTimes[$key])) {
                                                    return $statusTimes[$key];
                                                }
                                            }
                                            return null;
                                        };
                                        $isCanceled = $reached(['cancel', 'cancelled']);
                                    @endphp
                                    <ul>
                                        <li class="step completed">
                                            <span class="title">@lang('site.Create Order')</span>
                                            <span class="time">{{ \Carbon\Carbon::parse($order->created_at)->locale(app()->getLocale())->translatedFormat('h:i A') }}</span>
                                            <span class="checkmark"></span>
                                        </li>
                                        <li class="step @if ($reached(['approve','approved','pick_up','pickup','delivered','complete','completed','cancel','cancelled'])) completed @endif">
                                            <span class="title">@lang('site.Accept Offer')</span>
                                            <span class="time">
                                                @if ($time = $firstTime(['approve','approved']))
                                                    {{ \Carbon\Carbon::parse($time)->locale(app()->getLocale())->translatedFormat('h:i A') }}
                                                @else
                                                    --
                                                @endif
                                            </span>
                                            <span class="checkmark"></span>
                                        </li>
                                        <li class="step @if ($reached(['pick_up','pickup','delivered','complete','completed'])) completed @endif">
                                            <span class="title">@lang('site.Driver Pick Up')</span>
                                            <span class="time">
                                                @if ($time = $firstTime(['pick_up','pickup']))
                                                    {{ \Carbon\Carbon::parse($time)->locale(app()->getLocale())->translatedFormat('h:i A') }}
                                                @else
                                                    --
                                                @endif
                                            </span>
                                            <span class="checkmark"></span>
                                        </li>
                                        <li class="step @if ($reached(['delivered','complete','completed'])) completed @endif">
                                            <span class="title">@lang('site.Driver Delivered')</span>
                                            <span class="time">
                                                @if ($time = $firstTime(['delivered']))
                                                    {{ \Carbon\Carbon::parse($time)->locale(app()->getLocale())->translatedFormat('h:i A') }}
                                                @else
                                                    --
                                                @endif
                                            </span>
                                            <span class="checkmark"></span>
                                        </li>
                                        <li class="step @if ($reached(['complete','completed'])) completed @endif">
                                            <span class="title">@lang('site.Order Completed')</span>
                                            <span class="time">
                                                @if ($time = $firstTime(['complete','completed']))
                                                    {{ \Carbon\Carbon::parse($time)->locale(app()->getLocale())->translatedFormat('h:i A') }}
                                                @else
                                                    --
                                                @endif
                                            </span>
                                            <span class="checkmark"></span>
                                        </li>
                                        @if ($isCanceled)
                                            <li class="step @if ($isCanceled) completed @endif">
                                                <span class="title">@lang('site.Order Cancel')</span>
                                                <span class="time">
                                                    @if ($time = $firstTime(['cancel','cancelled']))
                                                        {{ \Carbon\Carbon::parse($time)->locale(app()->getLocale())->translatedFormat('h:i A') }}
                                                    @else
                                                        --
                                                    @endif
                                                </span>
                                                <span class="checkmark"></span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row col-gap-70">
                            <div class="col-lg-6 col-12 mb-lg-0 mb-4">
                                <ul class="shipment-details">
                                    <li class="flex-space">
                                        <span class="title">@lang('site.shipment_type')</span>
                                        <span>{{ app()->getLocale() == 'ar' ? $order->shipmentType?->name_ar ?? '-' : $order->shipmentType?->name_en ?? '-' }}</span>
                                    </li>
                                </ul>
                                @if (!empty($order->shipment_details))
                                    <ul class="shipment-details">
                                        <li class="flex-space">
                                            <span class="title">@lang('site.shipment_details')</span>
                                            <span>{{ $order->shipment_details }}</span>
                                        </li>
                                    </ul>
                                @endif
                                <ul class="extra-precautions my-4">
                                    @if ($order->spoil_quickly == 1)
                                        <li>@lang('site.spoil_quickly')</li>
                                    @endif
                                    @if ($order->breakable == 1)
                                        <li>@lang('site.breakable')</li>
                                    @endif
                                </ul>
                                <ul class="shipment-details">
                                    <li class="flex-space">
                                        <span class="title">@lang('site.Shipment Size')</span>
                                        @php
                                            $sizeKey = strtolower($order->size ?? '');
                                            $sizeLabel = $sizeKey ? __('site.' . $sizeKey) : '-';
                                            if ($sizeKey && $sizeLabel === 'site.' . $sizeKey) {
                                                $sizeLabel = $order->size;
                                            }
                                        @endphp
                                        <span>{{ $sizeLabel }}</span>
                                    </li>
                                    <li class="flex-space">
                                        <span class="title">@lang('site.weight')</span>
                                        <span>{{ $order->weight_ton }} @lang('site.ton')</span>
                                    </li>
                                    <li class="flex-space">
                                        <span class="title">@lang('site.total_price')</span>
                                        <span>{{ $order->total_price != 0.0 ? number_format($order->total_price, 2, '.', '') : number_format($order->ton_price * $order->weight_ton, 2, '.', '') }}
                                            {{ ' ' . setting('currency_atr') }}</span>
                                    </li>
                                    @if ($order->accountant && $order->accountant->fine != '')
                                        <li class="flex-space">
                                            <span class="title">@lang('site.fine')</span>
                                            <span>{{ $order->accountant->fine }}
                                                {{ ' ' . setting('currency_atr') }}</span>
                                        </li>
                                    @endif
                                    @if ($order->accountant)
                                        <li class="flex-space">
                                            <span class="title">@lang('site.service_seeker_fee')</span>
                                            <span>{{ $order->accountant->service_seeker_fee }}
                                                {{ ' ' . setting('currency_atr') }}</span>
                                        </li>
                                    @endif
                                    <li class="flex-space">
                                        <span class="title">@lang('site.payment_method')</span>
                                        <span>{{ $order->paymentType?->name ?? '-' }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-6 col-12">
                                @if ($order->accountant)
                                    <ul class="shipment-details">
                                        <li class="flex-space">
                                            <span class="title">@lang('site.service_provider_commission')</span>
                                            <span>{{ $order->accountant->service_provider_commission }} %</span>
                                        </li>
                                        <li class="flex-space">
                                            <span class="title">@lang('site.operating_costs')</span>
                                            <span>{{ $order->accountant->operating_costs }}
                                                {{ ' ' . setting('currency_atr') }}</span>
                                        </li>
                                        <li class="flex-space">
                                            <span class="title">@lang('site.expenses')</span>
                                            <span>{{ $order->accountant->expenses }}
                                                {{ ' ' . setting('currency_atr') }}</span>
                                        </li>
                                        <li class="flex-space">
                                            <span class="title"> @lang('site.service_provider_amount')</span>
                                            <span>{{ $order->accountant->service_provider_amount }}
                                                {{ ' ' . setting('currency_atr') }}</span>
                                        </li>
                                    </ul>
                                    @if ($order->offer)
                                        <div class="order-summary my-4">
                                            <h4>@lang('site.checkout_summary')</h4>
                                            <ul class="mb-3">
                                                <li>
                                                    <span>@lang('site.shipping_price') (
                                                        {{ app()->getLocale() == 'ar' ? setting('app_name_ar') : setting('app_name_en') }})</span>
                                                    <span>{{ $order->offer->price ?? 0.0 }}
                                                        {{ ' ' . setting('currency_atr') }}</span>
                                                </li>
                                                <li>
                                                    <span>@lang('site.vat')</span>
                                                    <span>{{ $order->offer->sub_total - $order->offer->price ?? 0.0 }}
                                                        {{ ' ' . setting('currency_atr') }}</span>
                                                </li>
                                            </ul>
                                            <div class="flex-space subtotal">
                                                <span class="flex-align-center">
                                                    <img src="{{ asset('assets/images/svgs/money-circle.svg') }}"
                                                        alt="">
                                                    @lang('site.sub_total')
                                                </span>
                                                <span class="amount">{{ $order->offer->sub_total ?? 0.0 }}
                                                    {{ ' ' . setting('currency_atr') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                @if ($order->serviceProvider)
                                    @php
                                        $driver = $order->serviceProvider;
                                        $driverStats = $driver->evaluationsReceived()
                                            ->selectRaw('COALESCE(AVG(rate),0) as avg_rate, COUNT(*) as total_rates')
                                            ->first();
                                        $driverRating = round($driverStats?->avg_rate ?? 0, 2);
                                        $driverRatingCount = $driverStats?->total_rates ?? 0;
                                        $driverImage = $driver->userData && $driver->userData->image != '' ? asset($driver->userData->image) : asset('uploads/users/default.png');
                                        $driverPhone = $driver->phone ?? $driver->userData?->phone ?? '';
                                        $driverEmail = $driver->email ?? '';
                                        $driverRateRoute = null;
                                        if (Route::has('admin.drivers.evaluate') && $driver->type == 'driver') {
                                            $driverRateRoute = route('admin.drivers.evaluate', $driver->id);
                                        } elseif (Route::has('admin.companies.evaluate') && $driver->type == 'driversCompany') {
                                            $driverRateRoute = route('admin.companies.evaluate', $driver->id);
                                        } elseif (Route::has('admin.factories.evaluate') && $driver->type == 'factory') {
                                            $driverRateRoute = route('admin.factories.evaluate', $driver->id);
                                        }
                                    @endphp
                                    <div class="driver-card">
                                        <div class="flex-align-center">
                                            <img src="{{ $driverImage }}" alt="{{ $driver->name }}">
                                            <div class="flex-column">
                                                <span class="name">{{ $driver->name }}</span>
                                                @if ($driverRateRoute)
                                                    <a href="{{ $driverRateRoute }}" class="rate">
                                                        <div class="flex-align-center">
                                                            @for ($star = 1; $star <= 5; $star++)
                                                                <img src="{{ asset($star <= round($driverRating) ? 'assets/images/svgs/star-fill.svg' : 'assets/images/svgs/star.svg') }}"
                                                                    alt="">
                                                            @endfor
                                                        </div>
                                                        <span class="value">{{ number_format($driverRating, 2) }}</span>
                                                        <span class="count">({{ $driverRatingCount }})</span>
                                                    </a>
                                                @else
                                                    <span class="rate">
                                                        <div class="flex-align-center">
                                                            @for ($star = 1; $star <= 5; $star++)
                                                                <img src="{{ asset($star <= round($driverRating) ? 'assets/images/svgs/star-fill.svg' : 'assets/images/svgs/star.svg') }}"
                                                                    alt="">
                                                            @endfor
                                                        </div>
                                                        <span class="value">{{ number_format($driverRating, 2) }}</span>
                                                        <span class="count">({{ $driverRatingCount }})</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="driver-actions">
                                            <a href="{{ $driverPhone ? 'tel:' . $driverPhone : '#' }}">
                                                <img src="{{ asset('assets/images/svgs/call.svg') }}" alt="">
                                                @lang('site.call')
                                            </a>
                                            <a href="{{ $driverEmail ? 'mailto:' . $driverEmail : '#' }}">
                                                <img src="{{ asset('assets/images/svgs/message.svg') }}" alt="">
                                                @lang('site.message')
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <!-- Start OrderTracking Modal -->
    <div class="modal fade" id="TrackingModal" tabindex="-1" role="dialog" aria-labelledby="TrackingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered zoom-animation">
            <div class="modal-content fog-background">
                <div class="bring-to-front">
                    <div class="modal-header flex-center">
                        <h4 class="modal-title text-navy mb-0">@lang('site.order_tracking')</h4>
                        <button type="button" id="TrackingModalClose" class="btn-close" data-dismiss="modal"
                            aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                <path id="Exclusion_23" data-name="Exclusion 23"
                                    d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                    transform="translate(-0.384 0.422)" fill="#d27979" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body position-relative px-0 pb-0">
                        <div class="flex-column max-width-60 px-md-0 px-3 mb-4">
                            <div class="tracking-path">
                                <img src="{{ asset('assets/images/track-path.png') }}" alt="">
                                <span class="flex-column">
                                    <span class="from">
                                        <span id="from">@lang('site.tracking_placeholder_address')</span>
                                    </span>
                                    <span class="to">
                                        <span id="to">@lang('site.tracking_placeholder_address')</span>
                                    </span>
                                </span>
                            </div>
                            <ul class="tracking-informations">
                                <li>
                                    <img src="{{ asset('assets/images/svgs/time-check.svg') }}" alt="">
                                    @lang('site.estimated_time'):&nbsp;<span id="duration"></span>
                                </li>
                                <li>
                                    <img src="{{ asset('assets/images/svgs/road.svg') }}" alt="">
                                    @lang('site.road_distance'):&nbsp;<span id="distance"></span>
                                </li>
                            </ul>
                        </div>
                        <div id="map" class="map" style="height: 460px; width: 100%;"></div>
                        <div class="flex-center">
                            <a href="#" class="btn btn-navy shadow-none" data-dismiss="modal"
                                aria-label="Close">@lang('site.back_to_order')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- Data Tables -->
    <script src='{{ asset('assets/tiny/js/jquery.dataTables.min.js') }}'></script>
    <script src='{{ asset('assets/tiny/js/dataTables.bootstrap4.min.js') }}'></script>
    <!-- DataTables Playground (Setups, Options, Actions) -->
    <script src='{{ asset('assets/js/dataTables-init.js') }}'></script>

    <!-- Google Maps Js -->
    {{-- <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCWsYnE6Jsdi4SGqw50cYLDcSYI8eAYL7k&callback=initMap&language={{app()->getLocale()}}">
    </script> --}}
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJf7AnrqzR7AVTi2uFIrT9UTfF6dLRqEM&callback=initMap&libraries=places&language={{ app()->getLocale() }}">
    </script>
    <script>
        /*============= Google Map Scripts =============*/
        $('#TrackingModalClose, a[data-dismiss="modal"][aria-label="Close"]').on('click', function(e) {
            e.preventDefault();
            $('#TrackingModal').modal('hide');
        });
        let map, map1, map2, activeInfoWindow, markers = [];
        var directionsService, directionsDisplay;

        function drawPath(directionsService, directionsDisplay, start, end) {
            directionsService.route({
                origin: start,
                destination: end,
                optimizeWaypoints: true,
                travelMode: window.google.maps.DirectionsTravelMode.WALKING
            }, function(response, status) {
                if (status === 'OK') {
                    directionsDisplay.setDirections(response);
                    var infowindow = new window.google.maps.InfoWindow({
                        content: "@lang('site.drop_of_address')<br>" + " " + response.routes[0].legs[0].distance.text
                    })
                } else {
                    alert("@lang('site.direction_problem_due_to') " + status);
                }
            });
        }

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 30.036053390817127,
                    lng: 31.236625493518176,
                },
                zoom: 16,
                disableDefaultUI: true,
                mapTypeId: 'terrain'
            });
        }

        var previousMarker;

        function showModal(plat, plng, dlat, dlng, drlat, drlng, order) {
            let order1 = order;
            $('#TrackingModal').modal('show');

            // initMap();
            if (directionsDisplay != null) {
                directionsDisplay.setMap(null);
                directionsDisplay = null;
            }
            directionsService = new google.maps.DirectionsService;
            directionsDisplay = new google.maps.DirectionsRenderer;
            directionsDisplay.setMap(map);
            directionsDisplay.setOptions({
                polylineOptions: {
                    strokeColor: 'red'
                }
            });
            let mylatelng = {
                lat: parseFloat(plat),
                lng: parseFloat(plng)
            };
            let myLatlng1 = {
                lat: parseFloat(dlat),
                lng: parseFloat(dlng)
            };

            drawPath(directionsService, directionsDisplay, mylatelng, myLatlng1);
            if (drlat != 0 && drlng != 0) {
                let myLatlng2 = {
                    lat: parseFloat(drlat),
                    lng: parseFloat(drlng)
                };
                //console.log('myLatlng2',myLatlng2);
                if (previousMarker && previousMarker.setMap) {
                    previousMarker.setMap(null);
                }
                previousMarker = new google.maps.Marker({
                    position: myLatlng2,
                    map,
                    title: "@lang('site.driver')",
                    animation: google.maps.Animation.BOUNCE,
                    draggable: true,

                });
                var infowindow = new google.maps.InfoWindow({
                    content: "<h5  >{{ __('site.order_number') }} : <span style='color:red;'>" + order1.id +
                        "</span></h5>" +
                        "<h5  >{{ __('site.customer') }} : <span style='color:red;'>" + order1.user.name +
                        "</span></h5>" +
                        "<h5  >{{ __('site.driver') }} : <span style='color:red;'>" + order1.service_provider
                        .name + "</span></h5>" + ""
                });
                infowindow.open(map, previousMarker);
                map.addListener("center_changed", () => {
                    // 3 seconds after the center of the map has changed, pan back to the
                    // marker.
                    window.setTimeout(() => {
                        map.panTo(previousMarker.getPosition());
                    }, 3000);
                });

            }
            // set  pick up addraess & drop fo addraess
            $('#from').html(order.pick_up_address)
            $('#to').html(order.drop_of_address)
            // set destance & duration
            getDisAndDur({
                'lat': plat,
                'long': plng
            }, {
                'lat': dlat,
                'long': dlng
            })
        }

        function getDisAndDur(ori, dest) {
            var origin = new google.maps.LatLng(ori.lat, ori.long);
            var destination = new google.maps.LatLng(dest.lat, dest.long);
            // define spacing matrix options
            var options = {
                origins: [origin],
                destinations: [destination],
                travelMode: 'DRIVING',
                unitSystem: google.maps.UnitSystem.METRIC
            };
            // define spacing matrix using DistanceMatrixService()
            var service = new google.maps.DistanceMatrixService();
            service.getDistanceMatrix(options, function(response, status) {
                if (status == 'OK') {
                    var distance = response.rows[0].elements[0].distance.text;
                    var duration = response.rows[0].elements[0].duration.text;
                    $("#distance").html(distance)
                    console.log('Distance: ' + distance);
                    $("#duration").html(duration)
                    console.log('Duration: ' + duration);
                } else {
                    console.log('Error: ' + ori + ' ' + dest + ' ' + status);
                }
            });
        }
    </script>
@endsection
