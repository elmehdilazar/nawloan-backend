@extends('layouts.admin.app')
@section('title',' | ' .  __('site.external_gateway'))
@section('styles')
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
    <div class="flex-space mb-4 dash-head">
        <h2 class="section-title mb-0">@lang('site.external_gateway')</h2>
        <div class="head-btns mb-0">
            <span id="checks-count" class="onchange-visible"></span>
            <a class="btn btn-transparent navy" href="{{route('admin.gateway.export')}}"
               title="@lang('site.export') @lang('site.the_gateway')">
                @lang('site.export')
            </a>
            <a href="#" id="bulk-delete" class="btn btn-danger onchange-visible">@lang('site.delete_selected')</a>
            <a class="btn btn-navy onchange-hidden" href="{{route('admin.gateway.create')}}"
               title="@lang('site.add') @lang('site.the_gateway')">
                @lang('site.add') @lang('site.the_gateway')
            </a>
        </div>
    </div>
    <form action="{{route('admin.gateway.index')}}" method="GET">
        <div class="row search-by-group mb-5">
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <div class="input-group mb-0">
                    <label for="name">@lang('site.name')</label>
                    <div class="input-group">
                        <input type="text" class="fe-14" name="name" id="name"
                               value="{{request()->name}}" placeholder="@lang('site.name')">
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
                    <label for="type">@lang('site.type')</label>
                    <select class="form-control select2 fe-14" name="type" id="type">
                        <option value="" selected>@lang('site.view_all')</option>
                        <option value="payment"{{request()->type=='payment' ? 'selected' : ''}}>@lang('site.the_payment')</option>
                        <option value="sms"{{request()->type=='sms' ? 'selected' : ''}}>@lang('site.sms')</option>
                    </select>
                </div>
            </div>
            <div class="form-group col-lg-4 col-md-6 col-12 mb-3">
                <div class="select-group mb-0">
                    <label for="active">@lang('site.status')</label>
                    <select class="form-control select2 fe-14" id="active" name="active">
                        <option value="0" selected >@lang('site.view_all')</option>
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
                <th class="min-width-170">@lang('site.name')</th>
                <th>@lang('site.type')</th>
                <th>@lang('site.status')</th>
                <th>@lang('site.edit')</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($gateway as $index=>$gate)
            <tr>
                <td>{{$gate->id}}</td>
                <td>{{$index + 1}}</td>
                <td>
                    <div class="user-col">
                        @if(!empty($gate->image))
                        <img src="{{$gate ->image != '' ? asset($gate->image) : '#'}}" alt="">
                        @endif
                        <span class="name">{{$gate->name}}</span>
                    </div>
                </td>
                <td>{{$gate->type}}</td>
                <td>
                    <span class="badge badge-pill {{$gate->active==1 ? 'badge-primary ': 'badge-danger'}}">
                        {{$gate->getActive()}}
                    </span>
                </td>
                <td>
                    <ul class="actions">
                        <li>
                            <a href="{{route('admin.gateway.edit',$gate->id)}}" class="show">
                                <i class="fad fa-edit"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="@if($gate->active==1) cancel @else success @endif"
                               data-toggle="modal" data-target="#enableModal_{{$index}}">
                                <i class="fad @if($gate->active==1) fa-times @else fa-check-double @endif"></i>
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{$gateway->appends(request()->query())->links()}}
    </div>
    @foreach ($gateway as $index=>$gate)
        <!-- Modal -->
        <div class="modal fade mini-modal" id="enableModal_{{$index}}" tabindex="-1" role="dialog" aria-labelledby="verticalModalTitle_{{$index}}"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                            @if ($gate->active==0)
                                @lang('site.enable') @lang('site.gateway')
                            @else
                                @lang('site.disable') @lang('site.gateway')
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
                            @if ($gate->active==0)
                                @lang('site.enable_confirm') @lang('site.gateway') @lang('site.question_mark')
                            @else
                                @lang('site.disable_confirm') @lang('site.gateway') @lang('site.question_mark')
                            @endif
                        </p>
                        <form action="{{route('admin.gateway.changeStatus',$gate->id)}}" method="post">
                            @csrf
                            @method('post')
                            <div class="d-flex justify-content-center mt-3 gap-15">
                                <button type="submit" class="btn @if($gate->active==0) btn-navy @else btn-danger @endif"
                                        title="@if($gate->active==0)@lang('site.enable')@else @lang('site.disable')@endif">
                                    <i class="fa @if($gate->active==0) fa-check @else fa-remove @endif"></i>
                                    @if($gate->active==0)@lang('site.enable')@else
                                        @lang('site.disable')
                                    @endif
                                </button>
                                <button type="submit" class="btn btn-transparent navy" title="@lang('site.cancel')" data-dismiss="modal"
                                        aria-label="Close">
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
                var url = @json(route('admin.gateway.destroy-selected')) + '?ids=' + selected.join(',');
                window.location = url;
            }
        });
    </script>
@endsection
