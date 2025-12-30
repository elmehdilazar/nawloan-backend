@extends('layouts.admin.app')
@section('title',' | '. __('site.Add Article Category'))
@section('styles')
    <!-- Quill Editor -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/quill.snow.css')}}">
    <style>
        input[type="date"].form-control{
            width: 45%;
            transition: all .5s;
            font-size: 16px;
            letter-spacing: 0.36px;
            color: #777575;
            text-transform: capitalize;
            border-radius: 3px;
            padding: 0 24px;
            height: 48px;
            background: #FFF;
            border: 1px solid #D8DAD9;
            border-radius: 16px !important;

        }
    </style>

@endsection
@section('content')
    <!--=== Start Main ===-->
    <form action="{{route('admin.article_categories.store')}}" method="post">
        @csrf
        @method('post')
        <h2 class="section-title mb-5">@lang('site.Add Article Category')</h2>
        <div class="row">
            <div class="col-xl-6 col-lg-12 co-12">
                <h4 class="inner-title mb-4">@lang('site.Arabic')</h4>
                <div class="input-group">
                    <label for="">@lang('site.Name Of Category')</label>
                    <input  type="text" name="category_ar" id="category_ar" placeholder="@lang('site.PLease Enter Name Of Category')">
                    @error("category_ar")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.desc')</label>
                    <textarea class="textarea" id="category_desc_ar" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="category_desc_ar">
                    </textarea>
                    @error("category_desc_ar")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.meta_title')</label>
                    <input type="text" name="meta_title_ar" id="meta_title_ar" placeholder="@lang('site.Please Enter Meta title')">
                    @error("meta_title_ar")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.slug')</label>
                    <input  type="text" name="slug_ar" id="slug_ar" placeholder="@lang('site.Please Enter Slug')">
                    @error("slug_ar")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.desc')</label>
                    <textarea class="textarea" id="meta_desc_ar" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="meta_desc_ar">
                    </textarea>
                    @error("meta_desc_ar")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
            </div>
            <div class="col-xl-6 col-lg-12 co-12">
                <h4 class="inner-title mb-4">@lang('site.English')</h4>
                <div class="input-group">
                    <label for="">@lang('site.Name Of Category')</label>
                    <input  type="text" name="category_en" id="category_en" placeholder="@lang('site.PLease Enter Name Of Category')">
                    @error("category_en")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.desc')</label>
                    <textarea class="textarea" id="category_desc_en" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="category_desc_en">
                    </textarea>
                    @error("category_desc_en")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.meta_title')</label>
                    <input  type="text" name="meta_title_en" id="meta_title_en" placeholder="@lang('site.Please Enter Meta title')">
                    @error("meta_title_en")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.slug')</label>
                    <input  type="text" name="slug_en" id="slug_en" placeholder="@lang('site.Please Enter Slug')">
                    @error("slug_en")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.desc')</label>
                    <textarea class="textarea" id="meta_desc_en" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="meta_desc_en">
                    </textarea>
                    @error("meta_desc_en")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-navy min-width-170 mt-4">@lang('site.add')</button>
    </form>


    <!--=== End Main ===-->
@endsection


@section('scripts')
    <!-- Custom ImageInput -->
    <script src="{{asset('assets/js/custom-imageInput.js')}}"></script>
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
    <!-- IntlTelInput -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.7/js/intlTelInput.js"></script>
    <script src="//cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        if(document.getElementById('meta_desc_ar'))
            CKEDITOR.replace('meta_desc_ar', {
                filebrowserUploadMethod: 'form'
            });

        if(document.getElementById('category_desc_en'))
            CKEDITOR.replace('category_desc_en', {
                filebrowserUploadMethod: 'form'
            });


        if(document.getElementById('meta_desc_en'))
            CKEDITOR.replace('meta_desc_en', {
                filebrowserUploadMethod: 'form'
            });


        if(document.getElementById('category_desc_ar'))
            CKEDITOR.replace('category_desc_ar', {
                filebrowserUploadMethod: 'form'
            });

    </script>

    <script>
        $(document).ready(function(){
            let country_codes= <?php echo json_encode( \App\Models\Country::select('country_code')->get()); ?>;
            let countries=[];
            for(var i=0;i<country_codes.length;i++){
                countries.push(country_codes[i].country_code);
            }
            $(".phone").intlTelInput({
                rtl: true,
                initialCountry: "eg",
                autoHideDialCode:false,
                allowDropdown:false,
                nationalMode: true,
                numberType: "MOBILE",
                onlyCountries:countries,// ['us', 'gb', 'ch', 'ca', 'do'],
                preferredCountries:['eg','sa','ue'],// ['sa', 'ae', 'qa','om','bh','kw','ma'],
                preventInvalidNumbers: true,
                separateDialCode: true ,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/js/utils.js",
            });

            $(".phone").on('change',function(e){
                e.preventDefault();
                $('#phone').val($(".phone").intlTelInput("getNumber"));
            });
            $(".intl-tel-input .country-list").on('click',function(e){
                e.preventDefault();
                $('#phone').val($(".phone").intlTelInput("getNumber"));
            });

            $(".intl-tel-input.allow-dropdown .flag-container").on('click',function(e){
                // e.stopPropagation();
                $(this).toggleClass("dropdown-opened");
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.intl-tel-input.allow-dropdown .flag-container').length) {
                    $('.intl-tel-input.allow-dropdown .flag-container').removeClass('dropdown-opened');
                }
            });
        });
    </script>
@endsection



