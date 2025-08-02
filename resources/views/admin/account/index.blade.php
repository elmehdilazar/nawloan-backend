@extends('layouts.admin.app')
@section('title',' | ' .  __('site.account'))
@section('content')
    <h2 class="section-title mb-4">@lang('site.account')</h2>
    <div class="row justify-content-center mb-2">
        <div class="col-md-4">
            @if(!empty($user->userData->image))
            <img src="{{  asset($user->userData->image) }}" class="img-thumbnail image-preview" alt="{{$user->name}}">
            @else
            <img src="{{ asset('uploads/img/logo.png') }}" class="img-thumbnail image-preview" alt="{{$user->name}}">
            @endif
        </div>
        <div class="col-md-8">
        <div class="table-responsive ">
            <table class="table table-hover">
                <thead style="display: none"></thead>
                <tbody>
                    <tr><td>@lang('site.name')</td> <td>{{$user->name}}</td></tr>
                    <tr><td>@lang('site.phone')</td> <td>{{$user->phone}}</td></tr>
                    <tr><td>@lang('site.type')</td> <td>{{ $user->type}}</td></tr>
                    <tr><td>@lang('site.status')</td> <td>{{$user->getActive()}}</td></tr>
                </tbody>
            </table>
        </div>
            <div class="flex-end gap-15">
                <a href="{{route('admin.account.edit')}}" class="btn btn-transparent navy shadow-none" title="@lang('site.account_edit')">
                    @lang('site.account_edit')
                </a>
                <a href="{{route('admin.account.edit.password')}}" class="btn btn-navy shadow-none">
                   @lang('site.reset_password')
                </a>
            </div>
        </div>
</div>
@endsection
