@extends('layouts.admin.app')
@section('title',' | ' . __('site.edit') .' '. __('site.country_code'))
@section('content')
<h2 class="section-title mb-5">@lang('site.edit') @lang('site.country_code')</h2>
<form action="{{route('admin.countries.update',$country->id)}}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('put')
    <div class="row">
        <div class="col-xl-6 col-lg-8 co-12">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="image-input">
                        <label for="">@lang('site.image')</label>
                        <div class="imageUpload-wrapper"
                             style="background-image: url({{asset('assets/images/no-image.jpg')}})">
                            <div id="imageUpload" style="background-image: url({{$country->image!='' ? asset($country->image) : asset('uploads/users/default.png')}})">
                                <input type="file" name="image" id="image" class="mediaFile">
                                <label for="image"><i class="fad fa-pencil"></i></label>
                                <button id="clear-input"><i class="fal fa-times"></i></button>
                            </div>
                        </div>
                        @error('image')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="input-group">
                <label for="phone_code">@lang('site.phone_code')</label>
                <div class="input-group">
                    <input type="text" id="phone_code" name="phone_code"
                           placeholder="@lang('site.phone_code')" value="{{ $country->phone_code }}" required>
                </div>
                @error('phone_code')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="name">@lang('site.name')</label>
                <input type="text" id="name" name="name" placeholder="@lang('site.name')"
                       value="{{$country->name}}" required>
                @error('name')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="country_code">@lang('site.country_code')</label>
                <div class="input-group">
                    <input type="text" id="country_code" name="country_code" placeholder="@lang('site.country_code')"
                           value="{{$country->name}}" required>
                </div>
                @error('country_code')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="checkbox-group mb-0">
                <label for="active">@lang('site.status')</label>
                <div class="form-check">
                    <input class="form-check-input transparent" type="checkbox" id="active" name="active"
                           @if ($country->active==1)checked @endif>
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
    <!-- Custom ImageInput -->
    <script src="{{asset('assets/js/custom-imageInput.js')}}"></script>
@endsection

