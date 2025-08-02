@extends('layouts.admin.app')
@section('title',' | ' . __('site.themes_settings'))
@section('content')
<div class="row justify-content-start">
    <h4>
        @lang('site.themes_settings')
    </h4>
</div>
<div class="row justify-content-center">
    <div class="col-md-12">
        <form action="{{route('admin.setting.theme.store')}}" method="post">
            @csrf
            @method('post')
            <div class="row">
                <div class="col-md-6">
                 <h4 style="text-align-last:start;">@lang('site.light_theme')</h4>
                    <div class="form-group">
                        <label for="light_top_menu_bg">@lang('site.top_menu_bg')</label>
                        <input type="color" name="light_top_menu_bg" id="light_top_menu_bg" class="form-control round-btn"
                            placeholder="@lang('site.top_menu_bg')"
                            value="{{setting('light_top_menu_bg')!=''?setting('light_top_menu_bg'):'#f8f9fa'}}">
                        @error('light_top_menu_bg')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="light_top_menu_tc">@lang('site.top_menu_tc')</label>
                        <input type="color" name="light_top_menu_tc" id="light_top_menu_tc" class="form-control round-btn"
                            placeholder="@lang('site.top_menu_tc')"
                            value="{{setting('light_top_menu_tc')!=''?setting('light_top_menu_tc'):'#B1B9C0'}}">
                        @error('light_top_menu_tc')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="light_side_menu_bg">@lang('site.side_menu_bg')</label>
                        <input type="color" name="light_side_menu_bg" id="light_side_menu_bg" class="form-control round-btn"
                            placeholder="@lang('site.side_menu_bg')"
                            value="{{setting('light_side_menu_bg')!=''?setting('light_side_menu_bg'):'#F1F9FA'}}">
                        @error('light_side_menu_bg')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="light_side_menu_tc">@lang('site.side_menu_tc')</label>
                        <input type="color" name="light_side_menu_tc" id="light_side_menu_tc" class="form-control round-btn"
                            placeholder="@lang('site.side_menu_tc')"
                            value="{{setting('light_side_menu_tc')!=''?setting('light_side_menu_tc'):'#001A4E'}}">
                        @error('light_side_menu_tc')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="light_side_menu_ttc">@lang('site.side_menu_ttc')</label>
                        <input type="color" name="light_side_menu_ttc" id="light_side_menu_ttc" class="form-control round-btn"
                            placeholder="@lang('site.side_menu_ttc')"
                            value="{{setting('light_side_menu_ttc')!=''?setting('light_side_menu_ttc'):'#ADB5DB'}}">
                        @error('light_side_menu_ttc')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row col-md-12 d-fex justify-content-start mb-4">
                        <button type="submit" class="btn btn-primary " title="@lang('site.save')">
                            </i> @lang('site.save')
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                 <h4 style="text-align-last:start;">@lang('site.dark_theme')</h4>
                    <div class="form-group">
                        <label for="dark_top_menu_bg">@lang('site.top_menu_bg')</label>
                        <input type="color" name="dark_top_menu_bg" id="dark_top_menu_bg" class="form-control round-btn"
                            placeholder="@lang('site.top_menu_bg')"
                            value="{{setting('dark_top_menu_bg')!=''?setting('dark_top_menu_bg'):'#f8f9fa'}}">
                        @error('dark_top_menu_bg')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="dark_top_menu_tc">@lang('site.top_menu_tc')</label>
                        <input type="color" name="dark_top_menu_tc" id="dark_top_menu_tc" class="form-control round-btn"
                            placeholder="@lang('site.top_menu_tc')"
                            value="{{setting('dark_top_menu_tc')!=''?setting('dark_top_menu_tc'):'#B1B9C0'}}">
                        @error('dark_top_menu_tc')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="dark_side_menu_bg">@lang('site.side_menu_bg')</label>
                        <input type="color" name="dark_side_menu_bg" id="dark_side_menu_bg" class="form-control round-btn"
                            placeholder="@lang('site.side_menu_bg')"
                            value="{{setting('dark_side_menu_bg')!=''?setting('dark_side_menu_bg'):'#F1F9FA'}}">
                        @error('dark_side_menu_bg')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="dark_side_menu_tc">@lang('site.side_menu_tc')</label>
                        <input type="color" name="dark_side_menu_tc" id="dark_side_menu_tc" class="form-control round-btn"
                            placeholder="@lang('site.side_menu_tc')"
                            value="{{setting('dark_side_menu_tc')!=''?setting('dark_side_menu_tc'):'#001A4E'}}">
                        @error('dark_side_menu_tc')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="dark_side_menu_ttc">@lang('site.side_menu_ttc')</label>
                        <input type="color" name="dark_side_menu_ttc" id="dark_side_menu_ttc" class="form-control round-btn"
                            placeholder="@lang('site.side_menu_ttc')"
                            value="{{setting('dark_side_menu_ttc')!=''?setting('dark_side_menu_ttc'):'#ADB5DB'}}">
                        @error('dark_side_menu_ttc')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
