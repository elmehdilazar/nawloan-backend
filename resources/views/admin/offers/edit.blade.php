@extends('layouts.admin.app')
@section('title',' | ' . __('site.edit') .' ' . __('site.offer').' - '. $offer->id)
@section('content')
<div class="row page-titles mx-2">
    <div class="col p-md-0">
        <h4>@lang('site.offers')</h4>
    </div>
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.index')}}">@lang('site.dashboard')</a>
            </li>
            <li class="breadcrumb-item"><a href="{{route('admin.offers.index')}}">@lang('site.offers')</a>
            </li>
            <li class="breadcrumb-item active">@lang('site.edit') @lang('site.offer') {{' - '. $offer->id}}</li>
        </ol>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.offers.update',$offer->id)}}" method="POST">
                    @csrf
                    @method('put')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_id">@lang('site.service_provider')</label>
                                <input type="text" class="form-control" value="{{$offer->user->name}}" readonly>
                                <input type="hidden" class="form-control" id="user_id" name="user_id" value="{{$offer->user_id}}">
                                @error('user_id')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="driver_id">@lang('site.driver')</label>
                                <select id="driver_id" name="driver_id" class="form-control custom-select select2">
                                    <option value="" selected disabled>@lang('site.choose_driver')</option>
                                    @foreach ($drivers as $driver)
                                        <option value="{{$driver->id}}" {{$driver->id == $offer->driver_id ? 'selected' : ''}}>
                                            {{$driver->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="order_id">@lang('site.order_number')</label>
                                <input type="number" step="1" min="1" id="order_id" name="order_id" class="form-control"
                                    value="{{$offer->order_id}}" readonly>
                                @error('order_id')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">@lang('site.price')</label>
                                <input type="number" step="1" min="1" id="price" name="price" class="form-control" value="{{$offer->price}}">
                                @error('price')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="desc">@lang('site.desc')</label>
                                <textarea class="form-control" id="desc" name="desc" placeholder="@lang('site.desc')"
                                    rows="4">{{$offer->desc}}</textarea>
                                @error('desc')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="notes">@lang('site.notes')</label>
                                <textarea class="form-control" id="notes" name="notes" placeholder="@lang('site.notes')"
                                    rows="4">{{$offer->notes}}</textarea>
                                @error('notes')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-success mx-2">
                            <i class="fas fa-edit"></i> @lang('site.edit')
                        </button>
                        <a href="{{route('admin.offers.index')}}" class="btn btn-danger mx-2">
                            <i class="fas fa-close"></i> @lang('site.cancel')
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
