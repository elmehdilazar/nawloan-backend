@extends('layouts.admin.app')
@section('title',' | ' . __('site.edit') .' '. __('site.cars'))
@section('content')
<h2 class="section-title mb-5">@lang('site.edit') @lang('site.car')</h2>
<form action="{{route('admin.trucks.update',$truck->id)}}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('put')
    <div class="row">
        <div class="col-xl-6 col-lg-8 co-12">
            <div class="image-input wide">
                <label for="">Truck picture</label>
                <div class="imageUpload-wrapper"
                     style="background-image: url({{asset('assets/images/no-image.jpg')}})">
                    <div id="imageUpload" class="contain"
                         style="background-image: url({{$truck->image!='' ? asset($truck->image) :asset('uploads/img/logo.png')}})">
                        <input type="file" name="image" id="image" class="mediaFile">
                        <label for="image"><i class="fad fa-pencil"></i></label>
                        <a href="#" id="clear-input"><i class="fal fa-times"></i></a>
                    </div>
                </div>
                @error('image')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="name_en">@lang('site.name_en')</label>
                <input type="text" id="name_en" name="name_en"
                       placeholder="@lang('site.name_en')" value="{{$truck->name_en}}" required>
                @error('name_en')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="name_ar">@lang('site.name_ar')</label>
                <input type="text" id="name_ar" name="name_ar"
                       placeholder="@lang('site.name_ar')" value="{{$truck->name_ar}}">
                @error('name_ar')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="frames">@lang('site.axes')</label>
                <div class="position-relative">
                    <input type="number" step="1" min="4" id="frames" name="frames" required
                           placeholder="@lang('site.axes')" value="{{$truck->frames}}">
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
                           placeholder="@lang('site.weight')" value="{{$truck->weight}}">
                    <i class="fad fa-weight-hanging"></i>
                </div>
                @error('weight')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="checkbox-group">
                <label for="active">@lang('site.status')</label>
                <div class="form-check">
                    <input class="form-check-input transparent" type="checkbox" id="active" name="active" @if($truck->active==1 ) checked @endif>
                    <label class="form-check-label" for="active">@lang('site.enable')</label>
                </div>
                @error('active')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-navy min-width-170 mt-4">@lang('site.save')</button>
        </div>
    </div>
</form>
@endsection
@section('scripts')
    <!-- Custom ImageInput -->
    <script src="{{asset('assets/js/custom-imageInput.js')}}"></script>
@endsection