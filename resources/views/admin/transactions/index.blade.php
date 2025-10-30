@extends('layouts.admin.app')
@section('title',' | ' . __('site.transactions'))
@section('styles')
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/daterangepicker.css')}}">
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
<div class="flex-space mb-4 dash-head">
    <h2 class="section-title mb-0">@lang('site.transactions')</h2>
    <div class="flex-align-center gap-15">
        <div class="head-btns mb-0">
            <span id="checks-count" class="onchange-visible"></span>
            <a href="{{route('admin.transactions.export')}}" title="@lang('site.export') @lang('site.transactions')"
               class="btn btn-transparent navy">@lang('site.export')</a>
            @if(auth()->user()->hasPermission('transactions_disable'))
                <a href="#" id="bulk-delete" class="btn btn-danger onchange-visible">@lang('site.delete_selected')</a>
            @endif
        </div>
        <div class="quick-search">
            <div class="input-group mb-0">
                <div class="position-relative">
                    <input class="fe-14 border-radius-24" type="search" name="" itemid=""
                           placeholder="Quick Search">
                    <img src="{{asset('assets/images/svgs/search.svg')}}" alt="" class="icon">
                </div>
            </div>
        </div>
    </div>
</div>
<form action="{{route('admin.transactions.index')}}" method="GET">
    <div class="row search-by-group mb-4">
        <div class="col-12 mb-3">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="input-group mb-0">
                        <div id="reportrange" class="angle-down">
                            <i class="fad fa-calendar-alt"></i>
                            <span></span>
                        </div>
                        <input type="hidden" id="start_date" name="start_date">
                        <input type="hidden" id="end_date" name="end_date">
                    </div>
                </div>
            </div>
        </div>
        {{--<div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3">--}}
        {{--    <div class="input-group mb-0">--}}
        {{--        <label for="" class="fe-14">@lang('site.search')</label>--}}
        {{--        <div class="position-relative">--}}
        {{--            <input class="fe-14" type="text" name="search" id="search"--}}
        {{--                   value="{{request()->search}}" placeholder="@lang('site.search')">--}}
        {{--            <img src="{{asset('assets/images/svgs/search.svg')}}" alt="" class="icon">--}}
        {{--        </div>--}}
        {{--    </div>--}}
        {{--</div>--}}
        <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
            <div class="input-group mb-0">
                <label for="" class="fe-14">@lang('site.pay_transaction_id')</label>
                <div class="position-relative">
                    <input class="fe-14" type="text" name="transaction_id" id="transaction_id"
                           value="{{request()->transaction_id}}" placeholder="@lang('site.pay_transaction_id')">
                    <img src="{{asset('assets/images/svgs/search.svg')}}" alt="" class="icon">
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
            <div class="input-group mb-0">
                <label for="" class="fe-14">@lang('site.order_number')</label>
                <div class="position-relative">
                    <input class="fe-14" type="text" name="order_number" id="order_number"
                           value="{{request()->order_number}}" placeholder="@lang('site.order_number')">
                    <img src="{{asset('assets/images/svgs/search.svg')}}" alt="" class="icon">
                </div>
            </div>
        </div>
        <div class="form-group col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
            <div class="select-group mb-0">
                <label for="simple-select2" class="fe-14">@lang('site.payment_method')</label>
                <select class="form-control select2 fe-14" id="payment_method" name="payment_method">
                    <option value="0" selected>@lang('site.view_all')</option>
                    @foreach ($payment_methods as $method)
                        <option value="{{$method->id}}" {{ request()->payment_method== $method->id ? 'selected' : '' }}>
                            {{$method->name}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
            <div class="select-group mb-0">
                <label for="simple-select2" class="fe-14">@lang('site.user')</label>
                <select class="form-control select2 fe-14" id="user_id" name="user_id">
                    <option value="0" selected>@lang('site.view_all')</option>
                    @foreach ($users as $user)
                        <option value="{{$user->id}}" {{ request()->user_id== $user->id ? 'selected' : '' }}>
                            {{$user->name}}
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
<ul class="transactions-boxes mb-5">
    <li>
        <a href="#">
            <img src="{{asset('assets/images/svgs/money-gray.svg')}}" alt="">
            <h5>@lang('site.All Net Profit')</h5>
            <h2>{{$earn['total_profit']}} {{setting('currency_atr')!='' ? setting('currency_atr') : ''}}</h2>
        </a>
    </li>
    <li>
        <a href="#">
            <img src="{{asset('assets/images/svgs/money-gray.svg')}}" alt="">
            <h5>@lang('site.Net Profit')</h5>
            <h2>{{$earn['profit']}} {{setting('currency_atr')!='' ? setting('currency_atr') : ''}}</h2>
        </a>
    </li>
    <li>
        <a href="#">
            <img src="{{asset('assets/images/svgs/money-gray.svg')}}" alt="">
            <h5>@lang('site.All Income')</h5>
            <h2>{{$earn['total_income']}} {{setting('currency_atr')!='' ? setting('currency_atr') : ''}}</h2>
        </a>
    </li>
    <li>
        <a href="#">
            <img src="{{asset('assets/images/svgs/money-gray.svg')}}" alt="">
            <h5>@lang('site.Net Income')</h5>
            <h2>{{$earn['income']}} {{setting('currency_atr')!='' ? setting('currency_atr') : ''}}</h2>
        </a>
    </li>
</ul>
<table class="table datatables lg-responsive datatables-active" id="">
    <thead>
        <tr>
            <th>
                <div class="dt-checkbox">
                    <input type="checkbox" name="select_all" value="1" id="selectAll">
                    <label for="selectAll" class="visual-checkbox"></label>
                </div>
            </th>
            <th>@lang('site.num')</th>
            <th class="min-width-170">@lang('site.customer')</th>
            <th class="min-width-170">@lang('site.driver')</th>
            <th>@lang('site.order')</th>
            <th>@lang('site.amount')</th>
            <th>@lang('site.profit')</th>
            <th>@lang('site.fee')</th>
            <th>@lang('site.currency')</th>
            <th>@lang('site.payment_method')</th>
            <th>@lang('site.payment_type')</th>
            <th>@lang('site.status')</th>
            <th>@lang('site.at')</th>
            <th>@lang('site.edit')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($trans as $index=>$tran)
        <tr>
            <td>{{$tran->id}}</td>
            <td>{{$index + 1}}</td>
            <td>
                <div class="user-col">
                    <img src="{{$tran->order->user->image != '' ? asset($tran->order->user->image) : asset('uploads/users/default.png')}}" alt="">
                    <span class="name">{{$tran->user->name}}</span>
                </div>
            </td>
            <td>
                @if($tran->order->serviceProvider)
                <div class="user-col">
                    <img src="{{$tran->order->serviceProvider->image != '' ? asset($tran->order->serviceProvider->image) : asset('uploads/users/default.png')}}" alt="">
                    <span class="name">{{$tran->order->serviceProvider->name}}</span>
                </div>
                @else
                    --
                @endif
            </td>
            <td>{{$tran->order->id}}</td>
            <td>{{$tran->payTransaction->amount}}</td>
            <td>
                @if($tran->order->serviceProvider)
                    @if($tran->order->serviceProvider->type=='driver')
                        {{ $tran->payTransaction->amount * 15 / 100 }}
                    @elseif($tran->order->serviceProvider->type=='driverCompany')
                        {{ $tran->payTransaction->amount * 10 / 100 }}
                    @endif
                @endif
            </td>
            <td>{{$tran->payTransaction->fee}}</td>
            <td>{{$tran->payTransaction->currency}}</td>
            <td>{{$tran->payMethod->name}}</td>
            <td>{{$tran->payTransaction->payment_type}}</td>
            <td>
                @if($tran->payTransaction->status=='success')
                    <span class="badge badge-success">{{$tran->payTransaction->status}}</span>
                @elseif($tran->payTransaction->status=='Refounded')
                    <span class="badge badge-primary">{{$tran->payTransaction->status}}</span>
                @elseif($tran->payTransaction->status=='Failed')
                    <span class="badge badge-danger">{{$tran->payTransaction->status}}</span>
                @elseif($tran->payTransaction->status=='pending')
                    <span class="badge badge-warning">{{$tran->payTransaction->status}}</span>
                @endif
            </td>
            <td>{{$tran->created_at}}</td>
            <td>
                <ul class="actions">
                    <li><a href="#" title="@lang('site.show')" class="show"><i class="fad fa-eye"></i></a></li>
                    {{--<li><a href="#" class="cancel"><i class="fad fa-times"></i></a></li>--}}
                </ul>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="d-flex justify-content-center">
    {{$trans->appends(request()->query())->links()}}
</div>
@endsection
@section('scripts')
    <!-- Data Tables -->
    <script src='{{asset('assets/tiny/js/jquery.dataTables.min.js')}}'></script>
    <script src='{{asset('assets/tiny/js/dataTables.bootstrap4.min.js')}}'></script>
    <!-- DataTables Playground (Setups, Options, Actions) -->
    <script src='{{ asset('assets/js/dataTables-init.js') }}?v={{ @filemtime(public_path('assets/js/dataTables-init.js')) }}'></script>
    <script>
        $(document).on('click', '#bulk-delete', function (e) {
            e.preventDefault();
            var selected = [];
            $('.datatables-active tbody input[type="checkbox"]').not('#selectAll').each(function(){
                if($(this).is(':checked')){ selected.push($(this).val()); }
            });
            if(selected.length === 0){
                alert(@json(__('site.no_items_selected')));
                return;
            }
            if(confirm(@json(__('site.delete_selected_confirm')))){
                var url = @json(route('admin.transactions.destroy-selected')) + '?ids=' + selected.join(',');
                window.location = url;
            }
        });
    </script>
    <!-- DateRangePicker JS -->
    <script src="{{asset('assets/tiny/js/daterangepicker.js')}}"></script>
    <script>
        let req_start="{{request()->start_date}}";
        let req_end="{{request()->end_date}}";
        var start = moment().subtract(29, 'days');
        var end = moment();
        function cb(start, end) {
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
        $('[data-range-key="Custom Range"]').html('Translated text');
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
