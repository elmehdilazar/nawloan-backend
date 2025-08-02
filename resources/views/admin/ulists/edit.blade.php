@extends('layouts.admin.app')
@section('title',' | ' . __('site.edit') .' '. __('site.ulist'))
@section('styles')
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
<h2 class="section-title mb-5">@lang('site.edit') @lang('site.ulist')</h2>
<div class="row mb-5">
    <div class="col-xl-6 col-lg-8 co-12">
        <form action="{{route('admin.ulists.update',$list->id)}}" method="POST">
            @csrf
            @method('put')
            <div class="input-group">
                <label for="name_en">@lang('site.name_en')</label>
                <input type="text" id="name_en" name="name_en"
                       placeholder="@lang('site.name_en')" value="{{$list->name_en}}" required>
                @error('name_en')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="name_ar">@lang('site.name_ar')</label>
                <input type="text" id="name_ar" name="name_ar"
                       placeholder="@lang('site.name_ar')" value="{{$list->name_ar}}" required>
                @error('name_ar')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="users[]">@lang('site.users')</label>
                <select class="form-control select2-multi" id="users[]" name="users[]">
                    @foreach($utypes as $model=>$utype)
                        <optgroup label="{{$model=='user' ? __('site.customers') : __('site.the_'.$model.'')}}">
                            @foreach($utype as $user)
                                <option value="{{$user->id}}"
                                @foreach($list->users as $usr)
                                    {{$usr->user->id==$user->id ? 'selected' : ''}}
                                        @endforeach>
                                    {{$user->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('users')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-navy min-width-170 mt-4">@lang('site.save')</button>
        </form>
    </div>
</div>
<table class="table datatables datatables-active check-disabled">
    <thead>
    <tr>
        <th>@lang('site.num')</th>
        <th>@lang('site.user')</th>
        <th>@lang('site.type')</th>
        <th>@lang('site.remove')</th>
    </tr>
    </thead>
    <tbody>
    @foreach($list->users as $index=>$user)
        <tr>
            <td>{{$index + 1}}</td>
            <td> {{$user->user->name}}</td>
            <td> @lang('site.'.$user->user->type.'')</td>
            <td>
                <ul class="actions">
                    <li>
                        <a href="#" class="cancel">
                            <i class="fad fa-trash-alt"></i>
                        </a>
                    </li>
                </ul>
            </td>
        </tr>
    @endforeach
    @if($list->users->count()==0)
        <tr>
            <td colspan="4" style="width:100%; text-align: center !important;">@lang('site.no_records_found')</td>
        </tr>
    @endif
    </tbody>
</table>
@endsection
@section('scripts')
    <!-- Data Tables -->
    <script src='{{asset('assets/tiny/js/jquery.dataTables.min.js')}}'></script>
    <script src='{{asset('assets/tiny/js/dataTables.bootstrap4.min.js')}}'></script>
    <!-- DataTables Playground (Setups, Options, Actions) -->
    <script src='{{asset('assets/js/dataTables-init.js')}}'></script>
@endsection