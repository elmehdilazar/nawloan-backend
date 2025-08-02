@extends('layouts.admin.app')
@section('title',' | ' . __('site.create_career_category'))
@section('styles')
    <!-- Quill Editor -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/quill.snow.css')}}">
@endsection
@section('content')
<h2 class="section-title mb-5">@lang('site.create_career_category')</h2>
<form action="{{route('admin.career_categories.store')}}" method="post">
    @csrf
    @method('post')
    <div class="row">
        <div class="col-xl-6 col-lg-12 co-12">
            <h4 class="inner-title mb-4">@lang('site.English')</h4>
            <div class="input-group">
                <label for="">@lang('site.category_title')</label>
                <input type="text" aria-label="@lang('site.category_title')"
                       placeholder="@lang('site.please_enter_category_title')"
                       value="{{old('category_en')}}" name="category_en">
                @error("category_en")
                <small class="form-text text-danger">{{$message}}</small>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.category_description')</label>
                <div id="" class="editor" style="height: 180px;">
                    <p>@lang('site.write_something')</p>
                </div>
                <input type="hidden" name="category_desc_en" id="" value="{{ old('category_desc_en')}}">
                @error("category_desc_en")
                <small class="form-text text-danger">{{$message}}</small>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.meta_title')</label>
                <input type="text" aria-label="@lang('site.meta_title')"
                       placeholder="@lang('site.please_enter_meta_title')"
                       value="{{old('meta_title_en')}}" name="meta_title_en">
                @error("meta_title_en")
                <small class="form-text text-danger">{{$message}}</small>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.slug')</label>
                <input type="text" aria-label="@lang('site.slug')"
                       placeholder="@lang('site.please_enter_slug')"
                       value="{{old('slug_en')}}" name="slug_en">
                @error("slug_en")
                <small class="form-text text-danger">{{$message}}</small>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.meta_description')</label>
                <div id="" class="editor" style="height: 180px;">
                    <p>@lang('site.write_something')</p>
                </div>
                <input type="hidden" name="meta_desc_en" id="" value="{{ old('meta_desc_en')}}">
                @error("meta_desc_en")
                <small class="form-text text-danger">{{$message}}</small>
                @enderror
            </div>
        </div>
        <div class="col-xl-6 col-lg-12 co-12">
            <h4 class="inner-title mb-4">@lang('site.Arabic')</h4>
            <div class="input-group">
                <label for="">@lang('site.category_title')</label>
                <input type="text" aria-label="@lang('site.category_title')"
                       placeholder="@lang('site.please_enter_category_title')"
                       value="{{old('category_ar')}}" name="category_ar">
                @error("category_ar")
                <small class="form-text text-danger">{{$message}}</small>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.category_description')</label>
                <div id="" class="editor" style="height: 180px;">
                    <p>
                        @if(old('category_desc_ar')) {{old('category_desc_ar')}} @else @lang('site.write_something') @endif
                    </p>
                </div>
                <input type="hidden" name="category_desc_ar" id="" value="{{old('category_desc_ar')}}">
                @error("category_desc_ar")
                <small class="form-text text-danger">{{$message}}</small>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.meta_title')</label>
                <input type="text" aria-label="@lang('site.meta_title')"
                       placeholder="@lang('site.please_enter_meta_title')"
                       value="{{old('meta_title_ar')}}" name="meta_title_ar">
                @error("meta_title_ar")
                <small class="form-text text-danger">{{$message}}</small>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.slug')</label>
                <input type="text" aria-label="@lang('site.slug')"
                       placeholder="@lang('site.please_enter_slug')"
                       value="{{old('slug_ar')}}" name="slug_ar">
                @error("slug_ar")
                <small class="form-text text-danger">{{$message}}</small>
                @enderror
            </div>
            <div class="input-group">
                <label for="">@lang('site.meta_description')</label>
                <div id="" class="editor" style="height: 180px;">
                    <p>
                        @if(old('meta_desc_ar')){{old('meta_desc_ar')}} @else @lang('site.write_something') @endif
                    </p>
                </div>
                <input type="hidden" name="meta_desc_ar" id="" value="{{ old('meta_desc_ar')}}">
                @error("meta_desc_ar")
                <small class="form-text text-danger">{{$message}}</small>
                @enderror
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-navy min-width-170 mt-4" title="@lang('site.save')">@lang('site.save')</button>
</form>
@endsection

@section('scripts')
    <!-- Quill Editor -->
    <script src='{{asset('assets/tiny/js/quill.min.js')}}'></script>
    <script>
        var editors = document.querySelectorAll('.editor');
        if (editors) {
            var toolbarOptions = [
                [
                    {
                        'font': []
                    }
                ],
                [
                    {
                        'header': [1, 2, 3, 4, 5, 6, false]
                    }
                ],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote'],
                [
                    {
                        'list': 'ordered'
                    },
                    {
                        'list': 'bullet'
                    }
                ],
                [
                    {
                        'indent': '-1'
                    },
                    {
                        'indent': '+1'
                    }
                ], // outdent/indent
                ['clean'] // remove formatting button
            ];
            editors.forEach(editor => {
                var quill = new Quill(editor, {
                    modules:
                        {
                            toolbar: toolbarOptions
                        },
                    theme: 'snow'
                });
                quill.on('text-change', function() {
                    var content = quill.getText();
                    var input = editor.closest('.input-group').querySelector('input[type="hidden"]');
                    input.value = content;
                });
            });
        }
    </script>
@endsection
