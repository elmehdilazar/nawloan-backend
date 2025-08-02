@extends('layouts.admin.app')
@section('title',' | ' .  __('site.search'))
@section('styles')
<style>
		#map {
			height: 400px;
		    width: 100%;
		}
		/* Optional: Makes the sample page fill the window. */
		html, body {
			height: 100%;
			margin: 0;
			padding: 0;
		}
</style>{{--
<script>
	function initMap() {
		/*var mapOptions = {
			zoom: 8,
			center: new google.maps.LatLng(44, -110),
			mapTypeId: 'satellite'
		};*/
		var mapOptions = {
			zoom: 8,
			center: new google.maps.LatLng(51.508742,-0.120850),//new google.maps.LatLng(44, -110),
			mapTypeId: google.maps.MapTypeId.MAP,
			zoomControl: true,
			zoomControlOptions: {
				style: google.maps.ZoomControlStyle.LARGE
			},
			heading: 90,
			tilt: 45,
		};
	var map = new google.maps.Map(document.getElementById('map'), mapOptions);
}
</script>
--}}@endsection
@section('content')
<div class="row justify-content-center my-2">
    <div class="col-12">
        <div class="row justify-content-start m-2">
            <h3 class="mb-3">@lang('site.users')</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-secondary">

                    <tr><td scope="col">#</td><td scope="col">@lang('site.name')</td>
                    <td scope="col">@lang('site.phone')</td>
                    <td scope="col">@lang('site.type')</td><td scope="col">@lang('site.status')</td><td scope="col">@lang('site.edit')</td></tr></thead>
                <tbody>
                    @isset($users)
                @foreach ($users as $index=>$user)
                @if ($user->type=='user' && (auth()->user()->hasPermission('users_read') || auth()->user()->hasRole('superadministrator') )
                || $user->type=='driver' && (auth()->user()->hasPermission('drivers_read') || auth()->user()->hasRole('superadministrator') )
                || $user->type=='driverCompany' && (auth()->user()->hasPermission('driverCompanies_read') || auth()->user()->hasRole('superadministrator') )
                || $user->type=='factory' && (auth()->user()->hasPermission('factories_read') || auth()->user()->hasRole('superadministrator') ))
                    <tr><td>{{$index + 1}}</td><td>{{$user->name}}</td>
                    <td style="direction: ltr;">{{$user->phone}}</td><td>@lang('site.'.$user->type.'')</td>
                    <td><span class="badge {{$user->active==1 ? 'badge-primary ': 'badge-danger'}}">{{$user->getActive()}}</span>
                    </td>
                <td>
                    @if(auth()->user()->hasPermission('users_update'))  
                    <?php if($user->type=='driver'){?>
                         <a href="{{route('admin.drivers.edit',$user->id)}}" class="mx-2"><i class="fas fa-pen"></i></a><?php
                    }
                    elseif($user->type=='factory'){
                        ?>
                         <a href="{{route('admin.factories.edit',$user->id)}}" class="mx-2"><i class="fas fa-pen"></i></a><?php
                    }
                    elseif($user->type=='user'){
                        ?>
                         <a href="{{route('admin.customers.edit',$user->id)}}" class="mx-2"><i class="fas fa-pen"></i></a><?php
                    }
                     elseif($user->type=='driverCompany'){
                        ?>
                         <a href="{{route('admin.companies.edit',$user->id)}}" class="mx-2"><i class="fas fa-pen"></i></a><?php
                    }?>
                    @endif
                    @if($user->active==1)

                @if(auth()->user()->hasPermission('users_disable'))
                <a class=" text-danger mx-2" href="#" data-toggle="modal"
                    data-target="#enableModal_{{$index}}">
                    <i class="fas fa-remove "></i></a>
                    @endif
                    @else
                @if(auth()->user()->hasPermission('users_enable'))
                <a class=" text-success mx-2" href="#" data-toggle="modal"
                    data-target="#enableModal_{{$index}}">
                    <i class="fas fa-check "></i></a>
                    @endif
                    @endif
                </td></tr>

                <!-- Modal -->
                <div class="modal fade" id="enableModal_{{$index}}" tabindex="-1" role="dialog"
                    aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="verticalModalTitle_{{$index}}">@if ($user->active==0)
                                    @lang('site.enable') @lang('site.user')
                                    @else
                                    @lang('site.disable') @lang('site.user')
                                    @endif</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body"> @if ($user->active==0)
                                @lang('site.enable_confirm') @lang('site.user') @lang('site.question_mark')
                                @else
                                @lang('site.disable_confirm') @lang('site.user') @lang('site.question_mark')
                                @endif
                                <form action="{{route('admin.users.changeStatus',$user->id)}}" method="post">
                                    @csrf
                                    @method('post')
                                    <div class="d-flex justify-content-center mt-2">
                                        <button type="submit" class="btn btn-success mx-2"
                                            title="@if($user->active==0)@lang('site.enable')@else @lang('site.disable')@endif">
                                            <i class="fa @if($user->active==0) fa-check @else fa-remove @endif"></i>
                                            @if($user->active==0)@lang('site.enable')@else
                                            @lang('site.disable')@endif
                                        </button>
                                        <button type="submit" class="btn btn-danger mx-2" title="@lang('site.cancel')"
                                            data-dismiss="modal" aria-label="Close">
                                            <i class="fas fa-remove"></i> @lang('site.cancel')
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
                @endisset
                @if(!empty($users) && $users->count()==0)
                <tr>
                    <td colspan="7" style="text-align: center !important;">@lang('site.no_records_found')</td>
                </tr>
                @endif
                </tbody>
            </table>
            @if(!empty($users))
            <div class="d-flex justify-content-center">
                {{$users->appends(request()->query())->links()}}
            </div>
            @endif
        </div>
    </div>
