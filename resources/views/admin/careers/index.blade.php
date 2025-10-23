@extends('layouts.admin.app')
@section('title',' | ' . __('site.careers'))
@section('styles')
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
    <div class="flex-space mb-4 dash-head">
        <h2 class="section-title mb-0">@lang('site.careers')</h2>
        <div class="head-btns mb-0">
            <span id="checks-count" class="onchange-visible"></span>
            @if(auth()->user()->hasPermission('careers_export'))
                <a class="btn btn-transparent navy" href="{{route('admin.careers.export')}}"
                   title="@lang('site.export') @lang('site.careers')">
                    @lang('site.export')
                </a>
            @endif
            <a class="btn btn-transparent navy" href="{{route('admin.career_categories.index')}}"
               title="@lang('site.careers') @lang('site.categories')">
                @lang('site.categories')
            </a>
            @if(auth()->user()->hasPermission('careers_disable'))
                <a href="#" id="bulk-delete" class="btn btn-danger onchange-visible">@lang('site.delete_selected')</a>
            @endif
            @if(auth()->user()->hasPermission('careers_create'))
                <a class="btn btn-navy onchange-hidden" href="{{route('admin.careers.create')}}"
                   title="@lang('site.create_career')">
                    @lang('site.create_career')
                </a>
            @endif
        </div>
    </div>
    <form action="{{route('admin.careers.index')}}" method="GET">
        <div class="row search-by-group mb-5">
            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
                <div class="input-group mb-0">
                    <label for="name_en" class="fe-14">@lang('site.name_en')</label>
                    <div class="input-group">
                        <input type="text" class="fe-14" name="name_en" id="name_en" value="{{request()->name_en}}"
                               placeholder="@lang('site.name_en')">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
                <div class="input-group mb-0">
                    <label for="name_ar" class="fe-14">@lang('site.name_ar')</label>
                    <div class="input-group">
                        <input class="fe-14" type="text" name="name_ar" id="name_ar" value="{{request()->name_ar}}"
                               placeholder="@lang('site.name_ar')">
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
                    <label for="list_name" class="fe-14">@lang('site.track_type')</label>
                    @if(app()->getLocale()=='ar')
                        <select class="form-control select2 fe-14" id="category" name="category_id">
                            <option value="" selected >@lang("site.category")</option>
                            @foreach ($categories as $index => $category )
                                <option value=" {{$category->id}} " {{ request()->category_id== $category->id ? 'selected' : '' }}>{{$category->category_ar}}</option>
                            @endforeach
                        </select>
                    @else
                        <select class="form-control select2 fe-14" id="category" name="category_id">
                            <option value="" selected >@lang("site.choose_category")</option>
                            @foreach ($categories as $index => $category )
                                <option value="{{$category->id}}" {{ request()->category_id== $category->id ? 'selected' : '' }}> {{$category->category_en}}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
            <div class="form-group col-xl-3 col-lg-4 col-md-6 col-12 mb-3">
                <div class="select-group mb-0">
                    <label for="active" class="fe-14">@lang('site.status')</label>
                    <select class="form-control select2 no-search fe-14" id="active" name="active">
                        <option value="2" selected>@lang('site.view_all')</option>
                        <option value="1" {{ request()->active== '1' ? 'selected' : '' }}>
                            @lang('site.active')
                        </option>
                        <option value="0" {{ request()->active== '0' ? 'selected' : '' }}>
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
                <th>@lang('site.career_ar')</th>
                <th>@lang('site.career_en')</th>
                <th>@lang('site.category')</th>
                <th>@lang('site.date')</th>
                <th>@lang('site.status')</th>
                <th>@lang('site.edit')</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($careers as $index=>$career)
            <tr>
                <td>{{$career->id}}</td>
                <td>{{$career->id}}</td>
                <td>{{$career->name_ar}}</td>
                <td>{{$career->name_en}}</td>
                @if ($career->category == null)
                    <td style="color: red;font-size: 10px">@lang('site.The Category Inactive')</td>
                @elseif(app()->getLocale()=='ar')
                    <td>{{@$career->category->category_ar}}</td>
                @elseif(app()->getLocale()=='en')
                    <td>{{@$career->category->category_en}}</td>
                @endif

                <td>{{$career->created_at->format('Y-m-d')}}</td>
                <td>
                    <span class="badge badge-pill {{$career->active==1 ? 'badge-primary ': 'badge-danger'}}">
                        @if ($career->active==1 ) @lang('site.published') @else @lang('site.unpublished') @endif
                    </span>
                </td>
                <td>
                    <ul class="actions">
                        @if(auth()->user()->hasPermission('careers_update'))
                            <li>
                                <a href="{{route('admin.careers.edit',$career->id)}}" class="show" title="@lang('site.edit')">
                                    <i class="fad fa-edit"></i>
                                </a>
                            </li>
                        @endif
                        @if(auth()->user()->hasPermission('careers_disable') || auth()->user()->hasPermission('careers_enable'))
                            @if($career->active==1)
                                <li>
                                    <a class="cancel" href="#" data-toggle="modal" data-target="#enableModal_{{$index}}" title="@lang('site.unpublished')">
                                        <i class="fad fa-times"></i>
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="success" href="#" data-toggle="modal" data-target="#enableModal_{{$index}}" title="@lang('site.published')">
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
    @foreach ($careers as $index=>$career)
        <div class="modal fade mini-modal" id="enableModal_{{$index}}" tabindex="-1" role="dialog"
             aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                            @if($career->active==0)
                                @lang('site.published') @lang('site.career')
                            @else
                                @lang('site.disable') @lang('site.career')
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
                            @if ($career->active==0)
                                @if(app()->getLocale()=='en') @lang('site.enable_career')
                                    {{$career->name_en}} @lang('site.question_mark')
                                @else @lang('site.enable_career') {{$career->name_ar}}
                                    @lang('site.question_mark')
                                @endif
                            @else
                                @if(app()->getLocale()=='en') @lang('site.disable_career')
                                    {{$career->name_en}} @lang('site.question_mark')
                                @else
                                    @lang('site.disable_career') {{$career->name_ar}} @lang('site.question_mark')
                                @endif
                            @endif
                        </p>
                        <form action="{{route('admin.careers.changeStatus',$career->id)}}" method="post">
                            @csrf
                            @method('post')
                            <div class="d-flex justify-content-center mt-3 gap-15">
                                <button type="submit" class="btn shadow-none @if($career->active==0) btn-navy @else btn-danger @endif"
                                        title="@if($career->active==0)@lang('site.enable')@else @lang('site.disable')@endif">
                                    {{--<i class="fa @if($truck->active==0) fa-check @else fa-remove @endif"></i>--}}
                                    @if($career->active==0)
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
                var url = @json(route('admin.careers.destroy-selected')) + '?ids=' + selected.join(',');
                window.location = url;
            }
        });
    </script>
@endsection
