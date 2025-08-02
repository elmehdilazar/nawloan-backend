@extends('layouts.admin.app')
@section('title',' | ' .  __('site.view') .' '. __('site.service_seeker'))
@section('styles')
<style>
    .row{text-align: start;}
    .select2-container{width:100% !important;}
</style>
    <link rel="stylesheet" href="{{asset('assets/admin/plugins/jasny-bootstrap/dist/css/jasny-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/plugins/select2/css/select2.min.css')}}">
@endsection
@section('content')

<div class="container-fluid">
    <div class="row page-titles mx-2">
        <div class="col p-md-0">
            <h4>@lang('site.service_seekers')</h4>
        </div>
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.index')}}">@lang('site.dashboard')</a>
                </li>
                <li class="breadcrumb-item"><a href="{{route('admin.seekers.index')}}">@lang('site.service_seekers')</a></li>
                <li class="breadcrumb-item active">@lang('site.view')</li>
            </ol>
        </div>
    </div>
    <!-- row -->
    <div class="card forms-card">
        <div class="card-header">
            <h3>@lang('site.service_seeker') @lang('site.info')</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                        <tbody>
                                <tr><td>@lang('site.number')</td><td>{{$user->id}}</td></tr>
                                <tr><td>@lang('site.name')</td><td>{{$user->name}}</td></tr>
                                <tr><td>@lang('site.phone')</td><td>{{$user->phone}}</td></tr>
                                <tr><td>@lang('site.email')</td><td>{{$user->email}}</td></tr>
                                <tr><td>@lang('site.type')</td><td>@lang('site.'.$user->type.'')</td></tr>
                                @if(!empty($user->userData->commercial_record))
                                <tr><td>@lang('site.commercial_record')</td><td>{{$user->userData->commercial_record}}</td></tr>
                                @endif
                                @if(!empty($user->userData->tax_card))
                                <tr>
                                    <td>@lang('site.tax_card')</td>
                                    <td>{{$user->userData->tax_card}}</td>
                                </tr>
                                @endif
                                <tr><td>@lang('site.orders_count')</td><td>{{$user->orders->count()}}</td></tr>
                                
                                @if(!empty($user->bank->bank_name))
                                <tr> <td colspan="2"><h4>@lang('site.bank_info')</h4></td></tr>
                                @endif
                            @if(!empty($user->bank->bank_name))
                            <tr>
                                <td>@lang('site.bank_name')</td>
                                <td>{{$user->bank->bank_name}}</td>
                            </tr>
                            @endif
                            @if(!empty($user->bank->branch_name))
                            <tr>
                                <td>@lang('site.branch_name')</td>
                                <td>{{$user->bank->branch_name}}</td>
                            </tr>
                            @endif
                            @if(!empty($user->bank->account_holder_name))
                            <tr>
                                <td>@lang('site.account_holder_name')</td>
                                <td>{{$user->bank->account_holder_name}}</td>
                            </tr>
                            @endif
                            @if(!empty($user->bank->account_number))
                            <tr>
                                <td>@lang('site.account_number')</td>
                                <td>{{$user->bank->account_number}}</td>
                            </tr>
                            @endif
                            @if(!empty($user->bank->soft_code))
                            <tr>
                                <td>@lang('site.soft_code')</td>
                                <td>{{$user->bank->soft_code}}</td>
                            </tr>
                            @endif
                            
                            @if(!empty($user->bank->iban))
                            <tr>
                                <td>@lang('site.iban')</td>
                                <td>{{$user->bank->iban}}</td>
                            </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    @if(!empty($user->userData->image))
                    <div class="row">
                    <img src="{{asset($user->userData->image)}}" class="img-thumbnail  image-preview" alt="{{$user->name}}"
                            style="width: 100%;height: 450px;">
                    </div>
                            @endif
                </div>
            </div>
        </div>
    </div>
</div>
@if($user->type=="factory" )
    <div class="row mb-2">
        <div class="col-md-6">
            @if(!empty($user->userData->commercial_record_image_f))
            <div class="card">
                <div class="card-header">
                    <h4>@lang('site.commercial_record_image_f')</h3>
                </div>
                <div class="card-body">
                    <img src="{{asset($user->userData->commercial_record_image_f)}}" class="img-thumbnail  image-preview"
                        alt="{{$user->name}}" style="width: 100%;height: 200px;">
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-6">
            @if(!empty($user->userData->commercial_record_image_b))
            <div class="card">
                <div class="card-header">
                    <h3>@lang('site.commercial_record_image_b')</h3>
                </div>
                <div class="card-body">
                    <img src="{{asset($user->userData->commercial_record_image_b)}}" class="img-thumbnail  image-preview"
                        alt="{{$user->name}}" style="width: 100%;height: 200px;">
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-6">
            @if(!empty($user->userData->tax_card_image_f))
            <div class="card">
                <div class="card-header">
                    <h3>@lang('site.tax_card_image_f')</h3>
                </div>
                <div class="card-body">
                    <img src="{{asset($user->userData->tax_card_image_f)}}" class="img-thumbnail  image-preview"
                        alt="{{$user->name}}" style="width: 100%;height: 200px;">
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-6">
            @if(!empty($user->userData->tax_card_image_b))
            <div class="card">
                <div class="card-header">
                    <h3>@lang('site.tax_card_image_b')</h3>
                </div>
                <div class="card-body">
                    <img src="{{asset($user->userData->tax_card_image_b)}}" class="img-thumbnail  image-preview"
                        alt="{{$user->name}}" style="width: 100%;height: 200px;">
                </div>
            </div>
            @endif
        </div>
    </div>
@endif

@endsection

@section('sripts')
  <script src="{{asset('assets/admin/plugins/jasny-bootstrap/dist/js/jasny-bootstrap.min.js')}}"></script>
   <script src="asset('assets/admin/plugins/select2/js/select2.full.min.js')}}"></script>
 <script src="{{asset('assets/admin/js/plugins-init/select2-init.js')}}"></script>
@endsection









