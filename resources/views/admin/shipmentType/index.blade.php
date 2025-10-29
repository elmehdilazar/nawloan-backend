@extends('layouts.admin.app')
@section('title',' | ' . __('site.shipments_types'))
@section('styles')
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
<div class="flex-space mb-4 dash-head">
    <h2 class="section-title mb-0">@lang('site.shipments_types')</h2>
    <div class="head-btns mb-0">
        <span id="checks-count" class="onchange-visible"></span>
        @if(auth()->user()->hasPermission('shipments_types_export'))
            <a class="btn btn-transparent navy" href="{{route('admin.shipment.export')}}"
               title="@lang('site.export') @lang('site.shipments_types')">
                @lang('site.export')
            </a>
        @endif
        @if(auth()->user()->hasPermission('shipments_types_disable'))
        <a href="#" id="bulk-delete" class="btn btn-danger onchange-visible">@lang('site.delete_selected')</a>
        @endif
        @if(auth()->user()->hasPermission('shipments_types_create'))
            <a class="btn btn-navy onchange-hidden" href="{{route('admin.shipment.create')}}"
               title="@lang('site.add') @lang('site.shipment_type')">
                @lang('site.add') @lang('site.shipment_type')
            </a>
        @endif
    </div>
</div>
<form action="{{route('admin.shipment.index')}}" method="GET">
    <div class="row search-by-group mb-5">
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <div class="input-group mb-0">
                <label for="name_en" class="fe-14">@lang('site.name_en')</label>
                <div class="input-group">
                    <input class="fe-14" type="text" name="name_en" id="name_en"
                           value="{{request()->name_en}}" placeholder="@lang('site.name_en')">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <div class="input-group mb-0">
                <label for="name_ar" class="fe-14">@lang('site.name_ar')</label>
                <div class="input-group">
                    <input class="fe-14" type="text" name="name_ar" id="name_ar"
                           value="{{request()->name_ar}}" placeholder="@lang('site.name_ar')">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-lg-4 col-md-6 col-12 mb-3">
            <div class="select-group mb-0">
                <label for="active" class="fe-14">@lang('site.status')</label>
                <select class="form-control select2 fe-14" id="active" name="active">
                    <option value="0" selected>@lang('site.view_all')</option>
                    <option value="2" {{ request()->active== '2' ? 'selected' : '' }}>
                        @lang('site.active')
                    </option>
                    <option value="1" {{ request()->active== '1' ? 'selected' : '' }}>
                        @lang('site.inactive')
                    </option>
                </select>
            </div>
        </div>
        <div class="flex-center mt-3">
            <button type="submit" class="btn btn-navy" title="@lang('site.search')">@lang('site.search')</button>
        </div>
    </div>
</form>
<form id="bulk-delete-form" action="{{ route('admin.shipment.destroy-selected') }}" method="POST" style="display:none;">@csrf</form>
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
            <th>@lang('site.name_en')</th>
            <th>@lang('site.name_ar')</th>
            <th>@lang('site.status')</th>
            <th>@lang('site.edit')</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($shipmentTypes as $index=>$shipment)
        <tr>
            <td>{{$shipment->id}}</td>
            <td>{{$index + 1}}</td>
            <td>{{$shipment->name_en}}</td>
            <td class="text-arabic">{{$shipment->name_ar}}</td>
            <td>
                <span class="badge badge-pill {{$shipment->active==1 ? 'badge-primary ': 'badge-danger'}}">{{$shipment->getActive()}}</span>
            </td>
            <td>
                <ul class="actions">
                    @if(auth()->user()->hasPermission('shipments_types_update'))
                        <li>
                            <a href="{{route('admin.shipment.edit',$shipment->id)}}" class="show" title="@lang('site.edit')">
                                <i class="fad fa-edit"></i>
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasPermission('shipments_types_disable') || auth()->user()->hasPermission('shipments_types_enable'))
                        @if($shipment->active==1)
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
        {{$shipmentTypes->appends(request()->query())->links()}}
    </div>
    @foreach ($shipmentTypes as $index=>$shipment)
        <div class="modal fade mini-modal" id="enableModal_{{$index}}" tabindex="-1" role="dialog"
             aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                            @if($shipment->active==0)
                                @lang('site.enable') @lang('site.car')
                            @else
                                @lang('site.disable') @lang('site.car')
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
                            @if ($shipment->active==0)
                                @lang('site.enable_confirm') @lang('site.car') @lang('site.question_mark')
                            @else
                                @lang('site.disable_confirm') @lang('site.car') @lang('site.question_mark')
                            @endif
                        </p>
                        <form action="{{route('admin.shipment.changeStatus',$shipment->id)}}" method="post">
                            @csrf
                            @method('post')
                            <div class="d-flex justify-content-center mt-3 gap-15">
                                <button type="submit" class="btn shadow-none @if($shipment->active==0) btn-navy @else btn-danger @endif"
                                        title="@if($shipment->active==0)@lang('site.enable')@else @lang('site.disable')@endif">
                                    {{--<i class="fa @if($truck->active==0) fa-check @else fa-remove @endif"></i>--}}
                                    @if($shipment->active==0)
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
                var url = @json(route('admin.shipment.destroy-selected.get')) + '?ids=' + selected.join(',');
                window.location = url;
            }
        });
    </script>
@endsection
