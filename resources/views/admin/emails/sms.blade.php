@extends('layouts.admin.app')
@section('title',' | ' . __('site.send_sms'))

@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-2">
        <div class="col p-md-0">
            <h4>@lang('site.send_sms')</h4>
        </div>
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.index')}}">@lang('site.dashboard')</a>
                </li>
                <li class="breadcrumb-item">@lang('site.messages')</li>
                <li class="breadcrumb-item active">@lang('site.send_sms')</li>
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
    <div class="col-md-12">
                <form action="{{route('admin.messages.sendSms')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('post')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="phone">@lang('site.phone')<span class="text-danger">*</span></label>
                                <input type="tel" name="phone" id="phone" class="form-control"
                                    placeholder="@lang('site.phone')" required >
                                @error('phone')
                                <span class="text-danger"> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="message">@lang('site.message')<span class="text-danger">*</span></label>
                                <textarea name="message" id="editor" class="form-control"rows="6"
                                    placeholder="@lang('site.message')"  >{{old('message')}}</textarea>

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
                            <a class="btn btn-danger  m-2" href="{{route('admin.index')}}" title="@lang('site.cancel')">
                                <i class="fas fa-remove"></i> @lang('site.cancel')
                            </a>
                        </div>
                    </div>
                </form>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-secondary">
                        <tr>
                            <td scope="col">@lang('site.num')</td>
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
                            <td>{{$message->sender->phone}}</td>
                            <td>{{$message->message}}</td>
                            <td>{{$message->type}}</td>
                            <td>{{$message->sender_id !='' ? $message->sender->name : ''}}</td>
                            <td>{{$message->receiver_id !='' ? $message->receiver->name : ''}}</td>
                            <td> {{$message->created_at->diffForHumans()}}</td>
                        </tr>
                        <!-- Modal -->
                       {{--  <div class="modal fade" id="replayModal_{{$index}}" tabindex="-1" role="dialog"
                            aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                                            @lang('site.message_replay')</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('admin.customer_messages.replay',$message->id)}}"
                                            method="post">
                                            @csrf
                                            @method('post')
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="text-label" for="desc">@lang('site.desc')</label>
                                                    <textarea id="desc" name="desc" placeholder="@lang('site.desc')"
                                                        rows="3"
                                                        class="form-control">@if(!empty($message->desc)) {{$message->desc}} @else {{old('desc')}} @endif</textarea>
                                                    @error('desc')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-label" for="notes">@lang('site.notes')</label>
                                                    <textarea id="notes" name="notes" placeholder="@lang('site.notes')"
                                                        rows="3"
                                                        class="form-control">@if(!empty($message->notes)) {{$message->notes}} @else {{old('notes')}} @endif</textarea>
                                                    @error('notes')
                                                    <span class="text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-center mt-2">
                                                <button type="submit" class="btn btn-success mx-2"
                                                    title="@lang('site.replay')">
                                                    <i class="fa fa-send"></i>
                                                    @lang('site.replay')
                                                </button>
                                                <button type="submit" class="btn btn-danger mx-2"
                                                    title="@lang('site.cancel')" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <i class="fas fa-remove"></i> @lang('site.cancel')
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{$sms->appends(request()->query())->links()}}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
