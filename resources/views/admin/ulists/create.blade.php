@extends('layouts.admin.app')
@section('title',' | ' . __('site.add') .' '. __('site.ulist'))
@section('styles')

@endsection
@section('content')
<h2 class="section-title mb-5">@lang('site.add') @lang('site.ulist')</h2>
<div class="row">
    <div class="col-xl-6 col-lg-8 co-12">
        <form action="{{route('admin.ulists.store')}}" method="POST">
            @csrf
            @method('post')
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
            <div class="form-group">
                <label for="users[]">@lang('site.users')</label>
                <select class="form-control select2-multi" id="users[]" name="users[]">
                    @foreach($utypes as $model=>$utype)
                        <optgroup label="@lang('site.the_'.$model.'')">
                            @foreach($utype as $user)
                                <option value="{{$user->id}}" >{{$user->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('users')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-navy min-width-170 mt-4">@lang('site.add')</button>
        </form>
    </div>
</div>
@endsection