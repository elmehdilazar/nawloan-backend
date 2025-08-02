@extends('layouts.admin.app')
@section('title',' | ' . __('site.add') .' '. __('site.country_code'))
@section('styles')
    <!-- Uppy Dropzone -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/uppy.min.css')}}">
@endsection
@section('content')
    <h2 class="section-title mb-5">@lang('site.add') @lang('site.country_code')</h2>
    <form action="{{route('admin.countries.store')}}" method="POST" enctype="multipart/form-data">
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
                    <labe for="phone_code">@lang('site.phone_code')</labe>
                    <div class="input-group">
                        <input type="text" id="phone_code" name="phone_code"
                               placeholder="@lang('site.phone_code')"
                               value="{{ old('phone_code')}}" required>
                    </div>
                    @error('phone_code')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="name">@lang('site.name')</label>
                    <input type="text" id="name" name="name" placeholder="@lang('site.name')"
                           value="{{old('name')}}" required>
                    @error('name')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="country_code">@lang('site.country_code')</label>
                    <div class="input-group">
                        <input type="text" id="country_code" name="country_code" placeholder="@lang('site.country_code')"
                               value="{{old('country_code')}}" required>
                    </div>
                    @error('country_code')
                    <span class="text-danger">{{$message}}</span>
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
        <button type="submit" class="btn btn-navy min-width-170 mt-4">@lang('site.save')</button>
    </form>
@endsection

@section('scripts')
    <!-- Uppy Dropzone -->
    <script src="{{asset('assets/tiny/js/uppy.min.js')}}"></script>
    <!-- Uppy Dropzone Playground(Config, Options, ...ect) -->
    <script src="{{asset('assets/js/uppy-init.js')}}"></script>
@endsection
