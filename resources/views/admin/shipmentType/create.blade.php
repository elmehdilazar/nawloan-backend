@extends('layouts.admin.app')
@section('title',' | ' . __('site.add') .' '. __('site.shipment_type'))
@section('styles')

@endsection
@section('content')
<h2 class="section-title mb-5">@lang('site.add') @lang('site.shipment_type')</h2>
<form action="{{route('admin.shipment.store')}}" method="POST" >
    @csrf
    @method('post')
    <div class="row">
        <div class="col-xl-6 col-lg-8 co-12">
            <div class="input-group">
                <label for="name_en">@lang('site.name_en')</label>
                <input type="text" id="name_en" name="name_en"
                       placeholder="@lang('site.name_en')" value="{{old('name_en')}}" required>
                @error('name_en')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.name_ar')</label>
                <input type="text" id="name_ar" name="name_ar"
                       placeholder="@lang('site.name_ar')" value="{{old('name_ar')}}" required>
                @error('name_ar')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-navy min-width-170 mt-4">@lang('site.add')</button>
</form>
@endsection