@extends('layouts.admin.app')
@section('title',' | ' . __('site.career_categories'))
@section('styles')
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
    <div class="flex-space mb-4 dash-head">
        <h2 class="section-title mb-0">@lang('site.career_categories')</h2>
        <div class="head-btns mb-0">
            <span id="checks-count" class="onchange-visible"></span>
            @if(auth()->user()->hasPermission('careercategories_export'))
            <a class="btn btn-transparent navy" href="#"
               title="@lang('site.export') @lang('site.career_categories')">
                @lang('site.export')
            </a>
            @endif
            @if(auth()->user()->hasPermission('careercategories_disable'))
                <a href="#" id="bulk-delete" class="btn btn-danger onchange-visible">@lang('site.delete_selected')</a>
            @endif
            @if(auth()->user()->hasPermission('careercategories_create'))
                <a class="btn btn-navy onchange-hidden" href="{{route('admin.career_categories.create')}}"
                   title="@lang('site.create_career_category')">
                    @lang('site.create_career_category')
                </a>
            @endif
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
            <th>@lang('site.category_ar')</th>
            <th>@lang('site.category_en')</th>
            <th>@lang('site.status')</th>
            <th>@lang('site.edit')</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($categories as $index=>$category)
            <tr>
                <td>{{$category->id}}</td>
                <td>{{$category->id}}</td>
                <td>{{$category->category_ar}}</td>
                <td>{{$category->category_en}}</td>
                <td>
                    <span class="badge badge-pill {{$category->deleted_at!=null ? 'badge-danger ': 'badge-primary'}}">
                        @if($category->deleted_at!=null) Inactive @else Active @endif
                        {{--{{$category->getActive()}}--}}
                    </span>
                </td>
                <td>
                    <ul class="actions">
                        @if(auth()->user()->hasPermission('careercategories_update'))
                            <li>
                                <a href="{{route('admin.career_categories.edit',$category->id)}}" class="show" title="@lang('site.edit')">
                                    <i class="fad fa-edit"></i>
                                </a>
                            </li>
                        @endif
                        @if(auth()->user()->hasPermission('careercategories_disable') || auth()->user()->hasPermission('careercategories_enable'))
                            @if($category->deleted_at!=null)
                                <li>
                                    <a class="success" href="#" data-toggle="modal" data-target="#enableModal_{{$index}}" title="@lang('site.published')">
                                        <i class="fad fa-check-double"></i>
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="cancel" href="#" data-toggle="modal" data-target="#enableModal_{{$index}}" title="@lang('site.unpublished')">
                                        <i class="fad fa-times"></i>
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
        {{$categories->appends(request()->query())->links()}}
    </div>
    @foreach ($categories as $index=>$category)
        <div class="modal fade mini-modal" id="enableModal_{{$index}}" tabindex="-1" role="dialog"
             aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                            @if($category->deleted_at!=null)
                                @lang('site.enable') @lang('site.category')
                            @else
                                @lang('site.disable') @lang('site.category')
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
                            @if ($category->deleted_at==null)
                                @if(app()->getLocale()=='en') @lang('site.disable') {{$category->category_en}} @lang('site.question_mark')
                                @else @lang('site.disable') {{$category->category_ar}} @lang('site.question_mark')@endif
                            @else
                                @if(app()->getLocale()=='en') @lang('site.enable') {{$category->category_en}} @lang('site.question_mark')
                                @else @lang('site.enable') {{$category->category_ar}} @lang('site.question_mark')@endif
                            @endif
                        </p>
                        @if ($category->deleted_at==null)
                        <form action="{{route('admin.career_categories.destroy',$category->id)}}" method="post">
                            @csrf
                            @method('delete')
                        @else
                        <form action="{{route('admin.career_categories.restore',$category->id)}}" method="post">
                            @csrf
                            @method('put')
                        @endif
                            <div class="d-flex justify-content-center mt-3 gap-15">
                                <button type="submit" class="btn shadow-none @if($category->deleted_at==null) btn-danger @else btn-navy @endif"
                                        title="@if($category->deleted_at==null)@lang('site.disable')@else @lang('site.enable')@endif">
                                    {{--<i class="fa @if($truck->active==0) fa-check @else fa-remove @endif"></i>--}}
                                    @if($category->deleted_at!=null)
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
                var url = @json(route('admin.career_categories.destroy-selected')) + '?ids=' + selected.join(',');
                window.location = url;
            }
        });
    </script>
@endsection
