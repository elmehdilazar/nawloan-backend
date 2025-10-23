@extends('layouts.admin.app')
@section('title',' | ' . __('site.coupons'))
@section('styles')
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
    <div class="flex-space mb-4 dash-head">
        <h2 class="section-title mb-0">@lang('site.articles')</h2>
        <div class="head-btns mb-0">
            @if(auth()->user()->hasPermission('articlecategories_read'))
            <a href="{{route('admin.article_categories.index')}}" class="btn btn-transparent navy">@lang('site.categories')</a>
            @endif
            <a href="" class="btn btn-danger onchange-visible">Delete</a>
            @if(auth()->user()->hasPermission('articles_create'))
                <a class="btn btn-navy onchange-hidden" href="{{route('admin.articles.create')}}"
                   title="@lang('site.create_coupons')">
                    @lang('site.Add New Article')
                </a>
            @endif
        </div>
    </div>
    {{--<div class="row mb-5">
        <div class="col-xl-4 col-lg-6 col-md-6 col-12 mb-md-0 mb-3">
            <div class="input-group mb-0">
                <div id="reportrange" class="angle-down">
                    <i class="fad fa-calendar-alt"></i>
                    <span></span>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
            <div class="select-group mb-0">
                <select class="form-control select2 no-search fe-14" id="simple-select4">
                    @foreach($article_category as $article_category)
                        <option value="{{$article_category->id}}" selected >{{$article_category->category_en}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>--}}
    <div class="table-responsive">
        <table class="table datatables" id="dataTable-2" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>
                    <div class="dt-checkbox">
                        <input type="checkbox" name="select_all" value="1" id="selectAll">
                        <label for="selectAll" class="visual-checkbox"></label>
                    </div>
                </th>
                <th>@lang('site.num')</th>
                <th>@lang('site.Article Name')</th>
                <th>@lang('site.category')</th>
                <th>@lang('site.date')</th>
                <th>@lang('site.status')</th>
                <th>@lang('site.actions')</th>
            </tr>
            </thead>
            <tbody>

            @foreach($articles as $index => $article)
                <tr>
                    <td></td>
                    <td>{{$index+1}}</td>
                    <td>
                        <p>{{$article->article_en}}</p>
                        {{--<div class="flex-align-center gap-20 flex-wrap mt-2">
                            <a href="" class="text-navy">View</a>
                            <a href="" class="text-navy">Unpublish</a>
                            <a href="" class="text-red">Trash</a>
                        </div>--}}
                    </td>
                    <td>{{$article->category->category_en}}</td>
                    <td>{{$article->article_date}}</td>
                    <td>
                         <span class="badge badge-pill {{$article->active==1 ? 'badge-primary ': 'badge-danger'}}">
                        {{$article->getActive()}}
                    </span>
                    </td>
                    <td>
                        <ul class="actions">
                            @if(auth()->user()->hasPermission('articles_update'))
                                <li>
                                    <a href="{{route('admin.articles.edit',$article->id)}}" class="show" title="@lang('site.edit')">
                                        <i class="fad fa-edit"></i>
                                    </a>
                                </li>
                            @endif
                                @if(auth()->user()->hasPermission('articles_disable') || auth()->user()->hasPermission('articles_enable'))
                                    @if($article->active==1)
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
            {{$articles->appends(request()->query())->links()}}
        </div>

        @foreach ($articles as $index => $articl)

            <!-- Modal -->
            <div class="modal fade mini-modal" id="enableModal_{{$index}}" tabindex="-1" role="dialog"
                 aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                         <div class="modal-header">
                            <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                                @if($articl->active==0)
                                    @lang('site.enable') @lang('site.articles')
                                @else
                                    @lang('site.disable') @lang('site.articles')
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
                                @if ($articl->active==0)
                                    @lang('site.enable_confirm') @lang('site.articles') @lang('site.question_mark')
                                @else
                                    @lang('site.disable_confirm') @lang('site.articles') @lang('site.question_mark')
                                @endif
                            </p>
                            <form action="{{route('admin.articles.changeStatus',$articl->id)}}" method="post">@csrf
                                @method('post')
                                <div class="d-flex justify-content-center mt-3 gap-15">
                                    <button type="submit" class="btn shadow-none @if($articl->active==0) btn-navy @else btn-danger @endif"
                                            title="@if($articl->active==0)@lang('site.enable')@else @lang('site.disable')@endif">
                                        <i class="fa @if($articl->active==0) fa-check @else fa-remove @endif"></i>

                                        @if($articl->active==0)
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
