@extends('layouts.admin.app')
@section('title',' | ' . __('site.seo_settings'))
@section('content')
<h2 class="section-title mb-4">@lang('site.seo_settings')</h2>
<form action="{{route('admin.setting.seo.store')}}" method="post" enctype="multipart/form-data">
    @csrf
    @method('post')
    <div class="row mb-5">
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="title">@lang('site.title')</label>
                <input type="text" name="title" id="title"
                       placeholder="@lang('site.title')" value="{{setting('title')!=''? setting('title'):''}}">
                @error('title')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="canonical">@lang('site.canonical')</label>
                <input type="url" name="canonical" id="canonical"
                       placeholder="@lang('site.canonical')"
                       value="{{setting('canonical')!='' ?setting('canonical') :''}}">
                @error('canonical')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="textarea-group">
                <label for="keywords_ar">@lang('site.keywords_ar')</label>
                <textarea name="keywords_ar" id="keywords_ar"
                          placeholder="@lang('site.keywords_ar')" rows="4">
                    {{setting('keywords_ar')!=''?setting('keywords_ar'):''}}
                </textarea>
                @error('keywords_ar')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="textarea-group">
                <label for="keywords_en">@lang('site.keywords_en')</label>
                <textarea name="keywords_en" id="keywords_en"
                          placeholder="@lang('site.keywords_en')" rows="4">
                    {{setting('keywords_en')!=''?setting('keywords_en'):''}}
                </textarea>
                @error('keywords_en')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="textarea-group">
                <label for="description_ar">@lang('site.og_description_ar')</label>
                <textarea name="description_ar" id="description_ar"
                          placeholder="@lang('site.og_description_ar')" rows="4">
                                    {{setting('description_ar')!=''?setting('description_ar'):''}}
                                </textarea>
                @error('description_ar')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="textarea-group">
                <label for="">@lang('site.og_description_en')</label>
                <textarea name="description_en" id="description_en"
                          placeholder="@lang('site.og_description_en')" rows="4">
                                    {{setting('description_en')!=''?setting('description_en'):''}}
                                </textarea>
                @error('description_en')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <h2 class="section-title mb-4">@lang('site.open_graph')</h2>
    <div class="row mb-5">
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="og_site_name">@lang('site.og_site_name')</label>
                <input type="text" name="og_site_name" id="og_site_name"
                       placeholder="@lang('site.og_site_name')"
                       value="{{setting('og_site_name')!=''?setting('og_site_name'):''}}">
                @error('og_site_name')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="og_title">@lang('site.og_title')</label>
                <input type="text" name="og_title" id="og_title"
                       placeholder="@lang('site.og_title')"
                       value="{{setting('og_title')!=''?setting('og_title'):''}}">
                @error('og_title')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="og_type">@lang('site.og_type')</label>
                <input type="text" name="og_type" id="og_type"
                       placeholder="@lang('site.og_type')"
                       value="{{setting('og_type')!=''?setting('og_type'):''}}">
                @error('og_type')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="og_url">@lang('site.og_url')</label>
                <input type="url" name="og_url" id="og_url"
                       placeholder="@lang('site.og_url')"
                       value="{{setting('og_url')!=''?setting('og_url'):''}}">
                @error('og_url')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="textarea-group">
                <label for="og_description_ar">@lang('site.og_description_ar')</label>
                <textarea name="og_description_ar" id="og_description_ar"
                          placeholder="@lang('site.og_description_ar')" rows="5">
                                    {{setting('og_description_ar')!=''?setting('og_description_ar'):''}}
                                </textarea>
                @error('og_description_ar')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="textarea-group">
                <label for="">@lang('site.og_description_en')</label>
                <textarea name="og_description_en" id="og_description_en"
                          placeholder="@lang('site.og_description_en')" rows="5">
                                    {{setting('og_description_en')!=''?setting('og_description_en'):''}}
                                </textarea>
                @error('og_description_en')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="image-input">
                <label for="">@lang('site.og_image')</label>
                <div class="imageUpload-wrapper"
                     style="background-image: url({{asset('assets/images/no-image.jpg')}})">
                    <div id="imageUpload" class="contain dark" style="background-image: url({{ setting('og_image') != '' ? asset( setting('og_image')) :  asset('uploads/img/logo.png') }})">
                        <input type="file" name="og_image" id="og_image" class="mediaFile">
                        <label for="og_image"><i class="fad fa-pencil"></i></label>
                        <button id="clear-input"><i class="fal fa-times"></i></button>
                    </div>
                </div>
                @error('og_image')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <h2 class="section-title mb-4">@lang('site.twitter')</h2>
    <div class="row">
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="twitter_title">@lang('site.twitter_title')</label>
                <input type="text" name="twitter_title" id="twitter_title"
                       placeholder="@lang('site.title')"
                       value="{{setting('twitter_title')!=''?setting('twitter_title'):''}}">
                @error('twitter_title')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="twitter_domain">@lang('site.twitter_domain')</label>
                <input type="text" name="twitter_domain" id="twitter_domain"
                       placeholder="@lang('site.twitter_domain')"
                       value="{{setting('twitter_domain')!='' ?setting('twitter_domain') :''}}">
                @error('twitter_domain')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="textarea-group">
                <label for="twitter_card_ar">@lang('site.twitter_card_ar')</label>
                <textarea name="twitter_card_ar" id="twitter_card_ar"
                          placeholder="@lang('site.twitter_card_ar')" rows="4">
                    {{setting('twitter_card_ar')!=''?setting('twitter_card_ar'):''}}
                </textarea>
                @error('twitter_card_ar')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="textarea-group">
                <label for="twitter_card_en">@lang('site.twitter_card_en')</label>
                <textarea name="twitter_card_en" id="twitter_card_en"
                          placeholder="@lang('site.twitter_card_en')" rows="4">
                    {{setting('twitter_card_en')!=''?setting('twitter_card_en'):''}}
                </textarea>
                @error('twitter_card_en')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="textarea-group">
                <label for="twitter_description_ar">@lang('site.twitter_description_ar')</label>
                <textarea name="twitter_description_ar" id="twitter_description_ar"
                          placeholder="@lang('site.twitter_description_ar')" rows="4">
                                    {{setting('twitter_description_ar')!=''?setting('twitter_description_ar'):''}}
                                </textarea>
                @error('twitter_description_ar')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="textarea-group">
                <label for="twitter_description_en">@lang('site.twitter_description_en')</label>
                <textarea name="twitter_description_en" id="twitter_description_en"
                          placeholder="@lang('site.twitter_description_en')" rows="4">
                                    {{setting('twitter_description_en')!=''?setting('twitter_description_en'):''}}
                                </textarea>
                @error('twitter_description_en')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-navy min-width-170 mt-4" title="@lang('site.save')">@lang('site.save')</button>
</form>
@endsection

@section('scripts')
    <!-- Custom ImageInput -->
    <script src="{{asset('assets/js/custom-imageInput.js')}}"></script>
@endsection

