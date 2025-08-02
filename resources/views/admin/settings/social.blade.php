@extends('layouts.admin.app')
@section('title',' | ' . __('site.social_settings'))
@section('content')
<h2 class="section-title mb-5">@lang('site.social_settings')</h2>
<form action="{{route('admin.setting.social.store')}}" method="post">
    @csrf
    @method('post')
    <div class="row">
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="facebook_link">@lang('site.facebook_link')</label>
                <input type="url" name="facebook_link" id="facebook_link"
                       placeholder="@lang('site.facebook_link')"
                       value="{{setting('facebook_link')!=''?setting('facebook_link'):''}}">
                @error('facebook_link')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="instagram_link">@lang('site.instagram_link')</label>
                <input type="url" name="instagram_link" id="instagram_link"
                       placeholder="@lang('site.instagram_link')"
                       value="{{setting('instagram_link')!=''?setting('instagram_link'):''}}">
                @error('instagram_link')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="twitter_link">@lang('site.twitter_link')</label>
                <input type="url" name="twitter_link" id="twitter_link"
                       placeholder="@lang('site.twitter_link')"
                       value="{{setting('twitter_link')!=''?setting('twitter_link'):''}}">
                @error('twitter_link')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="snapchat_link">@lang('site.snapchat_link')</label>
                <input type="url" name="snapchat_link" id="snapchat_link"
                       placeholder="@lang('site.snapchat_link')"
                       value="{{setting('snapchat_link')!=''?setting('snapchat_link'):''}}">
                @error('snapchat_link')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="linkedin_link">@lang('site.linkedin_link')</label>
                <input type="url" name="linkedin_link" id="linkedin_link"
                       placeholder="@lang('site.linkedin_link')"
                       value="{{setting('linkedin_link')!=''?setting('linkedin_link'):''}}">
                @error('linkedin_link')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="linkedin_link">@lang('site.youtube_link')</label>
                <input type="url" name="youtube_link" id="youtube_link"
                       placeholder="@lang('site.youtube_link')"
                       value="{{setting('youtube_link')!=''?setting('youtube_link'):''}}">
                @error('youtube_link')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-navy min-width-170 mt-4" title="@lang('site.save')">@lang('site.save')</button>
</form>
@endsection
