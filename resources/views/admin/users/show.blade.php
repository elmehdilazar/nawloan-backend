@extends('layouts.admin.app')
@section('title',' | ' . __('site.view') .' '. __('site.the_user'))
@section('styles')
<style>
    .row {
        text-align: start;
    }

    .select2-container {
        width: 100% !important;
    }
</style>
<link rel="stylesheet" href="{{asset('assets/admin/plugins/jasny-bootstrap/dist/css/jasny-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/admin/plugins/select2/css/select2.min.css')}}">
@endsection
@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-2">
        <div class="col p-md-0">
            <h4>@lang('site.users')</h4>
        </div>
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.index')}}">@lang('site.dashboard')</a>
                </li>
                <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">@lang('site.users')</a></li>
                <li class="breadcrumb-item active">@lang('site.view') @lang('site.user_info')</li>
            </ol>
        </div>
    </div>
    <!-- row -->
    <div class="card forms-card mb-2">
        <div class="card-header">
            <div class="d-flex justify-content-between">
            <h3>@lang('site.user_info')</h3>
                <div>
                    @if(auth()->user()->hasPermission('users_update'))
                    <a href="{{route('admin.users.edit',$user->id)}}" class="btn btn-success btn-sm"><i class="fa fa-edit"></i>     @lang('site.edit')</a>
                    @endif
                    @if($user->active==1)
                    @if(auth()->user()->hasPermission('users_disable'))
                    <a class=" btn btn-sm btn-danger " href="{{route('admin.users.changeStatus',$user->id)}}">
                        <i class="fas fa-remove "></i>      @lang('site.disable')
                    </a>@endif
                    @else
                    @if(auth()->user()->hasPermission('users_enable'))
                    <a class=" btn btn-sm btn-success " href="{{route('admin.users.changeStatus',$user->id)}}">
                        <i class="fas fa-check "></i>       @lang('site.enable')
                    </a>
                    @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>@lang('site.number')</td>
                                    <td>{{$user->id}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('site.name')</td>
                                    <td>{{$user->name}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('site.phone')</td>
                                    <td>{{$user->phone}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('site.email')</td>
                                    <td>{{$user->email}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('site.type')</td>
                                    <td>@lang('site.'.$user->type.'')</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    @if(!empty($user->userData->image))
                    <div class="row">
                        <img src="{{asset($user->userData->image)}}" class="img-thumbnail  image-preview"
                            alt="{{$user->name}}" style="width: 100%;height: 250px;">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card forms-card">
        <div class="card-header">
            <h3>@lang('site.permissions')</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table secondary-table-bordered table-bordered">
                        <thead class="thead-secondary">
                            <tr><td>@lang('site.page')</td><td>@lang('site.view')</td><td>@lang('site.add')</td><td>@lang('site.update')</td><td>@lang('site.enable')</td><td>@lang('site.disable')</td></tr></thead>
                            <tbody>
                                @php
                                $models = ['users', 'seekers','providers','cars','shipments_types','transactions','send_messages','support_center','orders','offers'];
                                $titles=    ['manage_users','service_seekers','service_providers','cars','shipments_types','transactions','send_messages','support_center','orders','offers'];
                                $maps = ['create', 'read', 'update','enable','disable'];
                                @endphp
                                @foreach ($models as $index => $model)
                                <tr>
                                    <td>@lang('site.'.$titles[$index].'')</td>
                                    @foreach ($maps as $map)
                                    <td>@if ($user->hasPermission($model.'_'.$map))
                                        <span class="text-success-dark">@lang('site.yes') </span>
                                        @else <span class="text-danger">@lang('site.no')</span> @endif</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

</div>

@endsection

@section('scripts')
<script src="{{asset('assets/admin/plugins/jasny-bootstrap/dist/js/jasny-bootstrap.min.js')}}"></script>
<script src="{{asset('assets/admin/plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('assets/admin/js/plugins-init/select2-init.js')}}"></script>
@endsection
