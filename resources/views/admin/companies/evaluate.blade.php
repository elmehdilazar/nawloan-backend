@extends('layouts.admin.app')
@section('title',' | ' . __('site.evaluates'))
@section('styles')

<link href="{{ asset('assets/jquery-rating/css/star-rating.css') }}" rel="stylesheet">
<link href="{{ asset('assets/jquery-rating/themes/krajee-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css">
<!-- rating.js file -->
<style>
.rating-container .clear-rating ,.rating-container .caption{
    display:none !important;
}
</style>
@section('content')

<div class="container-fluid">
    <div class="row page-titles mx-1">
        <div class="col px-1 mx-1 ">
            <h4>@lang('site.comments_reviews')</h4>
            <img src="{{$user->userData->image!='' ? asset($user->userData->image) : asset('uploads/users/default.png')}}" style="width:100px;height:100px;border-radius: 50%;">
            <h5 class="mt2">{{$user->name}}</h5>
            <div >
                <input id="cleanInput" value="{{$avg}}" type="text" class="rating disabled"
                    data-theme="krajee-fas" data-min="0" data-max="5" data-step="1" data-size="xs" readOnly>
            </div>
            <span class="text-muted">({{$evaluates->count()}})@lang('site.reviews') , ({{$evaluates->count()}})@lang('site.comments')</span>
        </div>
    </div>
    <!-- row -->
    <div class="row justify-content-start my-2">
        @foreach ($evaluates as $eval)
        <div class="col-md-6 px-1">
            <div class="card shadow" style="background-color:#fbfafa;border-radius: 20px;">
                <div class="card-body p-0 py-2">
                    <div class="d-flex justify-content-start">
                        <div class="w-20 px-1">
                            <img src="{{$eval->user->userData->image!='' ? asset($eval->user->userData->image) : ''}}"
                             style="width:100px;height:100px;border-radius: 50%;">
                        </div>
                        <div class="col w-80">
                           <div class="d-flex justify-content-between">
                            <div style="text-align: start;">
                                <h6 style="">{{$eval->user->name}}</h6>
                            </div>
                            <div>
                                <td> {{$eval->created_at->diffForHumans()}}</td>
                            </div>
                           </div>
                           <div class="d-flex justify-content-between">
                            <div style="text-align: start;">
                                <input id="cleanInput" value="{{$eval->rate}}" type="text" class="rating disabled" data-theme="krajee-fas"
                                    data-min="0" data-max="5" data-size="xs" readOnly>
                            </div>
                            <div>
                                @if($eval->active==1)
                                <a href="{{route('admin.drivers.evaluate.changeStatus',$eval->id)}}" class="btn btn-danger"><i
                                        class="fas fa-times"></i></a>
                                @else
                                <a href="{{route('admin.drivers.evaluate.changeStatus',$eval->id)}}" class="btn btn-success"><i
                                        class="fas fa-check"></i></a>
                                @endif
                            </div>
                        </div>
                           <div class="d-flex justify-content-between">
                            <div class="text-muted" style="text-align: start;">{{$eval->comment}}</div>

                        </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection

@section('scripts')

<script type="text/javascript" src="{{ asset('assets/jquery-rating/js/star-rating.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/jquery-rating/themes/krajee-fas/theme.min.js') }}"></script>
@endsection
