@extends('layouts.admin.app')
@section('title',' | ' . __('site.add').' '. __('site.the_gateway'))
@section('styles')
    <!-- Uppy Dropzone -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/uppy.min.css')}}">
@endsection
@section('content')
<h2 class="section-title mb-5">@lang('site.add') @lang('site.the_gateway')</h2>
<form action="{{route('admin.gateway.store')}}" method="post" enctype="multipart/form-data">
    @csrf
    @method('post')
    <div class="row">
        <div class="col-xl-6 col-lg-8 co-12">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="dropzone-field">
                        <label for="">@lang('site.image')</label>
                        <input type="file" class="" name="image" id="image">
                        <div class="drag-drop-area" id="drag-drop-area"></div>
                    </div>
                    @error('image')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
            </div>
            <div class="input-group">
                <label for="name">@lang('site.name')</label>
                <input type="text" name="name" id="name" placeholder="@lang('site.name')"
                       value="{{old('name')}}">
                @error('name')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="select-group">
                <label for="simple-select2" class="">Type</label>
                <select class="form-control select2" name="type" id="type">
                    <option value="" selected disabled>@lang('site.choose_type')</option>
                    <option value="payment" {{old('type')=='payment' ? 'selected' : '' }}>
                        @lang('site.the_payment')
                    </option>
                    <option value="sms" {{old('type')=='sms' ? 'selected' : '' }}>@lang('site.sms')</option>
                    <option value="firebase" {{old('type')=='firebase' ? 'selected' : '' }}>@lang('site.firebase')</option>
                </select>
                @error('type')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="textarea-group">
                <label for="publishable_key">@lang('site.publishable_key')</label>
                <textarea name="publishable_key" id="publishable_key"
                          placeholder="@lang('site.publishable_key')" rows="4">
                            {{old('publishable_key')}}
                        </textarea>
                @error('publishable_key')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="textarea-group">
                <label for="secret_key">@lang('site.secret_key')</label>
                <textarea name="secret_key" id="secret_key"
                          placeholder="@lang('site.secret_key')" rows="4">
                            {{old('secret_key')}}
                        </textarea>
                @error('secret_key')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="checkbox-group mb-0">
                <label for="active">@lang('site.status')</label>
                <div class="form-check">
                    <input class="form-check-input transparent" type="checkbox" id="active" name="active" checked>
                    <label class="form-check-label" for="active">@lang('site.enable')</label>
                </div>
                @error('active')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-navy min-width-170 mt-4" title="@lang('site.save')">@lang('site.save')</button>
</form>
@endsection
@section('scripts')
    <!-- Uppy Dropzone -->
    <script src="{{asset('assets/tiny/js/uppy.min.js')}}"></script>
    <!-- Uppy Dropzone Playground(Config, Options, ...ect) -->
    <script src="{{asset('assets/js/uppy-init.js')}}"></script>
@endsection
