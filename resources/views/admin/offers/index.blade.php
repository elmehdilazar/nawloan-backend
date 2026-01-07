@extends('layouts.admin.app')
@section('title',' | ' . __('site.offers'))
@section('styles')
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/daterangepicker.css')}}">
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
<div class="flex-space mb-4 dash-head">
    <h2 class="section-title mb-0">@lang('site.all_offers')</h2>
    <div class="flex-align-center gap-15">
        <div class="head-btns mb-0">
            <span id="checks-count" class="onchange-visible"></span>
            @if(auth()->user()->hasPermission('offers_export') )
            <a href="{{route('admin.offers.export')}}" class="btn btn-transparent navy">@lang('site.export')</a>
            @endif
            @if(auth()->user()->hasPermission('offers_delete'))
                <a href="#" class="btn btn-danger onchange-visible">@lang('site.delete')</a>
            @endif
        </div>
        <div class="quick-search">
            <div class="input-group mb-0">
                <div class="position-relative">
                    <input class="fe-14 border-radius-24" type="search" name="" itemid=""
                           placeholder="@lang('site.quick_search')">
                    <img src="{{asset('assets/images/svgs/search.svg')}}" alt="" class="icon">
                </div>
            </div>
        </div>
    </div>
</div>
<form action="{{route('admin.offers.index')}}" method="GET">
    <div class="row search-by-group mb-5">
        <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
            <div class="input-group mb-0">
                <label for="" class="fe-14">@lang('site.date')</label>
                <div id="reportrange" class="angle-down"
                     name="date" value="{{request()->date}}">
                    <i class="fad fa-calendar-alt"></i>
                    <span></span>
                </div>
                <input type="hidden" id="start_date" name="start_date">
                <input type="hidden" id="end_date" name="end_date">
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
            <div class="input-group mb-0">
                <label for="" class="fe-14">@lang('site.number')</label>
                <div class="position-relative">
                    <input class="fe-14" type="text" name="number" id="number" value="{{request()->number}}"
                           placeholder="@lang('site.number')">
                    <img src="{{asset('assets/images/svgs/search.svg')}}" alt="" class="icon">
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
            <div class="input-group mb-0">
                <label for="" class="fe-14">@lang('site.Price Range')</label>
                <div class="flex-align-center gap-15">
                    <input class="fe-14" type="text" name="from" id="from"
                           value="{{request()->from}}" placeholder="@lang('site.from')">
                    <input class="fe-14" type="text" name="to" id="to"
                           value="{{request()->to}}" placeholder="@lang('site.to')">
                </div>
            </div>
        </div>
        <div class="form-group col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
            <div class="select-group mb-0">
                <label for="simple-select2" class="fe-14">@lang('site.service_provider')</label>
                <select class="form-control select2 fe-14" id="user_id" name="user_id">
                    <option value="0" selected>@lang('site.view_all')</option>
                    @foreach ($users as $user)
                        <option value="{{$user->id}}" {{ request()->user_id== $user->id ? 'selected' : ''}}>
                            {{$user->name . ' - '}} @lang('site.the_'.$user->type.'')
                        </option>
                    @endforeach
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
        <tr role="row">
            <th>
                <div class="dt-checkbox">
                    <input type="checkbox" name="select_all" value="1" id="selectAll">
                    <label for="selectAll" class="visual-checkbox"></label>
                </div>
            </th>
            <th>@lang('site.num')</th>
            <th class="min-width-170">@lang('site.service_provider')</th>
            <th>@lang('site.order_number')</th>
            <th>@lang('site.price')</th>
            <th>@lang('site.time')</th>
            <th>@lang('site.status')</th>
            <th>@lang('site.edit')</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($offers as $index=>$offer )
        <tr>
            <td></td>
            <td>{{$offer->id}}</td>
            <td>
                <div class="user-col">
                    <img src="{{$offer->user->userData->image !='' ? asset($offer->user->userData->image) : asset('uploads/users/default.png')}}"
                         alt="{{$offer->user->name}}">
                    <span class="name">{{$offer->user->name}}</span>
                </div>
            </td>
            <td><a href="{{route('admin.orders.index',['number'=>$offer->order_id])}}">{{$offer->order_id}}</a></td>
            <td>{{$offer->price .' '.setting('currency_atr')}}</td>
            <td>{{$offer->created_at}}</td>
            <td>
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
                </span>
            </td>
            <td>
                <ul class="actions">
                    @if(auth()->user()->hasPermission('offers_read'))
                        <li>
                            <a href="{{route('admin.offers.show',['id'=>$offer->id])}}" title="@lang('site.show')" class="show"
                               >
                                <i class="fad fa-eye"></i>
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasPermission('offers_update') && ($offer->status!='complete' && $offer->status!='cancel'))
                        <li>
                            <a href="#" class="cancel"
                               onclick="event.preventDefault();document.getElementById('cancel-form_{{$index}}').submit();">
                                <i class="fad fa-times"></i>
                            </a>
                            <form action="{{route('admin.orders.changeOfferStatus',$offer->id)}}"
                                  method="POST" id="cancel-form_{{$index}}">
                                @csrf
                                @method('put')
                                <input type="hidden" id="status" name="status" value="cancel">
                            </form>
                        </li>
                    @endif
                </ul>
            </td>
        </tr>
    @endforeach
    @if($offers->count()==0)
        <tr>
            <td colspan="11" style="text-align: center !important;">@lang('site.no_records_found')</td>
        </tr>
    @endif
    </tbody>
