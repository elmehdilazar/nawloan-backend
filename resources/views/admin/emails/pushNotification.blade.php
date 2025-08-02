@extends('layouts.admin.app')
@section('title',' | ' . __('site.send') .' ' . __('site.notifications'))
@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-2">
        <div class="col p-md-0">
            <h4>@lang('site.send') @lang('site.notifications')</h4>
        </div>
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.index')}}">@lang('site.dashboard')</a>
                </li>
                <li class="breadcrumb-item">@lang('site.messages')</li>
                <li class="breadcrumb-item active">@lang('site.send') @lang('site.notifications')</li>
            </ol>
        </div>
    </div>
    @if (session('success'))
    <div class="alert alert-success" role="alert">
        <p class="mb-0">{{session('success')}}</p>
    </div>
    @endif
    @if (session('errors'))
    <div class="alert alert-danger" role="alert">
        <p class="mb-0">{{session('errors')}}</p>
    </div>
    @endif
    <div class="row justify-content-start">
        <div class="col-12">
            <div class="card card-light">
                <div class="card-body">
                    <form action="{{route('admin.pushNoti.send')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('post')
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title">@lang('site.title')<span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        placeholder="@lang('site.title')" required>
                                    @error('title')
                                    <span class="text-danger"> {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notification">@lang('site.notification')<span class="text-danger">*</span></label>
                                    <textarea name="message" id="message" class="form-control" rows="6"
                                        placeholder="@lang('site.notification')">{{old('message')}}</textarea>

                                    @error('message')
                                    <span class="text-danger"> {{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                        </div>
                        <div class="row col-md-12 d-fex justify-content-center mb-2">
                            <div class="col-md-4 text-center">
                                <button type="submit" class="btn btn-success m-2" title="@lang('site.send')">
                                    <i class="fas fa-sms"></i> @lang('site.send')
                                </button>
                                <a class="btn btn-danger  m-2" href="{{route('admin.index')}}"
                                    title="@lang('site.cancel')">
                                    <i class="fas fa-remove"></i> @lang('site.cancel')
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>{{--
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table secondary-table-bordered table-bordered">
                        <thead class="thead-secondary">
                            <tr>
                                <td scope="col">#</td>
                                <td scope="col">@lang('site.phone')</td>
                                <td scope="col">@lang('site.message')</td>
                                <td scope="col">@lang('site.type')</td>
                                <td scope="col">@lang('site.sender')</td>
                                <td scope="col">@lang('site.receiver')</td>
                                <td scope="col">@lang('site.at')</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sms as $index=>$message)
                            <tr>
                                <td>{{$index + 1}}</td>
                                <td>{{$message->notes}}</td>
                                <td>{{$message->message}}</td>
                                <td>{{$message->type}}</td>
                                <td>{{$message->sender_id !='' ? $message->sender->name : ''}}</td>
                                <td>{{$message->receiver_id !='' ? $message->receiver->name : ''}}</td>
                                <td> {{$message->created_at->diffForHumans()}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{$sms->appends(request()->query())->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>
@endsection

