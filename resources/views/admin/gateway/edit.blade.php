@extends('layouts.admin.app')
@section('title',' | ' . __('site.edit').' '. __('site.the_gateway'))
@section('content')
<h2 class="section-title mb-5">@lang('site.edit') @lang('site.the_gateway')</h2>
<form action="{{route('admin.gateway.update',$gateway->id)}}" method="post" enctype="multipart/form-data">
    @csrf
    @method('put')
    <div class="row">
        <div class="col-xl-7 col-lg-9 co-12">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="image-input">
                        <label for="">@lang('site.image')</label>
                        <div class="imageUpload-wrapper"
                             style="background-image: url({{asset('assets/images/no-image.jpg')}})">
                            <div id="imageUpload" style="background-image: url({{$gateway->image!='' ? asset($gateway->image) : asset('uploads/img/logo.png')}})">
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
                <label for="name">@lang('site.name')</label>
                <input type="text" name="name" id="name" placeholder="@lang('site.name')"
                       value="{{$gateway->name}}">
                @error('name')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="select-group">
                <label for="type">@lang('site.type')</label>
                <select class="form-control select2 no-search" name="type" id="type">
                    <option value="" selected disabled>@lang('site.choose_type')</option>
                    <option value="payment" {{$gateway->type=='payment' ? 'selected' : '' }}>
                        @lang('site.the_payment')
                    </option>
                    <option value="sms" {{$gateway->type=='sms' ? 'selected' : '' }}>@lang('site.sms')</option>
                    <option value="firebase" {{$gateway->type=='firebase' ? 'selected' : '' }}>
                        @lang('site.firebase')
                    </option>
                </select>
                @error('type')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="textarea-group">
                <label for="publishable_key">@lang('site.publishable_key')</label>
                <textarea name="publishable_key" id="publishable_key"
                          placeholder="@lang('site.publishable_key')" rows="4">
                            {{$gateway->publishable_key}}
                        </textarea>
                @error('publishable_key')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="textarea-group">
                <label for="secret_key">@lang('site.secret_key')</label>
                <textarea name="secret_key" id="secret_key"
                          placeholder="@lang('site.secret_key')" rows="4">
                            {{$gateway->secret_key}}
                        </textarea>
                @error('secret_key')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="checkbox-group mb-0">
                <label for="active">@lang('site.status')</label>
                <div class="form-check">
                    <input class="form-check-input transparent" type="checkbox" name="active" id="active" @if ($gateway->active==1)checked @endif>
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
    <!-- Custom ImageInput -->
    <script src="{{asset('assets/js/custom-imageInput.js')}}"></script>
@endsection