</table>
<div class="d-flex justify-content-center">
    {{$offers->appends(request()->query())->links()}}
</div>
@endsection
@section('scripts')
    <!-- Data Tables -->
    <script src='{{asset('assets/tiny/js/jquery.dataTables.min.js')}}'></script>
    <script src='{{asset('assets/tiny/js/dataTables.bootstrap4.min.js')}}'></script>
    <!-- DataTables Playground (Setups, Options, Actions) -->
    <script src='{{asset('assets/js/dataTables-init.js')}}'></script>
    <!-- DateRangePicker JS -->
    <script src="{{asset('assets/tiny/js/daterangepicker.js')}}"></script>
    <script>
        let req_start="{{request()->start_date}}";
        let req_end="{{request()->end_date}}";
        var start = moment().subtract(29, 'days');
        var end = moment();
        function cb(start, end)
        {
            if(req_start!='' && req_start!=null && req_end!='' && req_end!=null){
                $('#reportrange span').html(start.format(req_start) + ' - ' + end.format(req_end));
            }else{
                $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            }
        }
        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
            console.log(picker.startDate.format('YYYY-MM-DD'));
            console.log(picker.endDate.format('YYYY-MM-DD'));
            $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
            $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
            cb(picker.startDate.format('YYYY-MM-DD'),picker.endDate.format('YYYY-MM-DD'));
        });
        $('[data-range-key="Custom Range"]').html("@lang('site.custom_range')");
        if(req_start !='' && req_start !=null && req_end !='' && req_end !=null){
            $('#reportrange').daterangepicker(
            {
                showDropdowns: true,
                startDate:  moment(req_start ).local(),
                endDate:  moment(req_end ).local(),
                ranges:
                {
                    "@lang('site.Today')": [moment(), moment()],
                    "@lang('site.Yesterday')": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    "@lang('site.Last 7 Days')": [moment().subtract(6, 'days'), moment()],
                    "@lang('site.Last 30 Days')": [moment().subtract(29, 'days'), moment()],
                    "@lang('site.This Month')": [moment().startOf('month'), moment().endOf('month')],
                    "@lang('site.Last Month')": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                }
            }, cb);
        }else{
            $('#reportrange').daterangepicker(
            {
                showDropdowns: true,
                startDate: start,
                endDate: end,
                ranges:
                {
                    "@lang('site.Today')": [moment(), moment()],
                    "@lang('site.Yesterday')": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    "@lang('site.Last 7 Days')": [moment().subtract(6, 'days'), moment()],
                    "@lang('site.Last 30 Days')": [moment().subtract(29, 'days'), moment()],
                    "@lang('site.This Month')": [moment().startOf('month'), moment().endOf('month')],
                    "@lang('site.Last Month')": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                }
            }, cb);
            }
        cb(start, end);
    </script>
@endsection
