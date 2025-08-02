@extends('layouts.admin.app')
@section('title',' | ' . __('site.send_mail'))
@section('styles')
    <style>
.ck-editor__editable_inline {
    min-height: 250px;
}
    </style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-2">
        <div class="col p-md-0">
            <h4>@lang('site.send_mail')</h4>
        </div>
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.index')}}">@lang('site.dashboard')</a>
                </li>
                <li class="breadcrumb-item">@lang('site.messages')</li>
                <li class="breadcrumb-item active">@lang('site.send_mail')</li>
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
                <form action="{{route('admin.messages.sendMail')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('post')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">@lang('site.email')<span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control"
                                    placeholder="@lang('site.to')" required >
                                @error('email')
                                <span class="text-danger"> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cc">@lang('site.cc')<span class="text-danger">*</span></label>
                                <input type="text" name="cc" id="cc" class="form-control"
                                    placeholder="@lang('site.to')" required >
                                @error('cc')
                                <span class="text-danger"> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bcc">@lang('site.bcc')<span class="text-danger">*</span></label>
                                <input type="text" name="bcc" id="bcc" class="form-control"
                                    placeholder="@lang('site.bcc')" required >
                                @error('bcc')
                                <span class="text-danger"> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subject">@lang('site.subject')<span class="text-danger">*</span></label>
                                <input type="subject" name="subject" id="subject" class="form-control"
                                    placeholder="@lang('site.subject')" >
                                @error('subject')
                                <span class="text-danger"> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="message">@lang('site.message')<span class="text-danger">*</span></label>
                                <textarea name="message" id="editor" class="form-control"rows="12"
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
                                <i class="fe fe-mail fe-16"></i> @lang('site.send')
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
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-secondary">
                        <tr>
                            <td scope="col">#</td>
                            <td scope="col">@lang('site.email')</td>
                            <td scope="col">@lang('site.message')</td>
                            <td scope="col">@lang('site.type')</td>
                            <td scope="col">@lang('site.sender')</td>
                            <td scope="col">@lang('site.receiver')</td>
                            <td scope="col">@lang('site.at')</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages as $index=>$message)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{$message->notes}}</td>
                            <td>{{strip_tags($message->message)}}</td>
                            <td>{{$message->type}}</td>
                            <td>{{$message->sender_id !='' ? $message->sender->name : ''}}</td>
                            <td>{{$message->receiver_id !='' ? $message->receiver->name : ''}}</td>
                            <td> {{$message->created_at->diffForHumans()}}</td>
                        </tr>
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
@section('scripts')
{{-- <script src="{{asset('assets/js/ckeditor.js')}}"></script>
<script src="{{asset('assets/js/ckeditor5-translations/ar.js')}}"></script> --}}
<script src="https://cdn.ckeditor.com/4.17.1/full/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/4.17.1/full/lang/ar.js"></script>

    <script>
    CKEDITOR.replace( 'editor' ,{ language: '{{app()->getLocale()}}',});
            /*ClassicEditor
            .create( document.querySelector( '#editor' ), {
                language: 'ar',
                alignment: {
                    options: [ 'left', 'right' ]
                },
            } )
            .then( editor => {
                    console.log( editor );

            } )
            .catch( error => {
                    console.error( error );
            } ); */
            // $(document).ready(function(){
            //     $('#editor').on('change',function(){
            //         console.log('tag',$('#editor').val());
            //          $('#message').val( $('#editor').val());
            //     })
            // });
    </script>
@endsection
