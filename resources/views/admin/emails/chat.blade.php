@extends('layouts.admin.app')
@section('title',' | ' . __('site.chat'))
@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-2">
        <div class="col p-md-0">
            <h4>@lang('site.chat')</h4>
        </div>
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.index')}}">@lang('site.dashboard')</a>
                </li>
                <li class="breadcrumb-item">@lang('site.messages')</li>
                <li class="breadcrumb-item active">@lang('site.chat')</li>
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
                                    <label for="body">@lang('site.body')<span class="text-danger">*</span></label>
                                    <textarea name="body" id="body" class="form-control" rows="6"
                                        placeholder="@lang('site.body')">{{old('body')}}</textarea>

                                    @error('body')
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
    </div>
</div>
@endsection
@section('scripts')
<script>
    // Retrieve Firebase Messaging object.
        const messaging = firebase.messaging();
        // Add the public key generated from the console here.
        messaging.usePublicVapidKey("BCRJsjGk2nqEMYNn2TDESbxL8dGOts8jXApwxnCky9gZR2XUtl8ASK6SKq4T42mwCO_Ty6_TK_gNh-x_6o6RYjY");
        function sendTokenToServer(fcm_token) {
            const user_id = '{{auth()->user()->id}}';
            axios.post('/api/save-token', {
                fcm_token, user_id
            })
                .then(res => {
                    console.log(res);
                })
        }
        function retreiveToken(){
            messaging.getToken().then((currentToken) => {
                if (currentToken) {
                    sendTokenToServer(currentToken);
                    // updateUIForPushEnabled(currentToken);
                } else {
                    // Show permission request.
                    //console.log('No Instance ID token available. Request permission to generate one.');
                    // Show permission UI.
                    //updateUIForPushPermissionRequired();
                    //etTokenSentToServer(false);
                    alert('You should allow notification!');
                }
            }).catch((err) => {
                console.log(err.message);
                // showToken('Error retrieving Instance ID token. ', err);
                // setTokenSentToServer(false);
            });
        }
        retreiveToken();
        messaging.onTokenRefresh(()=>{
            retreiveToken();
        });
        messaging.onMessage((payload)=>{
            console.log('Message received');
            console.log(payload);

            location.reload();
        });
</script>
@endsection
@endsection