@extends('layouts.admin.app')
@section('title',' | ' . __('site.Articles Categories'))
@section('styles')
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')

    <div class="flex-space mb-4 dash-head">
        <h2 class="section-title mb-0">@lang('site.Articles Categories')</h2>
        <div class="head-btns mb-0">
            <span id="checks-count" class="onchange-visible"></span>
            @if(auth()->user()->hasPermission('articlecategories_export'))
                <a class="btn btn-transparent navy" href="{{route('admin.article_categories.export')}}"
                   title="@lang('site.export') @lang('site.articles')">
                    @lang('site.export')
                </a>
            @endif
            <a href="" class="btn btn-danger onchange-visible">Delete</a>
            @if(auth()->user()->hasPermission('articlecategories_create'))
                <a class="btn btn-navy onchange-hidden" href="{{route('admin.article_categories.create')}}"
                   title="@lang('site.Add New')">
                    @lang('site.Add New')
                </a>
            @endif
        </div>
    </div>
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
                <th>@lang('site.name_en')</th>
                <th>@lang('site.name_ar')</th>
                <th>@lang('site.status')</th>
                <th>@lang('site.actions')</th>
            </tr>
            </thead>
            <tbody>
            @foreach($categories as $index=> $categorie)
                <tr>
                    <td></td>
                    <td>{{$index+1}}</td>
                    <td>{{$categorie->category_en}}</td>
                    <td class="text-arabic">{{$categorie->category_ar}}</td>
                    <td>
                    <span class="badge badge-pill {{$categorie->active==1 ? 'badge-primary ': 'badge-danger'}}">
                        {{$categorie->getActive()}}
                    </span>
                    </td>
                    <td>
                        <ul class="actions">
                            @if(auth()->user()->hasPermission('articlecategories_update'))
                                <li>
                                    <a href="{{route('admin.article_categories.edit',$categorie->id)}}" class="show" title="@lang('site.edit')">
                                        <i class="fad fa-edit"></i>
                                    </a>
                                </li>
                            @endif
                            @if(auth()->user()->hasPermission('articlecategories_disable') || auth()->user()->hasPermission('articlecategories_enable'))
                                @if($categorie->active==1)
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
        <div class="d-flex justify-content-start mt-1">
            {{$categories->appends(request()->query())->links()}}
        </div>

        @foreach ($categories as $index => $categor)
            <!-- Modal -->
            <div class="modal fade mini-modal" id="enableModal_{{$index}}" tabindex="-1" role="dialog"
                 aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                                @if($categor->active==0)
                                    @lang('site.enable') @lang('site.Article Category')
                                @else
                                    @lang('site.disable') @lang('site.Article Category')
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
                                @if ($categor->active==0)
                                    @lang('site.enable_confirm') @lang('site.Article Category') @lang('site.question_mark')
                                @else
                                    @lang('site.disable_confirm') @lang('site.Article Category') @lang('site.question_mark')
                                @endif
                            </p>
                            <form action="{{route('admin.article_categories.changeStatus',$categor->id)}}" method="post">@csrf
                                @method('post')
                                <div class="d-flex justify-content-center mt-3 gap-15">
                                    <button type="submit" class="btn shadow-none @if($categor->active==0) btn-navy @else btn-danger @endif"
                                            title="@if($categor->active==0)@lang('site.enable')@else @lang('site.disable')@endif">
                                            <i class="fa @if($categor->active==0) fa-check @else fa-remove @endif"></i>

                                        @if($categor->active==0)
                                            @lang('site.enable')
                                        @else
                                            @lang('site.disable')
                                        @endif
                                    </button>
                                    <button type="submit" class="btn shadow-none btn-transparent navy" title="@lang('site.cancel')"
                                            data-dismiss="modal" aria-label="Close">
                                            <i class="fas fa-remove"></i>

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
    <script src='{{asset('assets/js/dataTables-init.js')}}'></script>
@endsection
