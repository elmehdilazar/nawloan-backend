@extends('layouts.admin.app')
@section('title',' | ' . __('site.customers'))
@section('styles')
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
    <div class="flex-space flex-wrap gap-15 mb-4 dash-head">
        <h2 class="section-title mb-0">@lang('site.customers')</h2>
        <div class="head-btns mb-0">
            <span id="checks-count" class="onchange-visible"></span>
            @if(auth()->user()->hasPermission('customers_export'))
            <a href="{{route('admin.customers.export')}}" class="btn btn-transparent navy"
               title="@lang('site.export') @lang('site.customers')">
                @lang('site.export')
            </a>
            @endif
            <a href="" class="btn btn-danger onchange-visible">Delete</a>
            @if(auth()->user()->hasPermission('customers_create'))
            <a href="{{route('admin.customers.create')}}" class="btn btn-navy onchange-hidden"
               title="@lang('site.add_new_customer')">
                @lang('site.add_new_customer')
            </a>
            @endif
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <form action="{{route('admin.customers.index')}}" method="GET">
                <div class="select-group mb-0">
                    <select class="form-control select2 fe-14" id="list_name" name="list_name" onchange="this.form.submit()">
                        <option value="0" selected>@lang('site.view_all')</option>
                        @foreach($ulists as $list)
                        <option value="{{$list->id}}"  {{request()->list_name==$list->id ? 'selected' :'' }}>{{app()->getLocale()=='ar' ? $list->name_ar : $list->name_en}}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>
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
                <th>@lang('site.phone')</th>
                <th>@lang('site.orders')</th>
                <th>@lang('site.status')</th>
                <th>@lang('site.edit')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $index=>$user)
            <tr>
            <td></td>
            <td>{{$index + 1}}</td>
            <td>
                <div class="user-col">
                    <img src="{{  asset($user->userData->image ?? 'uploads/users/default.png')   }}" alt="">

                    <span class="name">{{$user->name}}</span>
                </div>
            </td>
            <td>{{$user->phone}}</td>
            <td>
                <a href="{{route('admin.orders.index',['user_id'=>$user->id])}}" class="act-btn"
                   title="@lang('site.show') @lang('site.orders')">
                    <i class="fad fa-file-check"></i>
                </a>
            </td>
            <td>
                <span class="badge badge-pill {{$user->active==1 ? 'badge-primary ': 'badge-danger'}}">{{$user->getActive()}}</span>
            </td>
            <td>
                <ul class="actions">
                @if(auth()->user()->hasPermission('customers_update'))
                    <li>
                        <a href="{{route('admin.customers.edit',$user->id)}}" class="show">
                            <i class="fad fa-edit"></i>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="#" class="show">
                        <i class="fad fa-star-half"></i>
                    </a>
                </li>
                @if(auth()->user()->hasPermission('customers_disable') || auth()->user()->hasPermission('customers_enable'))
                    @if($user->active==1)
                        <li>
                            <a href="#" class="cancel" data-toggle="modal"
                               data-target="#enableModal_{{$index}}" title="@lang('site.disable')">
                                <i class="fad fa-times"></i>
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="#" class="success" data-toggle="modal"
                               data-target="#enableModal_{{$index}}" title="@lang('site.enable')">
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
    <div class="flex-end mt-4">
        {{$users->appends(request()->query())->links()}}
    </div>
    @foreach ($users as $index=>$user)
        <!-- Modal -->
        <div class="modal fade mini-modal" id="enableModal_{{$index}}" tabindex="-1" role="dialog"
             aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                            @if($user->active==0)
                                @lang('site.enable') @lang('site.service_seeker')
                            @else
                                @lang('site.disable') @lang('site.service_seeker')
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
                            @if ($user->active==0)
                                @lang('site.enable_confirm') @lang('site.service_seeker') @lang('site.question_mark')
                            @else
                                @lang('site.disable_confirm') @lang('site.service_seeker') @lang('site.question_mark')
                            @endif
                        </p>
                        <form action="{{route('admin.customers.changeStatus',$user->id)}}" method="post">
                            @csrf
                            @method('post')
                            <div class="d-flex justify-content-center mt-3 gap-15">
                                <button type="submit" class="btn btn-danger shadow-none"
                                        title="@if($user->active==0)@lang('site.enable')@else @lang('site.disable')@endif">
                                    {{--<i class="fad @if($user->active==0) fa-check-double @else fa-times @endif"></i>--}}
                                    @if($user->active==0)
                                        @lang('site.enable')
                                    @else
                                        @lang('site.disable')
                                    @endif
                                </button>
                                <button type="submit" class="btn btn-transparent navy shadow-none"
                                        title="@lang('site.cancel')" data-dismiss="modal"
                                        aria-label="Close">
                                    {{--<i class="fas fa-remove"></i>--}}
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
    <script src='{{asset('assets/js/dataTables-init.js')}}'></script>
@endsection
