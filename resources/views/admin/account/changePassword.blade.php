@extends('layouts.admin.app')
@section('title',' | ' .  __('site.reset_password'))
@section('content')
<h2 class="section-title mb-5">@lang('site.reset_password')</h2>
<div class="row">
    <div class="col-xl-6 col-lg-8 co-12">
        <form method="POST" action="{{route('admin.account.change.password')}}" >
            @csrf
            @method('post')
            <div class="input-group mb-5">
                <label for=""> @lang('site.current_password')</label>
                <div class="position-relative">
                    <i class="fad fa-user-lock"></i>
                    <input id="password" type="password" name="current_password"
                           autocomplete="current-password" placeholder="@lang('site.current_password')">
                </div>
            </div>
            <div class="input-group">
                <label for="">@lang('site.new_password')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/lock.svg')}}" alt="">
                    <input id="new_password" type="password" name="new_password"
                           autocomplete="current-password" placeholder="@lang('site.new_password')">
                </div>
            </div>
            <div class="input-group">
                <label for="">@lang('site.new_con_password')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/lock.svg')}}" alt="">
                    <input id="new_confirm_password" type="password" name="new_confirm_password"
                           autocomplete="current-password" placeholder="@lang('site.new_con_password')">
                </div>
            </div>
            <button type="submit" class="btn btn-navy shadow-none min-width-170 mt-4" title="@lang('site.edit') @lang('site.password')">
                @lang('site.reset_password')
            </button>
        </form>
    </div>
</div>
@endsection
