@extends('layouts.admin.app')
@section('title',' | ' . __('site.add') .' '. __('site.cars'))
@section('styles')
    <!-- Uppy Dropzone -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/uppy.min.css')}}">
@endsection
@section('content')
<h2 class="section-title mb-5">@lang('site.add') @lang('site.car')</h2>
<form action="{{route('admin.trucks.store')}}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('post')
    <div class="row">
        <div class="col-xl-6 col-lg-8 co-12">
            <div class="dropzone-field">
                <label for="">Truck picture</label>
                <input type="file" class="" name="image" id="image">
                <div class="drag-drop-area" id="drag-drop-area"></div>
                @error('image')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="name_en">@lang('site.name_en')</label>
                <input type="text" id="name_en" name="name_en"
                       placeholder="@lang('site.name_en')" value="{{old('name_en')}}" required>
                @error('name_en')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="name_ar">@lang('site.name_ar')</label>
                <input type="text" id="name_ar" name="name_ar"
                       placeholder="@lang('site.name_ar')" value="{{old('name_ar')}}" required>
                @error('name_ar')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="frames">@lang('site.axes')</label>
                <div class="position-relative">
                    <input type="number" step="1" min="4" id="frames" name="frames" required
                           placeholder="@lang('site.axes')" value="{{old('frames') !='' ? old('frames') : '4'  }}">
                    <i class="fad fa-weight"></i>
                </div>
                @error('frames')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="weight">@lang('site.weight')</label>
                <div class="position-relative">
                    <input type="number" step="0.01" min="0" id="weight" name="weight" required
                           placeholder="@lang('site.weight')" value="{{old('weight') !='' ? old('weight') : '0.00'}}">
                    <i class="fad fa-weight-hanging"></i>
                </div>
                @error('weight')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="checkbox-group">
                <label for="active">@lang('site.status')</label>
                <div class="form-check">
                    <input class="form-check-input transparent" type="checkbox" id="active" name="active" checked>
                    <label class="form-check-label" for="active">@lang('site.enable')</label>
                </div>
                @error('active')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-navy min-width-170 mt-4">@lang('site.add') @lang('site.car')</button>
        </div>
    </div>
</form>

@endsection

@section('scripts')
    <!-- Uppy Dropzone -->
    <script src="{{asset('assets/tiny/js/uppy.min.js')}}"></script>
    <!-- Uppy Dropzone Playground(Config, Options, ...ect) -->
    <script src="{{asset('assets/js/uppy-init.js')}}"></script>
@endsection
