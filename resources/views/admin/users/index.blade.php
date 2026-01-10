@extends('layouts.admin.app')
@section('title',' | ' .  __('site.users'))
@section('styles')
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
    <!-- IntlTelInput -->
    <link rel="stylesheet" href="{{asset('assets/css/flag-icon-css/flag-icon.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/css/intlTelInput.css" rel="stylesheet">
@endsection
@section('content')
    <div class="flex-space mb-4 dash-head">
        <h2 class="section-title mb-0">@lang('site.users')</h2>
        <div class="head-btns mb-0">
            <span id="checks-count" class="onchange-visible"></span>
            @if(auth()->user()->hasPermission('users_export'))
            <a href="{{route('admin.users.export')}}" class="btn btn-transparent navy"
               title="@lang('site.export') @lang('site.users')">
                @lang('site.export')
            </a>
            @endif
            @if(auth()->user()->hasPermission('users_disable'))
            <a href="#" id="bulk-delete" class="btn btn-danger onchange-visible">@lang('site.delete_selected')</a>
            @endif
            @if(auth()->user()->hasPermission('users_create'))
            <a href="{{route('admin.users.create')}}" class="btn btn-navy onchange-hidden"
               title="@lang('site.add') @lang('site.user')">
                @lang('site.add') @lang('site.user')
            </a>
            @endif
        </div>
    </div>
    <form action="{{route('admin.users.index')}}" method="GET">
        <div class="row search-by-group mb-5">
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <div class="input-group mb-0">
                    <label for="" class="fe-14">@lang('site.name')</label>
                    <div class="input-group">
                        <input class="fe-14" type="text" name="name" id="name"
                               value="{{request()->name}}" placeholder="@lang('site.name')">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <div class="input-group">
                    <label for="intl_phone" class="fe-14">@lang('site.phone')</label>
                    <div class="international-phone gray">
                        <div class="position-relative">
                            <input type="tel" id="intl_phone" class="form-control phone"
                                   placeholder="@lang('site.phone')" value="{{request()->phone}}">
                            <img src="{{asset('assets/images/svgs/perm-phone.svg')}}" alt="" class="icon">
                        </div>
                    </div>
                    <input type="hidden" name="phone" id="phone">
                </div>
            </div>
            <div class="form-group col-lg-4 col-md-6 col-12 mb-3">
                <div class="select-group mb-0">
                    <label for="simple-select2" class="fe-14">@lang('site.status')</label>
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
                <th>@lang('site.name')</th>
                <th>@lang('site.phone')</th>
                <th>@lang('site.type')</th>
                <th>@lang('site.status')</th>
                <th>@lang('site.edit')</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($users as $index=>$user)
            <tr>
                <td>{{$user->id}}</td>
                <td>{{$index + 1}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->phone}}</td>
                @php
                    $userTypeKey = $user->type;
                    $userTypeLabel = $userTypeKey ? __('site.' . $userTypeKey) : '-';
                    if ($userTypeKey && $userTypeLabel === 'site.' . $userTypeKey) {
                        $userTypeLabel = $userTypeKey;
                    }
                @endphp
                <td>{{ $userTypeLabel }}</td>
                <td>
                    <span class="badge badge-pill {{$user->active==1 ? 'badge-primary ': 'badge-danger'}}">{{$user->getActive()}}</span>
                </td>
                <td>
                    <ul class="actions">
                        @if(auth()->user()->hasPermission('users_update'))
                        <li>
                            <a href="{{route('admin.users.edit',$user->id)}}" class="show">
                                <i class="fad fa-edit"></i>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasPermission('users_enable') || auth()->user()->hasPermission('users_disable'))
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
        <div class="modal fade alert-modal" id="enableModal_{{$index}}" tabindex="-1" role="dialog"
             aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                            @if($user->active==0)
                                @lang('site.enable') @lang('site.user')
                            @else
                                @lang('site.disable') @lang('site.user')
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
                                @lang('site.enable_confirm') @lang('site.user') @lang('site.question_mark')
                            @else
                                @lang('site.disable_confirm') @lang('site.user') @lang('site.question_mark')
                            @endif
                        </p>
                        <form action="{{route('admin.users.changeStatus',$user->id)}}" method="post">
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
    <script src='{{ asset('assets/js/dataTables-init.js') }}?v={{ @filemtime(public_path('assets/js/dataTables-init.js')) }}'></script>
    <!-- IntlTelInput -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.7/js/intlTelInput.js"></script>
    <script>
        $(document).ready(function(){
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
                    var url = @json(route('admin.users.destroy-selected')) + '?ids=' + selected.join(',');
                    window.location = url;
                }
            });
            let country_codes= <?php echo json_encode( \App\Models\Country::select('country_code')->get()); ?>;
            let countries=[];
            for(var i=0;i<country_codes.length;i++){
                countries.push(country_codes[i].country_code);
            }
            $(".phone").intlTelInput({
                rtl: true,
                initialCountry: "eg",
                autoHideDialCode:false,
                allowDropdown:false,
                nationalMode: true,
                numberType: "MOBILE",
                onlyCountries:countries,// ['us', 'gb', 'ch', 'ca', 'do'],
                preferredCountries:['eg','sa','ue'],// ['sa', 'ae', 'qa','om','bh','kw','ma'],
                preventInvalidNumbers: true,
                separateDialCode: true ,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/js/utils.js",
            });

            $(".phone").on('change',function(e){
                e.preventDefault();
                $('#phone').val($(".phone").intlTelInput("getNumber"));
            });
            $(".intl-tel-input .country-list").on('click',function(e){
                e.preventDefault();
                $('#phone').val($(".phone").intlTelInput("getNumber"));
            });

            $(".intl-tel-input.allow-dropdown .flag-container").on('click',function(e){
                // e.stopPropagation();
                $(this).toggleClass("dropdown-opened");
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.intl-tel-input.allow-dropdown .flag-container').length) {
                    $('.intl-tel-input.allow-dropdown .flag-container').removeClass('dropdown-opened');
                }
            });
        });
    </script>
@endsection
