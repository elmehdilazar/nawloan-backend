@extends('layouts.admin.app')
@section('title',' | ' . __('site.coupons'))
@section('styles')
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
    <div class="flex-space mb-4 dash-head">
        <h2 class="section-title mb-0">@lang('site.coupons')</h2>
        <div class="head-btns mb-0">
            <span id="checks-count" class="onchange-visible"></span>
            @if(auth()->user()->hasPermission('coupons_export'))
                <a class="btn btn-transparent navy" href="{{route('admin.coupons.export')}}"
                   title="@lang('site.export') @lang('site.coupons')">
                    @lang('site.export')
                </a>
            @endif
            @if(auth()->user()->hasPermission('coupons_disable'))
                <a href="#" id="bulk-delete" class="btn btn-danger onchange-visible">@lang('site.delete_selected')</a>
            @endif
            @if(auth()->user()->hasPermission('coupons_create'))
                <a class="btn btn-navy onchange-hidden" href="{{route('admin.coupons.create')}}"
                   title="@lang('site.create_coupons')">
                    @lang('site.create_coupons')
                </a>
            @endif
        </div>
    </div>
    {{-- @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error_message'))
        <div class="alert alert-danger">{{ session('error_message') }}</div>
    @endif --}}
    <div class="table-responsive">
        <table class="table datatables datatables-active" id="dataTable-2" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>
                    <div class="dt-checkbox">
                        <input type="checkbox" name="select_all" value="1" id="selectAll">
                        <label for="selectAll" class="visual-checkbox"></label>
                    </div>
                </th>
                <th>@lang('site.num')</th>
                <th>@lang('site.name')</th>
                <th>@lang('site.code')</th>
                <th>@lang('site.discount_type')</th>
                <th>@lang('site.discount_amount')</th>
                <th>@lang('site.expires')</th>
                <th>@lang('site.apply_to')</th>
                <th>@lang('site.status')</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($coupons as $index=>$coupon)
                <tr>
                <td>{{$coupon->id}}</td>
                <td>{{$index+1}}</td>
                <td>{{$coupon->name}}</td>
                <td>{{$coupon->code}}</td>
                <td>{{$coupon->type}}</td>
                <td>@if($coupon->type == 'fixed') {{$coupon->discount}} @else % {{$coupon->discount}}@endif</td>
                <td>{{$coupon->expiry_date}}</td>
                <td>{{$coupon->apply_to}}</td>
                <td>
                    <span class="badge badge-pill {{$coupon->active==1 ? 'badge-primary ': 'badge-danger'}}">
                        {{$coupon->getActive()}}
                    </span>
                </td>
                <td>
                    <ul class="actions">
                        @if(auth()->user()->hasPermission('coupons_update'))
                            <li>
                                <a href="{{route('admin.coupons.edit',$coupon->id)}}" class="show" title="@lang('site.edit')">
                                    <i class="fad fa-edit"></i>
                                </a>
                            </li>
                        @endif
                            @if(auth()->user()->hasPermission('coupons_disable') || auth()->user()->hasPermission('coupons_enable'))
                                @if($coupon->active==1)
                                    <li>
                                        <a class="cancel" href="#" data-toggle="modal" data-target="#enableModal_{{$index}}" title="@lang('site.disable')">
                                            <i class="fad fa-times"></i>
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <a class="success" href="#" data-toggle="modal" data-target="#enableModal_{{$index}}" title="@lang('site.enable')">
                                            <i class="fad fa-check-double"></i>
                                        </a>
                                    </li>
                                @endif
                            @endif
                    </ul>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-start">
            {{$coupons->appends(request()->query())->links()}}
        </div>

        @foreach ($coupons as $index=>$coupon)
            <!-- Modal -->
            <div class="modal fade mini-modal" id="enableModal_{{$index}}" tabindex="-1" role="dialog"
                 aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                                @if($coupon->active==0)
                                    @lang('site.enable') @lang('site.coupon_code')
                                @else
                                    @lang('site.disable') @lang('site.coupon_code')
                                @endif
                            </h5>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                    <path id="Exclusion_23" data-name="Exclusion 23"
                                          d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                          transform="translate(-0.384 0.422)" fill="#d27979" />
                                </svg>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-center">
                                @if ($coupon->active==0)
                                    @lang('site.enable_confirm') @lang('site.coupon_code') @lang('site.question_mark')
                                @else
                                    @lang('site.disable_confirm') @lang('site.coupon_code') @lang('site.question_mark')
                                @endif
                            </p>
                            <form action="{{route('admin.coupons.changeStatus',$coupon->id)}}" method="post">@csrf
                                @method('post')
                                <div class="d-flex justify-content-center mt-3 gap-15">
                                    <button type="submit" class="btn shadow-none @if($coupon->active==0) btn-navy @else btn-danger @endif"
                                            title="@if($coupon->active==0)@lang('site.enable')@else @lang('site.disable')@endif">
                                        {{--<i class="fa @if($country->active==0) fa-check @else fa-remove @endif"></i>--}}
                                        @if($coupon->active==0)
                                            @lang('site.enable')
                                        @else
                                            @lang('site.disable')
                                        @endif
                                    </button>
                                    <button type="submit" class="btn shadow-none btn-transparent navy" title="@lang('site.cancel')"
                                            data-dismiss="modal" aria-label="Close">
                                        {{--<i class="fas fa-remove"></i> --}}
                                        @lang('site.cancel')
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
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
                if($(this).is(':checked')){
                    selected.push($(this).val());
                }
            });
            if(selected.length === 0){
                alert(@json(__('site.no_items_selected')));
                return;
            }
            if(confirm(@json(__('site.delete_selected_confirm')))){
                var url = @json(route('admin.coupons.destroy-selected')) + '?ids=' + selected.join(',');
                window.location = url;
            }
        });
    </script>
@endsection
