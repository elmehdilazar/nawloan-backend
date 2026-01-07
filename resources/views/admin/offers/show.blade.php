@extends('layouts.admin.app')
@section('title',' | ' .  __('site.show') . ' '. __('site.offer').' - '. $offer->id)
@section('content')
<div class="row page-titles mx-2">
    <div class="col p-md-0">
        <h4>@lang('site.offers')</h4>
    </div>
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.index')}}">@lang('site.dashboard')</a>
            </li>
            <li class="breadcrumb-item"><a href="{{route('admin.offers.index')}}">@lang('site.offers')</a>
            </li>
            <li class="breadcrumb-item active">@lang('site.show') @lang('site.offer') {{' - '. $offer->id}}</li>
        </ol>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card mb-2">
                    <div class="card-header">
                      <div class="d-flex justify-content-between">
                        <h3> @lang('site.order_info')</h3>
                        <div class="d-flex justify-content-between">
                        </div>
                      </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>@lang('site.number')</td><td>{{$offer->id}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('site.order_number')</td><td><<a href="{{route('admin.orders.show', $offer->order_id)}}" title="@lang('site.view')">{{$offer->order_id}}</a>
</td>
                                </tr>
                                <tr>
                                    <td>@lang('site.service_seeker')</td>
                                    <td><a title="@lang('site.view')">{{$offer->user->name}}</a></td>
                                </tr><tr>
                                    <td>@lang('site.service_provider')</td>
                                    <td><a
                                            title="@lang('site.view')">{{$offer->order->user->name}}</a>
                                    </td>
                                </tr>
                                <tr><td>@lang('site.price')</td>
                                <td>{{$offer->price .' '. setting('currency_atr')}}</td></tr>
                                @if(!empty($offer->change_by))
                                </tr>
                                <tr>
                                    <td>@lang('site.change_by')</td>
                                    <td>{{$offer->change_by }}</td>
                                </tr>
                                @endif
                                @if(!empty($offer->notes))
                                </tr>
                                <tr>
                                    <td>@lang('site.notes')</td>
                                    <td>{{$offer->notes }}</td>
                                </tr>
                                @endif
                                <tr><td>@lang('site.status')</td><td>
                                         <span class="badge badge-pill
                @if($offer->status =='pending' || $offer->status =='pend'  ) badge-warning
                @elseif($offer->status=='approve' || $offer->status=='pick_up' || $offer->status=='delivered') badge-primary
                @elseif ($offer->status=='complete' ||  $offer->status=='completed' ) badge-success
                @elseif ($offer->status=='cancel' || $offer->status=='cancelled' ) badge-danger @endif">
                    @if($offer->status=='pending' || $offer->status =='pend')
                        @lang('site.pend')
                    @elseif($offer->status=='approve')
                        @lang('site.approval')
                    @elseif($offer->status=='pick_up')
                        @lang('site.Pick Up')
                    @elseif($offer->status=='delivered')
                        @lang('site.Delivered')
                    @elseif($offer->status=='complete' ||  $offer->status=='completed' )
                        @lang('site.completed')
                    @elseif($offer->status=='cancel' || $offer->status=='cancelled')
                        @lang('site.canceled')
                    @endif
                </span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row justify-content-center mb-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>@lang('site.offer_track')</h3>
            </div>
            <div class="card-body">
                    <div class="card-body p-0">
                        <table class="table secondary-table-bordered table-bordered">
                            <thead class="thead-secondary"><tr>
                            <td>@lang('site.num')</td>
                            <td>@lang('site.status')</td>
                            <td>@lang('site.change_by')</td>
                            <td>@lang('site.notes')</td>
                            <td>@lang('site.at')</td></tr></thead>
                            <tbody>
                                @foreach ($statuses as $index=>$stat)
                                    <tr>
                                        <td>{{$index +1}}</td>
                                        <td><span class="badge badge-pill
                @if($stat->status =='pending' || $stat->status =='pend'  ) badge-warning
                @elseif($stat->status=='approve' || $stat->status=='pick_up'  || $stat->status=='delivered') badge-primary
                @elseif ($stat->status=='complete' ||  $stat->status=='completed' ) badge-success
                @elseif ($stat->status=='cancel' || $stat->status=='cancelled' ) badge-danger @endif">
                    @if($stat->status=='pending' || $stat->status =='pend')
                        @lang('site.pend')
                    @elseif($stat->status=='approve')
                        @lang('site.approval')
                    @elseif($stat->status=='pick_up')
                        @lang('site.Pick Up')
                    @elseif($stat->status=='delivered')
                        @lang('site.Delivered')
                    @elseif($stat->status=='complete' ||  $stat->status=='completed' )
                        @lang('site.completed')
                    @elseif($stat->status=='cancel' || $stat->status=='cancelled')
                        @lang('site.canceled')
                    @endif
               
                </span></td>
                                        <td>{{$stat->changedBy->name}}</td>
                                        <td>{{$stat->notes}}</td>
                                        <td>{{$stat->created_at->diffForHumans()}}</td>
                                    </tr>
                                @endforeach
                                @if($statuses->count()==0)
                                <tr>
                                    <td colspan="5" style="text-align: center !important;">@lang('site.no_records_found')</td>
                                </tr>
                                @endif
                            </tbody>
                        </table><div class="d-flex justify-content-center">
                            {{$statuses->appends(request()->query())->links()}}
                        </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