</div>
<div class="row justify-content-start my-2">
                @if (auth()->user()->hasPermission('orders_read') || auth()->user()->hasRole('superadministrator'))
                <div class="row justify-content-start m-2">
                    <h3 class="mb-3">@lang('site.orders')</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-secondary">
                            <tr role="row">
                                <th>@lang('site.num')</th>
                                <th>@lang('site.customer')</th>
                                <th>@lang('site.total_price')</th>
                                <th>@lang('site.car')</th>
                                <th>@lang('site.offers')</th>
                                <th>@lang('site.transaction_status')</th>
                                <th>@lang('site.status')</th>
                                <th>@lang('site.edit')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $index=>$order )
                            <tr>
                                <td>{{$order->id}}</td>
                                <td>
                                    <div class="d-flex">
                                       {{-- <div class="avatar avatar-md">
                                            <img src="{{$order->user->userData->image !='' ? asset($order->user->userData->image) : asset('uploads/users/default.png')}}"
                                                alt="{{$order->user->name}}" class="avatar-img rounded-circle">
                                        </div>--}} {{--commit by mohammed v2--}}
                                        <div class="avatar avatar-md">
                                            <img src="{{ $order->user->userData && $order->user->userData->image != '' ? asset($order->user->userData->image) : asset('uploads/users/default.png') }}"
                                                 alt="{{ $order->user->name }}" class="avatar-img rounded-circle">
                                        </div>
                                        <span style="padding: 12px;">{{$order->user->name}}</span>

                                    </div>
                                </td>
                                <td>        {{ number_format( ( $order->ton_price * $order->weight_ton),2, ".", "")}}{{' '. setting('currency_atr')}}</td>
                                <td><a href="#" data-toggle="modal"
                                        data-target="#truckModal_{{$index}}">{{$order->car->name_en}}</a>
                                </td>
                                <td>@if(!empty($order->offers))
                                    <a href="#" data-toggle="modal" data-target="#offersModal_{{$index}}"><i
                                            class="fas fa-file-alt"></i></a>
                                    @endif
                                </td>
                                <td>{{$order->transaction!='' ? $order->transaction->status : __('site.Delayed Payment')}}</td>
                                <td><span class="badge
                                                    @if($order->status =='pend') badge-warning
                                                    @elseif($order->status=='approve') badge-primary
                                                    @elseif($order->status=='pick_up') badge-primary
                                                    @elseif( $order->status=='delivered') badge-primary
                                                    @elseif ($order->status=='complete') badge-success
                                                    @elseif ($order->status=='cancel') badge-danger @endif">
                                        @if($order->status=='pend')
                                        @lang('site.pend')
                                        @elseif($order->status=='approve')
                                        @lang('site.approval')
                                        @elseif($order->status=='pick_up')
                                        @lang('site.Pick Up')
                                        @elseif( $order->status=='delivered')
                                        @lang('site.Delivered')
                                        @elseif($order->status=='complete')
                                        @lang('site.completed')
                                        @elseif($order->status=='cancel')
                                        @lang('site.canceled')
                                        @endif
                                    </span></td>
                                </td>

                                <td>
                                    <div class="d-flex">
                                        @if(auth()->user()->hasPermission('orders_read') )
                                        <a href="#" data-toggle="modal" data-target="#orderModal_{{$index}}"
                                            title="@lang('site.show')" style="padding: 7px;"><i class="fas fa-eye"></i>
                                        </a>
                                        @endif
                                        @if(auth()->user()->hasPermission('orders_update') )
                                        <a href="{{route('admin.orders.edit',$order->id)}}" title="@lang('site.edit')"
                                            style="padding: 7px;">
                                            <i class="fas fa-pen"></i></a>
                                        @if($order->status=='cancel')
                                        <a class=""
                                            onclick="event.preventDefault();document.getElementById('pend-form_{{$index}}').submit();"
                                            href="#" style="padding: 7px;">
                                            <i class="fas fa-check text-warning"></i>
                                        </a>
                                        <form action="{{route('admin.orders.changeStatus',$order->id)}}" method="POST"
                                            id="pend-form_{{$index}}">
                                            @csrf
                                            @method('put')
                                            <input type="hidden" id="status" name="status" value="pend">

                                        </form>
                                        @endif
                                        {{-- @if($order->status!='complete' && $order->status!='cancel' &&
                                        $order->status!='pend')
                                        <a class=""
                                            onclick="event.preventDefault();document.getElementById('complete-form_{{$index}}').submit();"
                                            style="padding: 7px;"> <i class="fas fa-check-circle text-success"></i> </a>
                                        <form action="{{route('admin.orders.changeStatus',$order->id)}}" method="POST"
                                            id="complete-form_{{$index}}">
                                            @csrf
                                            @method('put')
                                            <input type="hidden" id="status" name="status" value="complete">

                                        </form>
                                        @endif --}}
                                        @if($order->status!='complete' && $order->status!='cancel')
                                        <a class=""
                                            onclick="event.preventDefault();document.getElementById('cancel-form_{{$index}}').submit();"
                                            href="#" style="padding: 7px;"> <i class="fas fa-remove text-danger"></i>
                                        </a>
                                        <form action="{{route('admin.orders.changeStatus',$order->id)}}" method="POST"
                                            id="cancel-form_{{$index}}">
                                            @csrf
                                            @method('put')
                                            <input type="hidden" id="status" name="status" value="cancel">

                                        </form>
                                        @endif
                                         <a href="#" onclick="showModal({{$order->pick_up_late}},{{$order->pick_up_long}},{{$order->drop_of_late}},{{$order->drop_of_long}},{{$order->serviceProvider->userData->latitude ?? '0'}},{{$order->serviceProvider->userData->longitude ?? '0'}},{{$order}});" title="@lang('site.order_track')" style="padding: 7px;"><i
                                    class="fas fa-map-marker-alt"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <div class="modal fade" id="truckModal_{{$index}}" tabindex="-1" role="dialog"
                                aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                                    <div class="modal-content" style="border-radius:25px">
                                        <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                                            </h5>
                                            <button type="button" class=" btn btn-danger" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true"><i class="fas fa-remove"></i></span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-9">
                                                    <div class="form-group d-flex justify-content-center"
                                                        style="background-color: #d4d4e4;border: 3px solid #d4d4d4;border-radius: 20px;">
                                                        <img src="{{$order->car->image !='' ? asset($order->car->image) : asset('uploads/cars/default.png')}}"
                                                            style="height:150px ;width:100%;border-radius:15px;">
                                                        <label class="text-label"
                                                            style="position: absolute;top: 119px;left: 20px;background: #fff;color: #000;padding: 5px;border-radius: 10px;font-weight: 600;">
                                                            @if(app()->getLocale()=='ar')
                                                            {{$order->car->name_ar}}
                                                            @else
                                                            {{$order->car->name_en}}
                                                            @endif</label>
                                                    </div>{{--
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between">
                                                            <label
                                                                class="text-label text-muted">@lang('site.track_number')</label>
                                                            <label class="text-label">{{$order->track_number}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between">
                                                            <label
                                                                class="text-label text-muted">@lang('site.track_license_number')</label>
                                                            <label
                                                                class="text-label">{{$order->track_license_number}}</label>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="offersModal_{{$index}}" tabindex="-1" role="dialog"
                                aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content" style="border-radius:25px;background-color: #ecebef;">
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                                            </h5>
                                            <button type="button" class=" btn btn-danger" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true"><i class="fas fa-remove"></i></span>
                                            </button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <div class="row justify-content-center">
                                                <h5 style="color:#4669b2;">@lang('site.offers')</h5>
                                            </div>
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-md-8">
                                                    @foreach ($order->offers as $offer)

                                                    <div class="card mb-2 shadow" style="border-radius: 20px ;">
                                                        <div class="card-body p-2">
                                                            <div class="row d-flex px-0 mx-0">
                                                                <div class="" style="display: grid;">
                                                                    <img src="{{$offer->user->userData->image !='' ? asset($offer->user->userData->image) : asset('uploads/users/default.png')}}"
                                                                        style="width:100px ; height: 100px; border-radius: 50%;">
                                                                    <div
                                                                        style="padding-right: 2%;padding-left: 2%;text-align: start;">
                                                                        <input id="rateInput"
                                                                            value="{{$offer->user->evaluates->avg('rate')}}"
                                                                            type="text" class="rating disabled"
                                                                            data-theme="krajee-fas" data-min="0"
                                                                            data-max="5" data-size="xs" readOnly>
                                                                    </div>
                                                                    <div class="d-flex justify-content-center">
                                                                        <label>{{$offer->user->evaluates->avg('rate')}}</label>
                                                                        <label>({{$offer->user->evaluates->count()}})</label>
                                                                    </div>
                                                                </div>
                                                                @if(!empty($offer->driver->userData->car))
                                                                <div class="col mt-5">
                                                                    <h5><label>{{$offer->user->name}}</label></h5>
                                                                    <label><i class="fas fa-map-marker"
                                                                            style="color:#0396ef;"></i><label>{{$offer->driver->userData->location}}</label></label>
                                                                    <div class="d-flex justify-content-between">
                                                                        <label><i class="fas fa-truck-moving"
                                                                                style="color: #0396ef;"></i>
                                                                            <label>@if(app()->getLocale()=='ar' ){{
                                                                                $offer->driver->userData->car->name_ar }}
                                                                                @else
                                                                                {{$offer->driver->userData->car->name_en}}
                                                                                @endif</label></label>
                                                                        <label style="color:#f4c61a;">{{$offer->price}}
                                                                            {{setting('currency_atr')
                                                                            !='' ? ' '. setting('currency_atr') :
                                                                            ''}}</label>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="orderModal_{{$index}}" tabindex="-1" role="dialog"
                                aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl order-modal" role="document">
                                    <div class="modal-content" style="border-radius:25px">
                                        <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                                            </h5>
                                            {{-- <button type="button" class=" btn btn-danger" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true"><i class="fas fa-remove"></i></span>
                                            </button> --}}
                                        </div>
                                        <div class="modal-body">
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-md-11">
                                                    <h4 style="color:#1b1733;"> @lang('site.order') {{' # '.$order->id}}
                                                    </h4>
                                                    <div class="row">
                                                        @if($order->serviceProvider)

                                                            <div class="col-md-6">
                                                    <div class="d-flex justify-content-between my-4">
                                                        <div style="display:grid">

                                                             <div class="text-center">
                                                                 @lang('site.customer')/@lang('site.'.$order->user->type.'')
                                                             </div>
                                                         <div class="d-flex mt-3">
                                                            <div class="avatar avatar-md">
                                                                <img src="{{$order->user->userData->image !='' ? asset($order->user->userData->image) : asset('uploads/users/default.png')}}"
                                                                    alt="{{$order->user->name}}" class="avatar-img rounded-circle">
                                                            </div>
                                                            <div style="display:grid;padding: 4px;">
                                                                <p style="padding: 1px;margin: 1px">{{$order->user->name}}</p>
                                                                <p style="padding: 1px;margin: 1px"> @lang('site.id') : {{$order->user->id}}</p>
                                                            </div>
                                                        </div>

                                                        </div>
                                                        <div style="display:grid">
                                                             <div class="text-center">
                                                                 @lang('site.driver')/@if($order->offer)@lang('site.'.$order->offer->user->type.'')@else @lang('site.'.$order->serviceProvider->type.'')@endif
                                                             </div>
                                                         <div class="d-flex  mt-3">
                                                            <div class="avatar avatar-md">
                                                                <img src="{{$order->serviceProvider->userData->image !='' ? asset($order->serviceProvider->userData->image) : asset('uploads/users/default.png')}}"
                                                                    alt="{{$order->serviceProvider->name}}" class="avatar-img rounded-circle">
                                                            </div>
                                                            <div style="display:grid;padding: 4px;">
                                                                <p style="padding: 1px;margin: 1px">{{$order->serviceProvider->name}}</p>
                                                                <p style="padding: 1px;margin: 1px"> @lang('site.id') : {{$order->serviceProvider->id}}</p>
                                                            </div>

                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                            <div class="col-md-6">
                                                            <div class="row my-1">
                                                                <div class="col-md-6 pt-3" style="color:#847333;">
                                                                    <div class="d-flex justify-content-between">
                                                                        <i class="fas fa-map-marker"></i>
                                                                        <div class="d-flex justify-content-start">
                                                                            <label class="text-label">
                                                                                {{$order->serviceProvider->userData->location}}</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between">
                                                                        <i class="fa-solid fa-tire"></i>
                                                                        <div class="d-flex justify-content-start">
                                                                            <label class="text-label"> {{$order->car->frames}}
                                                                                @lang('site.axes')</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between">
                                                                        <i class="fas fa-dumbbell"></i>
                                                                        <div class="d-flex justify-content-start">
                                                                            <label class="text-label"> {{$order->car->weight}}
                                                                                @lang('site.weight')</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between">
                                                                        <i class="fas fa-truck-moving"></i>
                                                                        <div class="d-flex justify-content-start">
                                                                            <label class="text-label"> {{app()->getLocale()=='ar' ?
                                                                                $order->car->name_ar :
                                                                                $order->car->name_en}}</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <img src="{{$order->car->image !='' ? asset($order->car->image) : asset('uploads/cars/default.png')}}" alt="@lang('site.order')"
                                                                        style="width: 100%;height:140px;">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @endif
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">

                                                            @if(!$order->serviceProvider)
                                                            <div class="row my-1">
                                                                <div class="col-md-6 pt-3" style="color:#847333;">
                                                                    <div class="d-flex justify-content-between">
                                                                        <i class="fas fa-map-marker"></i>
                                                                        <div class="d-flex justify-content-start">
                                                                            <label class="text-label">
                                                                                {{$order->pick_up_address }}</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between">
                                                                        <i class="fa-solid fa-tire"></i>
                                                                        <div class="d-flex justify-content-start">
                                                                            <label class="text-label"> {{$order->car->frames}}
                                                                                @lang('site.axes')</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between">
                                                                        <i class="fas fa-dumbbell"></i>
                                                                        <div class="d-flex justify-content-start">
                                                                            <label class="text-label"> {{$order->car->weight}}
                                                                                @lang('site.weight')</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between">
                                                                        <i class="fas fa-truck-moving"></i>
                                                                        <div class="d-flex justify-content-start">
                                                                            <label class="text-label">
                                                                                {{app()->getLocale()=='ar' ?
                                                                                $order->car->name_ar :
                                                                                $order->car->name_en}}</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <img src="{{$order->car->image !='' ? asset($order->car->image) : asset('uploads/cars/default.png')}}"
                                                                        alt="@lang('site.order')"
                                                                        style="width: 100%;height:140px;">
                                                                </div>
                                                            </div>
                                                            @endif
                                                            <div class="row my-2">
                                                                <div class="col-md-12">
                                                                    <div class="card" style="border-radius: 14px;">
                                                                        <div class="card-body py-2">
                                                                            <div class="d-flex justify-content-between">
                                                                                <div>
                                                                                    <i class="fas fa-map-marked"
                                                                                        style="width: 20px;"></i>
                                                                                   [ {{$order->pick_up_address.','.$order->pick_up_address}}]
                                                                                </div>
                                                                                <div>
                                                                                    <i class="fa fa-check-circle-o"
                                                                                        style="font-size: 1.4rem;"></i>
                                                                                </div>
                                                                            </div>
                                                                            <hr style="margin:0;">
                                                                            <div class="d-flex justify-content-between"
                                                                                style="color: #0844f8;">
                                                                                <div>
                                                                                    <i class="fas fa-map-marker-alt"
                                                                                        style="width: 20px;"></i>
                                                                                    [{{$order->drop_of_address.','.$order->drop_of_address}}]
                                                                                </div>
                                                                                <div>
                                                                                    <i class="fa fa-check-circle-o"
                                                                                        style="font-size: 1.4rem;"></i>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row my-2">
                                                                <div class="col-md-12">
                                                                    <div class="card" style="border-radius: 14px;">
                                                                        <div class="card-body py-2">
                                                                            <div class="d-flex justify-content-between">
                                                                                <div style="display: grid;">
                                                                                    <h6
                                                                                        style="color: #9293a9; text-align: center;">
                                                                                        @lang('site.date')</h6>
                                                                                    <h6
                                                                                        style="color:#0844f8;text-align: center;">
                                                                                        {{date("Y-m-d",strtotime($order->shipping_date))}}
                                                                                    </h6>
                                                                                </div>
                                                                                <div style="display: grid;">
                                                                                    <h6
                                                                                        style="color: #9293a9; text-align: center;">
                                                                                        @lang('site.time')</h6>
                                                                                    <h6
                                                                                        style="color:#0844f8;text-align: center;">
                                                                                        {{date("h:i
                                                                                        A",strtotime($order->shipping_date))}}
                                                                                    </h6>
                                                                                </div>
                                                                                <div style="display: grid;">
                                                                                    <h6
                                                                                        style="color: #9293a9; text-align: center;">
                                                                                        @lang('site.expected_ton_price')
                                                                                    </h6>
                                                                                    <h6
                                                                                        style="color:#0844f8;text-align: center;">
                                                                                        {{$order->ton_price}}{{
                                                                                        setting('currency_atr') !='' ? '
                                                                                        '.
                                                                                        setting('currency_atr') : ''}}
                                                                                    </h6>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row my-2">
                                                                <div class="col-md-12">
                                                                    <div class="card"
                                                                        style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                        <div class="card-body py-2">
                                                                            <div class="d-flex justify-content-between">
                                                                                <div>
                                                                                    <h6
                                                                                        style="color: #a18c33; text-align: center;">
                                                                                        @lang('site.shipment_type')</h6>
                                                                                </div>
                                                                                <div>
                                                                                    <h6
                                                                                        style="color: #a18c33; text-align: center;">
                                                                                        {{app()->getLocale()=='ar' ?
                                                                                        $order->shipmentType->name_ar :
                                                                                        $order->shipmentType->name_en }}
                                                                                    </h6>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if(!empty($order->shipment_details))
                                                            <div class="row my-2">
                                                                <div class="col-md-12">
                                                                    <div class="card"
                                                                        style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                        <div class="card-body py-2">
                                                                            <div class="d-flex justify-content-between">
                                                                                <div>
                                                                                    <h6
                                                                                        style="color: #a18c33; text-align: center;">
                                                                                        @lang('site.shipment_details')</h6>
                                                                                </div>
                                                                                <div>
                                                                                    <h6
                                                                                        style="color: #a18c33; text-align: center;text-align: start;">
                                                                                        <p style="text-indent: 15px;">{{
                                                                                        $order->shipment_details }}</p>
                                                                                    </h6>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            <div class="row my-2">
                                                                <div class="col-md-12">
                                                                    <div class="d-flex justify-content-start px-1">
                                                                        <div class="custom-control custom-radio ">
                                                                            <input type="radio" id="spoil_quickly"
                                                                                name="spoil_quickly" disabled
                                                                                class="custom-control-input"
                                                                                value="medium"
                                                                                @if($order->spoil_quickly==1) checked
                                                                            @endif>
                                                                            <label class="custom-control-label"
                                                                                for="spoil_quickly">@lang('site.spoil_quickly')</label>
                                                                        </div>
                                                                        <div class="custom-control custom-radio mx-2">
                                                                            <input type="radio" id="breakable"
                                                                                name="breakable" disabled
                                                                                class="custom-control-input"
                                                                                value="large" @if($order->breakable==1)
                                                                            checked @endif>
                                                                            <label class="custom-control-label"
                                                                                for="breakable">@lang('site.breakable')</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row my-2">
                                                                <div class="col-md-12">
                                                                    <div class="card"
                                                                        style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                        <div class="card-body py-2">
                                                                            <div class="d-flex justify-content-between">
                                                                                <div>
                                                                                    <h6
                                                                                        style="color: #a18c33; text-align: center;">
                                                                                        @lang('site.Shipment Size')</h6>
                                                                                </div>
                                                                                <div>
                                                                                    <h6
                                                                                        style="color: #a18c33; text-align: center;">
                                                                                        {{$order->size}}</h6>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row my-2">
                                                                <div class="col-md-12">
                                                                    <div class="card"
                                                                        style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                        <div class="card-body py-2">
                                                                            <div class="d-flex justify-content-between">
                                                                                <div>
                                                                                    <h6
                                                                                        style="color: #a18c33; text-align: center;">
                                                                                        @lang('site.weight')</h6>
                                                                                </div>
                                                                                <div>
                                                                                    <h6
                                                                                        style="color: #a18c33; text-align: center;">
                                                                                        {{$order->weight_ton}}
                                                                                        @lang('site.ton')
                                                                                    </h6>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                                <div class="row my-2">
                                                                    <div class="col-md-12">
                                                                        <div class="card" style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                            <div class="card-body py-2">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            @lang('site.total_price')</h6>
                                                                                    </div>
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            {{ $order->total_price!=0.00 ? number_format($order->total_price,2, ".", "") : number_format( ( $order->ton_price * $order->weight_ton),2, ".", "")}}{{' '. setting('currency_atr')}}
                                                                                        </h6>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <div class="row my-2">
                                                                <div class="col-md-12">
                                                                    <div class="card" style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                        <div class="card-body py-2">
                                                                            <div class="d-flex justify-content-between">
                                                                                <div>
                                                                                    <h6 style="color: #a18c33; text-align: center;">@lang('site.payment_method')</h6>
                                                                                </div>
                                                                                <div>
                                                                                    <h6 style="color: #a18c33; text-align: center;">{{$order->paymentType->name}}
                                                                                    </h6>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                                @if($order->accountant && $order->accountant->fine!='')
                                                                <div class="row my-2">
                                                                    <div class="col-md-12">
                                                                        <div class="card" style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                            <div class="card-body py-2">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            @lang('site.fine')</h6>
                                                                                    </div>
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            {{$order->accountant->fine}} {{' '.setting('currency_atr')}}
                                                                                        </h6>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                             @if($order->accountant)
                                                                <div class="row my-2">
                                                                    <div class="col-md-12">
                                                                        <div class="card" style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                            <div class="card-body py-2">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            @lang('site.service_seeker_fee')</h6>
                                                                                    </div>
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            {{$order->accountant->service_seeker_fee}} {{' '.setting('currency_atr')}}
                                                                                        </h6>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif

                                                        </div>
                                                        <div class="col-md-6">
                                                            <h4 style="color:#847333;">@lang('site.history')</h6>
                                                                <div class="d-flex justify-content-between">
                                                                    <div class="d-flex-justify-content-start">
                                                                        <div class="form-group ">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input "
                                                                                    disabled id="pend" name="pend"
                                                                                    checked>
                                                                                <label class="custom-control-label"
                                                                                    for="pend"
                                                                                    style="color:#847333;max-width:150px;width:150px;">
                                                                                    @lang('site.Create Order')
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex-justify-content-center"
                                                                        style="max-width: 200px; text-align:center !important;">
                                                                        <div class="form-group "
                                                                            style="text-align: center !important;color:#847333;">
                                                                            {{date("h:i
                                                                            A",strtotime($order->created_at))}}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <div class="d-flex-justify-content-start">
                                                                        <div class="form-group ">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input"
                                                                                    id="approve" disabled name="approve"
                                                                                    @if($order->status
                                                                                =='approve' ||
                                                                                $order->status=='pick_up' ||
                                                                                $order->status=='delivered' ||
                                                                                $order->status=='complete'||
                                                                                $order->status=='cancel') checked
                                                                                @endif>
                                                                                <label class="custom-control-label"
                                                                                    for="approve"
                                                                                    style="color:#847333;max-width:150px;width:150px;">
                                                                                    @lang('site.Accept Offer')
                                                                                </label>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex-justify-content-center"
                                                                        style="max-width: 200px; text-align:center !important;">
                                                                        <div class="form-group"
                                                                            style="text-align: center !important;color:#847333;">
                                                                            @php $counter =1 @endphp
                                                                            @forelse ($order->statuses as $stat )
                                                                            @if($stat->status=='approve' && $counter==1)
                                                                            {{date("h:i A",strtotime($stat->created_at))}}
                                                                            @php $counter +=1 @endphp
                                                                            @endif
                                                                            @empty
                                                                            --
                                                                            @endforelse
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <div class="d-flex-justify-content-start">
                                                                        <div class="form-group ">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input"
                                                                                    id="pick_up" disabled name="pick_up"
                                                                                    @if($order->status
                                                                                =='pick_up' ||
                                                                                $order->status=='delivered' ||
                                                                                $order->status=='complete') checked
                                                                                @endif>

                                                                                <label class="custom-control-label"
                                                                                    for="pick_up"
                                                                                    style="color:#847333;max-width:150px;width:150px;">
                                                                                    @lang('site.Driver Pick Up')
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex-justify-content-center"
                                                                        style="max-width: 200px; text-align:center !important;">
                                                                        <div class="form-group"
                                                                            style="text-align: center !important;color:#847333;">
                                                                            @php $counter =1 @endphp
                                                                            @forelse ($order->statuses as $stat )
                                                                            @if($stat->status=='pick_up' && $counter==1)
                                                                            {{date("h:i A",strtotime($stat->created_at))}}
                                                                            @php $counter +=1 @endphp
                                                                            @endif
                                                                            @empty
                                                                            --
                                                                            @endforelse
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <div class="d-flex-justify-content-start">
                                                                        <div class="form-group ">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input"
                                                                                    id="delivered" disabled
                                                                                    name="delivered" @if($order->status
                                                                                =='delivered' ||
                                                                                $order->status=='complete' )
                                                                                checked @endif>
                                                                                <label class="custom-control-label"
                                                                                    for="delivered"
                                                                                    style="color:#847333;max-width:150px;width:150px;">
                                                                                    @lang('site.Driver Delivered')
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex-justify-content-center"
                                                                        style="max-width: 200px; text-align:center !important;">
                                                                        <div class="form-group"
                                                                            style="text-align: center !important;color:#847333;">
                                                                            @php $counter =1 @endphp
                                                                            @forelse ($order->statuses as $stat )
                                                                            @if($stat->status=='delivered' && $counter==1)
                                                                            {{date("h:i A",strtotime($stat->created_at))}}
                                                                            @php $counter +=1 @endphp
                                                                            @endif
                                                                            @empty
                                                                            --
                                                                            @endforelse
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <div class="d-flex-justify-content-start">
                                                                        <div class="form-group ">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input"
                                                                                    id="complete" disabled
                                                                                    name="complete" @if($order->status
                                                                                =='complete' ) checked
                                                                                @endif>
                                                                                <label class="custom-control-label"
                                                                                    for="complete"
                                                                                    style="color:#847333;max-width:150px;width:150px;">
                                                                                    @lang('site.Order Completed')
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex-justify-content-center"
                                                                        style="max-width: 200px; text-align:center !important;">
                                                                        <div class="form-group"
                                                                            style="text-align: center !important;color:#847333;">
                                                                            @php $counter =1 @endphp
                                                                            @forelse ($order->statuses as $stat )
                                                                            @if($stat->status=='complete' && $counter==1)
                                                                            {{date("h:i A",strtotime($stat->created_at))}}
                                                                            @php $counter +=1 @endphp
                                                                            @endif
                                                                            @empty
                                                                            --
                                                                            @endforelse
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if($order->status=='cancel')
                                                                <div class="d-flex justify-content-between">
                                                                    <div class="d-flex-justify-content-start">
                                                                        <div class="form-group ">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input"
                                                                                    id="cancel" disabled name="cancel"
                                                                                    @if($order->status
                                                                                =='cancel' ) checked @endif>
                                                                                <label class="custom-control-label"
                                                                                    for="cancel"
                                                                                    style="color:#847333;max-width:150px;width:150px;">
                                                                                    @lang('site.Order Cancel')
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex-justify-content-center"
                                                                        style="max-width: 200px; text-align:center !important;">
                                                                        <div class="form-group"
                                                                            style="text-align: center !important;color:#847333;">
                                                                            @php $counter =1 @endphp
                                                                            @forelse ($order->statuses as $index=>$stat)
                                                                            @if($stat->status=='cancel' && $index <1 && $counter==1)
                                                                                {{date(" h:i A",strtotime($stat->created_at))}}
                                                                                @php $counter +=1 @endphp
                                                                                @endif
                                                                                @empty
                                                                                --
                                                                                @endforelse
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @endif
                                                                @if($order->accountant)
                                                                <div class="row my-2">
                                                                    <div class="col-md-12">
                                                                        <div class="card" style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                            <div class="card-body py-2">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            @lang('site.service_provider_commission')</h6>
                                                                                    </div>
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            {{$order->accountant->service_provider_commission}} %
                                                                                        </h6>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row my-2">
                                                                    <div class="col-md-12">
                                                                        <div class="card" style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                            <div class="card-body py-2">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            @lang('site.operating_costs')</h6>
                                                                                    </div>
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            {{$order->accountant->operating_costs}} {{' '.setting('currency_atr')}}
                                                                                        </h6>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row my-2">
                                                                    <div class="col-md-12">
                                                                        <div class="card" style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                            <div class="card-body py-2">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            @lang('site.expenses')</h6>
                                                                                    </div>
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            {{$order->accountant->expenses}} {{' '.setting('currency_atr')}}
                                                                                        </h6>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row my-2">
                                                                    <div class="col-md-12">
                                                                        <div class="card" style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                            <div class="card-body py-2">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            @lang('site.service_provider_amount')</h6>
                                                                                    </div>
                                                                                    <div>
                                                                                        <h6 style="color: #a18c33; text-align: center;">
                                                                                            {{$order->accountant->service_provider_amount}} {{' '.setting('currency_atr')}}
                                                                                        </h6>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                 @if($order->offer)
                                                    <label> @lang('site.checkout_summary')</label>
                                                    <div class="row my-2">
                                                        <div class="col-md-12">
                                                            <div class="card" style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                <div class="card-body py-2">
                                                                    <div class="d-flex justify-content-between">
                                                                        <div>
                                                                            <h6 style="color: #a18c33; text-align: center;">
                                                                                @lang('site.shipping_price') ( {{app()->getLocale()=='ar' ? setting('app_name_ar') : setting('app_name_en')}}) </h6>
                                                                        </div>
                                                                        <div>
                                                                            <h6 style="color: #a18c33; text-align: center;">
                                                                                {{$order->offer->price ?? 0.00}} {{' '.setting('currency_atr')}}
                                                                            </h6>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row my-2">
                                                        <div class="col-md-12">
                                                            <div class="card" style="background-color: transparent;border-radius: 14px;border: 2px solid white;">
                                                                <div class="card-body py-2">
                                                                    <div class="d-flex justify-content-between">
                                                                        <div>
                                                                            <h6 style="color: #a18c33; text-align: center;">
                                                                                @lang('site.vat') </h6>
                                                                        </div>
                                                                        <div>
                                                                            <h6 style="color: #a18c33; text-align: center;">
                                                                                {{$order->offer->sub_total - $order->offer->price ?? 0.00}} {{' '.setting('currency_atr')}}
                                                                            </h6>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row my-2">
                                                        <div class="col-md-12">
                                                            <div class="card" style="background-color: #7688a1;border-radius: 14px;border: 0px solid white;">
                                                                <div class="card-body py-2">
                                                                    <div class="d-flex justify-content-between">
                                                                        <div>
                                                                            <h6 style="color: #fff; text-align: center;">
                                                                               <i class="fas fa-cog " style="color:#fff;"></i>    @lang('site.sub_total') </h6>
                                                                        </div>
                                                                        <div>
                                                                            <h6 style="color: #fff; text-align: center;">
                                                                             {{$order->offer->sub_total  ?? 0.00}} {{' '.setting('currency_atr')}}
                                                                            </h6>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                                @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                           
                            <tr>
                                <td colspan="11" style="text-align: center !important;">@lang('site.no_records_found')
                                </td>
                            </tr>
            
            </tbody>
        </table>

            <div class="d-flex justify-content-center">
               
            </div>
        @endif
</div>
        <div class="modal fade" id="TrackingModal" tabindex="-1" role="dialog"
            aria-labelledby="TrackingModal" aria-hidden="true" >
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" style="border-radius:25px">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title" id="TrackingModal">
                            @lang('site.order_tracking')
                        </h5>
                        <button type="button" class=" btn btn-danger" data-dismiss="modal" aria-label="Close" id="TrackingModalClose">
                            <span aria-hidden="true"><i class="fas fa-remove"></i></span>
                        </button>
                    </div>
                    <div class="modal-body">
                     {{-- --}}
                        <div class="d-flex justify-content-center">
                            <span id="padd_"></span>
                            <br>{{----}}
                            <span id="pla_"></span>
                            <span id="plg_"></span>
                            <span id="dadd_"></span>
                            <br>{{----}}
                            <span id="dla_"></span>
                            <span id="dlg_"></span>

                        </div>
                        <div id="map" class="map" ></div>

                    </div>
                </div>
            </div>
        </div>
@endsection

@section('scripts')

<script async defer
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCWsYnE6Jsdi4SGqw50cYLDcSYI8eAYL7k&callback=initMap&language={{app()->getLocale()}}">
</script>
<script>
$('#TrackingModalClose').on('click',function(e){
    e.preventDefault();
    $('#TrackingModal').modal('dismis');

})
    let map,map1,map2, activeInfoWindow, markers = [];
            var directionsService, directionsDisplay ;
	function drawPath(directionsService, directionsDisplay,start,end) {
            directionsService.route({
              origin: start,
              destination: end,
              optimizeWaypoints: true,
              travelMode:window.google.maps.DirectionsTravelMode.WALKING
            }, function(response, status) {
                if (status === 'OK') {
                directionsDisplay.setDirections(response);
                //console.log(response);
                var infowindow = new window.google.maps.InfoWindow({
                    content: "@lang('site.drop_of_address')<br>"+" " +response.routes[0].legs[0].distance.text})

              //alert('drawed');
                } else {
               alert('Problem in showing direction due to ' + status);
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
           mapTypeId:'terrain'
        });
    }
    var previousMarker;
        function showModal(plat,plng,dlat,dlng,drlat,drlng,order){
            //console.log(drlat,drlng);
            let order1=order;
            //console.log(order1);
           $('#TrackingModal').modal('show');
           // initMap();

                if(directionsDisplay != null) {
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
            let mylatelng={ lat:parseFloat(plat), lng:parseFloat(plng)};
            let myLatlng1={ lat:parseFloat(dlat), lng:parseFloat(dlng)};
            //console.log(myLatlng1)
            //console.log(mylatelng)
             drawPath(directionsService, directionsDisplay,mylatelng,myLatlng1);
             if(drlat !=0 && drlng !=0){
            let myLatlng2={ lat:parseFloat(drlat), lng:parseFloat(drlng)};
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
                    content:
                 "<h5  >{{__('site.order_number')}} : <span style='color:red;'>"+order1.id+"</span></h5>"+
                 "<h5  >{{__('site.customer')}} : <span style='color:red;'>"+order1.user.name+"</span></h5>"+
                 "<h5  >{{__('site.driver')}} : <span style='color:red;'>"+ order1.service_provider.name+"</span></h5>"+""
             });
              infowindow.open(map,previousMarker);
           map.addListener("center_changed", () => {
            // 3 seconds after the center of the map has changed, pan back to the
            // marker.
            window.setTimeout(() => {
              map.panTo(previousMarker.getPosition());
            }, 3000);
          });}
        }

</script>
@endsection
