@extends('layouts.admin.app')
@section('title',' | ' . __('site.customers_messages'))
@section('content')
<div class="row page-titles mx-2">
    <div class="col p-md-0">
        <h4>@lang('site.customers_messages')</h4>
    </div>
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.index')}}">@lang('site.dashboard')</a>
            </li>
            <li class="breadcrumb-item active">@lang('site.customers_messages')</li>
        </ol>
    </div>
</div>
 <div class="row justify-content-center">
    <div class="col-12">
        <div class="card mb-2">
            <div class="card-body">
                <form action="{{route('admin.messages.customer_messages')}}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="title">@lang('site.title')</label>
                                <input type="text" class="form-control" name="title" id="title"
                                    value="{{request()->title}}" placeholder="@lang('site.title')">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="message">@lang('site.message')</label>
                                <input type="text" class="form-control" name="message" id="message"
                                    value="{{request()->message}}" placeholder="@lang('site.message')">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="user">@lang('site.user')</label>
                                <select class="form-control custom-select select2" id="user_id" name="user_id">
                                    <option value="0" selected>@lang('site.view_all')</option>
                                    @foreach ($users as $user)
                                    <option value="{{$user->id}}" {{ request()->user_id== $user->id ? 'selected' : '' }}>
                                        {{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="replay_by">@lang('site.replay_by')</label>
                                <select class="form-control custom-select select2" id="replay_by" name="replay_by">
                                    <option value="0" selected>@lang('site.view_all')</option>
                                    @foreach ($replaiers as $user)
                                    <option value="{{$user->id}}" {{ request()->replay_by== $user->id ? 'selected' : '' }}>
                                        {{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center my-2">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i>
                            @lang('site.search')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table secondary-table-bordered table-bordered">
                    <thead class="thead-secondary">
                        <tr>
                            <td scope="col">#</td>
                            <td scope="col">@lang('site.title')</td>
                            <td scope="col">@lang('site.message')</td>
                            <td scope="col">@lang('site.user')</td>
                            </td><td scope="col">@lang('site.replay_by')</td>
                            <td scope="col">@lang('site.time')</td>
                            <td scope="col">@lang('site.actions')</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages as $index=>$message)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td><a href="#" data-toggle="modal" data-target="#showModal_{{$index}}"
                                    title="@lang('site.show')">{{$message->title}}</a></td>
                            <td>{{$message->message}}</td>
                            <td>{{$message->user->name}}</td>
                            <td>{{$message->replay !='' ? $message->replayBy->name : ''}}</td>
                            <td> {{$message->created_at->diffForHumans()}}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="#" data-toggle="modal"
                                    data-target="#replayModal_{{$index}}" title="@lang('site.edit')"><i
                                        class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                            <!-- Modal -->
                            <div class="modal fade" id="replayModal_{{$index}}" tabindex="-1" role="dialog"
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
                                            <form action="{{route('admin.messages.customer_messages.replay',$message->id)}}"
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
                            </div>
                            <div class="modal fade" id="showModal_{{$index}}" tabindex="-1" role="dialog"
                                aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                                                @lang('site.show') @lang('site.customers_message')</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row ">
                                                <div class="col-md-5">
                                                    @lang('site.title')
                                                </div>
                                                <div class="col-md-7">
                                                    {{$message->title}}
                                                </div>
                                            </div>
                                            <div class="row ">
                                                <div class="col-md-5">
                                                    @lang('site.message')
                                                </div>
                                                <div class="col-md-7">
                                                    <p text-indent: 0px!important;>{{$message->message}}</p>
                                                </div>
                                            </div>
                                            @if(!empty($message->desc))
                                            <div class="row ">
                                                <div class="col-md-5">
                                                    @lang('site.desc')
                                                </div>
                                                <div class="col-md-7">
                                                    <p text-indent: 0px!important;>{{$message->desc}}</p>
                                                </div>
                                            </div>
                                            @endif
                                            @if(!empty($message->notes))
                                            <div class="row ">
                                                <div class="col-md-5">
                                                    @lang('site.notes')
                                                </div>
                                                <div class="col-md-7">
                                                    <p text-indent: 0px!important;>{{$message->notes}}</p>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="row ">
                                                <div class="col-md-5">
                                                    @lang('site.user')
                                                </div>
                                                <div class="col-md-7">
                                                    {{$message->user->name}}
                                                </div>
                                            </div>
                                            @if(!empty($message->replay_by))
                                            <div class="row ">
                                                <div class="col-md-5">
                                                    @lang('site.replay_by')
                                                </div>
                                                <div class="col-md-7">
                                                    {{$message->replayBy->name}}
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                                @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{$messages->appends(request()->query())->links()}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
