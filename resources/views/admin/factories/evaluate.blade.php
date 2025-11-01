@extends('layouts.admin.app')
@section('title',' | '  . __('site.evaluates'))
@section('styles')

<link href="{{ asset('assets/jquery-rating/css/star-rating.css') }}" rel="stylesheet">
<link href="{{ asset('assets/jquery-rating/themes/krajee-fas/theme.css') }}" media="all" rel="stylesheet" type="text/css">
<style>
.rating-container .clear-rating,
.rating-container .caption {
    display: none !important;
}
.rating-stars{
    direction: ltr !important;
}
</style>

@endsection
@section('content')

@php
    $factoryImage = optional($user->userData)->image;
    $factoryAvatar = $factoryImage ? asset($factoryImage) : asset('uploads/users/default.png');
@endphp

<div class="container-fluid">
    <div class="row page-titles mx-1">
        <div class="col px-1 mx-1 ">
            <h4>@lang('site.comments_reviews')</h4>
            <img src="{{ $factoryAvatar }}" style="width:100px;height:100px;border-radius: 50%;">
            <h5 class="mt-2">{{ $user->name }}</h5>
            <div>
                <input value="{{ $avg }}" type="text" class="rating disabled rating-stars"
                    data-theme="krajee-fas" data-min="0" data-max="5" data-step="1" data-size="xs" readonly>
            </div>
            <span class="text-muted">({{ $evaluates->total() }})@lang('site.reviews') , ({{ $evaluates->total() }})@lang('site.comments')</span>
        </div>
    </div>
    <div class="row justify-content-start my-2">
        @forelse ($evaluates as $eval)
        @php
            $reviewer = $eval->user;
            $reviewerImage = optional(optional($reviewer)->userData)->image;
            $reviewerAvatar = $reviewerImage ? asset($reviewerImage) : asset('uploads/users/default.png');
        @endphp
        <div class="col-md-6 px-1">
            <div class="card shadow" style="background-color:#fbfafa;border-radius: 20px;">
                <div class="card-body p-0 py-2">
                    <div class="d-flex justify-content-start">
                        <div class="w-20 px-1">
                            <img src="{{ $reviewerAvatar }}"
                                 style="width:100px;height:100px;border-radius: 50%;">
                        </div>
                        <div class="col w-80">
                           <div class="d-flex justify-content-between align-items-start">
                                <h6 style="text-align: start;">{{ optional($reviewer)->name ?? '--' }}</h6>
                                <span>{{ optional($eval->created_at)->diffForHumans() }}</span>
                           </div>
                           <div class="d-flex justify-content-between align-items-center">
                                <input value="{{ $eval->rate }}" type="text" class="rating disabled rating-stars"
                                    data-theme="krajee-fas" data-min="0" data-max="5" data-size="xs" readonly>
                                <div>
                                    @if((int) $eval->active === 1)
                                    <a href="{{ route('admin.factories.evaluate.changeStatus',$eval->id) }}" class="btn btn-danger">
                                        <i class="fas fa-remove"></i>
                                    </a>
                                    @else
                                    <a href="{{ route('admin.factories.evaluate.changeStatus',$eval->id) }}" class="btn btn-success">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    @endif
                                </div>
                           </div>
                           <div class="d-flex justify-content-between">
                                <div class="text-muted" style="text-align: start;">{{ $eval->comment }}</div>
                           </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted">
            @lang('site.no_records_found')
        </div>
        @endforelse
    </div>
    @if($evaluates->hasPages())
    <div class="d-flex justify-content-center">
        {{ $evaluates->links() }}
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('assets/jquery-rating/js/star-rating.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/jquery-rating/themes/krajee-fas/theme.min.js') }}"></script>
@endsection
