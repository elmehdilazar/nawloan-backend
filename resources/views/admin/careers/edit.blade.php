@extends('layouts.admin.app')
@section('title',' | ' . __('site.edit') .' '. __('site.career'))
@section('styles')
    <!-- Quill Editor -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/quill.snow.css')}}">
@endsection
@section('content')
<h2 class="section-title mb-5">@lang('site.edit_career')</h2>
<form action="{{route('admin.careers.update', $career->id)}}" method="post">
    @csrf
    @method('put')
    <div class="row">
            <div class="col-xl-6 col-lg-12 co-12">
                <h4 class="inner-title mb-4">@lang('site.English')</h4>
                <div class="input-group">
                    <label for="">@lang('site.job_title')</label>
                    <input type="text" aria-label="@lang('site.job_title')"
                           placeholder="@lang('site.please_enter_job_title')"
                           value="{{$career->name_en}}" name="name_en">
                    @error("name_en")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.location')</label>
                    <input type="text" aria-label="@lang('site.location')"
                           placeholder="@lang('site.please_enter_location')"
                           value="{{$career->address_en}}" name="address_en">
                    @error("address_en")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="select-group">
                    <label for="simple-select2" class="">@lang('site.category')</label>
                    <select class="form-control select2" id="category_en" name="category_id">
                        <option value="" selected >@lang("site.choose_category")</option>
                        @foreach ($categories as $index => $category )
                            <option value="{{$category->id}}" {{ $career->category_id== $category->id ? 'selected' : '' }}> {{$category->category_en}}</option>
                        @endforeach
                    </select>
                    @error("category_id")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">English description</label>
                    <div id="" class="editor" style="height: 180px;">
                        <p>{{$career->desc_en}}</p>
                    </div>
                    <input type="hidden" name="desc_en" id="" value="{{$career->desc_en}}">
                    @error("desc_en")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
            </div>
            <div class="col-xl-6 col-lg-12 co-12">
                <h4 class="inner-title mb-4">@lang('site.Arabic')</h4>
                <div class="input-group">
                    <label for="">@lang('site.job_title')</label>
                    <input type="text" aria-label="@lang('site.job_title')"
                           placeholder="@lang('site.please_enter_job_title')"
                           value="{{$career->name_ar}}" name="name_ar">
                    @error("name_ar")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.location')</label>
                    <input type="text" aria-label="@lang('site.location')"
                           placeholder="@lang('site.please_enter_location')"
                           value="{{$career->address_ar}}" name="address_ar">
                    @error("address_ar")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="select-group">
                    <label for="simple-select2" class="">@lang('site.category')</label>
                    <select class="form-control select2" id="category_ar" name="category_id">
                        <option value="" selected >@lang("site.choose_category")</option>
                        @foreach ($categories as $index => $category )
                            <option value="{{$category->id}}" {{$career->category_id == $category->id ? 'selected' : '' }}> {{$category->category_ar}}</option>
                        @endforeach
                    </select>
                    @error("category_id")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">Arabic description</label>
                    <div id="" class="editor" style="height: 180px;">
                        <p>{{$career->desc_ar}}</p>
                    </div>
                    <input type="hidden" name="desc_ar" id="" value="{{$career->desc_ar}}">
                    @error("desc_ar")
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
