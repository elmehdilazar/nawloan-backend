@extends('layouts.admin.app')
@section('title',' | ' .  __('site.view') .' '. __('site.service_provider'))
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
            <h4>@lang('site.service_providers')</h4>
        </div>
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.index')}}">@lang('site.dashboard')</a>
                </li>
                <li class="breadcrumb-item"><a href="{{route('admin.seekers.index')}}">@lang('site.service_providers')</a></li>
                <li class="breadcrumb-item active">@lang('site.view')</li>
            </ol>
        </div>
    </div>
    <!-- row -->
    <div class="card forms-card mb-2">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h3>@lang('site.service_provider')</h3>
                <div >
                    @if(auth()->user()->hasPermission('providers_update'))
                    <a href="{{route('admin.providers.edit',$user->id)}}" class="btn btn-success btn-sm" >
                        <i class="fas fa-edit"></i>     @lang('site.edit')
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('providers_enable'))
                    @if($user->active==0)
                    <a href="{{route('admin.providers.changeStatus',$user->id)}}" class="btn btn-success btn-sm" >
                        <i class="fas fa-check"></i>     @lang('site.enable')
                    </a>
                    @endif
                    @endif
                    @if(auth()->user()->hasPermission('providers_disable'))
                    @if($user->active==1)
                    <a href="{{route('admin.providers.changeStatus',$user->id)}}" class="btn btn-danger btn-sm" >
                        <i class="fas fa-remove"></i>     @lang('site.disable')
                    </a>
                    @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6"><div class="table-responsive">
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
                                <td>@lang('site.the_'.$user->type.'')</td>
                            </tr>
                            @if(!empty($user->userData->commercial_record))
                                <tr>
                                    <td>@lang('site.commercial_record')</td>
                                    <td>{{$user->userData->commercial_record}}</td>
                                </tr>
                            @endif
                            @if(!empty($user->userData->tax_card))
                                <tr>
                                    <td>@lang('site.tax_card')</td>
                                    <td>{{$user->userData->tax_card}}</td>
                                </tr>
                            @endif
                            @if(!empty($user->userData->national_id))
                            <tr>
                                <td>@lang('site.national_id')</td>
                                <td>{{$user->userData->national_id}}</td>
                            </tr>
                            @endif
                            @if(!empty($user->userData->tack_type))
                            <tr>
                                <td>@lang('site.tack_type')</td>
                                <td>{{$user->userData->card->name}}</td>
                            </tr>
                            @endif
                            @if(!empty($user->userData->driving_license_number))
                            <tr>
                                <td>@lang('site.driving_license_number')</td>
                                <td>{{$user->userData->driving_license_number}}</td>
                            </tr>
                            @endif
                            @if(!empty($user->userData->track_license_number))
                            <tr>
                                <td>@lang('site.track_license_number')</td>
                                <td>{{$user->userData->track_license_number}}</td>
                            </tr>
                            @endif
                            @if(!empty($user->userData->track_number))
                            <tr>
                                <td>@lang('site.track_number')</td>
                                <td>{{$user->userData->track_number}}</td>
                            </tr>
                            @endif
                            <tr><td>@lang('offers_count')</td><td>{{$user->driving->count()}}</td></tr>
                            
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
    @if($user->type=='driver')
    <div class="row mb-2">
        <div class="col-md-6">
            @if(!empty($user->userData->national_id_image_f))
            <div class="card">
                <div class="card-header">
                    <h4>@lang('site.national_id_image_f')</h3>
                </div>
                <div class="card-body">
                    <img src="{{asset($user->userData->national_id_image_f)}}" class="img-thumbnail  image-preview"
                        alt="{{$user->name}}" style="width: 100%;height: 200px;">
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-6">
            @if(!empty($user->userData->national_id_image_b))
            <div class="card">
                <div class="card-header">
                    <h3>@lang('site.national_id_image_b')</h3>
                </div>
                <div class="card-body">
                    <img src="{{asset($user->userData->national_id_image_b)}}" class="img-thumbnail  image-preview"
                        alt="{{$user->name}}" style="width: 100%;height: 200px;">
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="card forms-card mb-2">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h3>@lang('site.driverCompany')</h3>
                <div>
                    @if(auth()->user()->hasPermission('providers_update'))
                    <a href="{{route('admin.providers.edit',$user->userData->company->id)}}" class="btn btn-success btn-sm">
                        <i class="fas fa-edit"></i> @lang('site.edit')
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('providers_enable'))
                    @if($user->active==0)
                    <a href="{{route('admin.providers.changeStatus',$user->userData->company->id)}}" class="btn btn-success btn-sm">
                        <i class="fas fa-check"></i> @lang('site.enable')
                    </a>
                    @endif
                    @endif
                    @if(auth()->user()->hasPermission('providers_disable'))
                    @if($user->active==1)
                    <a href="{{route('admin.providers.changeStatus',$user->userData->company->id)}}" class="btn btn-danger btn-sm">
                        <i class="fas fa-remove"></i> @lang('site.disable')
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
                                    <td>{{$user->userData->company->id}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('site.name')</td>
                                    <td>{{$user->userData->company->name}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('site.phone')</td>
                                    <td>{{$user->userData->company->phone}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('site.email')</td>
                                    <td>{{$user->userData->company->email}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('site.type')</td>
                                    <td>@lang('site.the_'.$user->userData->company->type.'')</td>
                                </tr>
                                <tr>
                                    <td>@lang('site.commercial_record')</td>
                                    <td>{{$user->userData->company->userData->commercial_record}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('site.tax_card')</td>
                                    <td>{{$user->userData->company->userData->tax_card}}</td>
                                </tr>
                                
                                <tr>
                                    <td>@lang('site.offers_count')</td><td>{{$user->userData->company->offers->count()}}</td>
                                </tr> 
                                @if(!empty($user->userData->company->bank->bank_name))
                                <tr> <td colspan="2"><h4>@lang('site.bank_info')</h4></td></tr>
                                @endif
                            @if(!empty($user->userData->company->bank->bank_name))
                            <tr>
                                <td>@lang('site.bank_name')</td>
                                <td>{{$user->userData->company->bank->bank_name}}</td>
                            </tr>
                            @endif
                            @if(!empty($user->userData->company->bank->branch_name))
                            <tr>
                                <td>@lang('site.branch_name')</td>
                                <td>{{$user->userData->company->bank->branch_name}}</td>
                            </tr>
                            @endif
                            @if(!empty($user->userData->company->bank->account_holder_name))
                            <tr>
                                <td>@lang('site.account_holder_name')</td>
                                <td>{{$user->userData->company->bank->account_holder_name}}</td>
                            </tr>
                            @endif
                            @if(!empty($user->userData->company->bank->account_number))
                            <tr>
                                <td>@lang('site.account_number')</td>
                                <td>{{$user->userData->company->bank->account_number}}</td>
                            </tr>
                            @endif
                            @if(!empty($user->userData->company->bank->soft_code))
                            <tr>
                                <td>@lang('site.soft_code')</td>
                                <td>{{$user->userData->company->bank->soft_code}}</td>
                            </tr>
                            @endif
                            
                            @if(!empty($user->userData->company->bank->iban))
                            <tr>
                                <td>@lang('site.iban')</td>
                                <td>{{$user->userData->company->bank->iban}}</td>
                            </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    @if(!empty($user->userData->company->userData->image))
                    <div class="row">
                        <img src="{{asset($user->userData->company->userData->image)}}" class="img-thumbnail  image-preview"
                            alt="{{$user->userData->company->name}}" style="width: 100%;height: 200px;">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-6">
            @if(!empty($user->userData->company->userData->commercial_record_image_f))
            <div class="card">
                <div class="card-header">
                    <h4>@lang('site.commercial_record_image_f')</h3>
                </div>
                <div class="card-body">
                    <img src="{{asset($user->userData->company->userData->commercial_record_image_f)}}"
                        class="img-thumbnail  image-preview" alt="{{$user->userData->company->name}}"
                        style="width: 100%;height: 200px;">
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-6">
            @if(!empty($user->userData->company->userData->commercial_record_image_b))
            <div class="card">
                <div class="card-header">
                    <h3>@lang('site.commercial_record_image_b')</h3>
                </div>
                <div class="card-body">
                    <img src="{{asset($user->userData->company->userData->commercial_record_image_b)}}"
                        class="img-thumbnail  image-preview" alt="{{$user->userData->company->name}}"
                        style="width: 100%;height: 200px;">
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-6">
            @if(!empty($user->userData->company->userData->tax_card_image_f))
            <div class="card"><div class="card-header">
                <h3>@lang('site.tax_card_image_f')</h3>
            </div>
                <div class="card-body">
                    <img src="{{asset($user->userData->company->userData->tax_card_image_f)}}"
                        class="img-thumbnail  image-preview" alt="{{$user->userData->company->name}}"
                        style="width: 100%;height: 200px;">
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-6">
            @if(!empty($user->userData->company->userData->tax_card_image_b))
            <div class="card"><div class="card-header">
                <h3>@lang('site.tax_card_image_b')</h3>
            </div>
                <div class="card-body">
                    <img src="{{asset($user->userData->company->userData->tax_card_image_b)}}"
                        class="img-thumbnail  image-preview" alt="{{$user->userData->company->name}}"
                        style="width: 100%;height: 200px;">
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
    @if($user->type=="driverCompany" )
    <div class="row mb-2">
        <div class="col-md-6">
            @if(!empty($user->userData->commercial_record_image_f))
            <div class="card">
                <div class="card-header">
                    <h4>@lang('site.commercial_record_image_f')</h3>
                </div>
                <div class="card-body">
                    <img src="{{asset($user->userData->commercial_record_image_f)}}"
                        class="img-thumbnail  image-preview" alt="{{$user->name}}"
                        style="width: 100%;height: 200px;">
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
                    <img src="{{asset($user->userData->commercial_record_image_b)}}"
                        class="img-thumbnail  image-preview" alt="{{$user->name}}"
                        style="width: 100%;height: 200px;">
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
                    <img src="{{asset($user->userData->tax_card_image_f)}}"
                        class="img-thumbnail  image-preview" alt="{{$user->name}}"
                        style="width: 100%;height: 200px;">
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
                    <img src="{{asset($user->userData->tax_card_image_b)}}"
                        class="img-thumbnail  image-preview" alt="{{$user->name}}"
                        style="width: 100%;height: 200px;">
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3>@lang('site.drivers')</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table secondary-table-bordered table-bordered">
                    <thead class="thead-secondary">
                        <th>#</th>
                        <th>@lang('site.name')</th>
                        <th>@lang('site.phone')</th>
                        <th>@lang('site.email')</th>
                        <td>@lang('site.national_id')</td>
                        <td>@lang('site.track_type')</td>
                        <td>@lang('site.driving_license_number')</td>
                        <td>@lang('site.track_license_number')</td>
                        <td>@lang('site.track_number')</td>
                    </thead>
                    <tbody>
                        @foreach ($user->drivers as  $index=>$driver)
                        <tr>
                            <td>{{ $index + 1}}</td>
                            <td><a href="{{route('admin.providers.show',$driver->user_id)}}">{{$driver->user->name}}</a></td>
                            <td>{{$driver->user->phone}}</td>
                            <td>{{$driver->user->email}}</td>
                            <td>@if(!empty($driver->national_id_image_f))<span class="badge badge-success">{{$driver->national_id}}</span>@else {{$driver->national_id}} @endif</td>
                            <td><a href="{{route('admin.trucks.index',['name'=>$driver->car->name])}}">{{$driver->car->name}}</a></td>
                            <td>{{$driver->driving_license_number}}</td>
                            <td>{{$driver->track_license_number}}</td>
                            <td>{{$driver->track_number}}</td>
                        </tr>
                        @endforeach
                    @if($user->drivers->count()==0)
                    <tr>
                        <td colspan="9" style="text-align: center !important;">@lang('site.no_records_found')</td>
                    </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@section('scripts')
    <script src="{{asset('assets/admin/plugins/jasny-bootstrap/dist/js/jasny-bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/admin/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/plugins-init/select2-init.js')}}"></script>
@endsection
