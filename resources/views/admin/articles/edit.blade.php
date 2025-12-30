@extends('layouts.admin.app')
@section('title',' | '. __('site.Add Article'))
@section('styles')
    <!-- Quill Editor -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/quill.snow.css')}}">
    <link rel="stylesheet" href="{{asset('css/dropzone.css')}}">
    <link rel="stylesheet" href="{{asset('css/uppy.min.css')}}">
    <style>
        .image-container {
            position: relative;
            display: inline-flex;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 0.675rem;
            background-color: #FFF;
            margin: 0 15px 0 15px;
        }

        .delete-artical-image {
            position: absolute;
            cursor: pointer;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background-color: #FFF;
            box-shadow: 0 .5rem 1.5rem .5rem rgba(0, 0, 0, .075) !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #757575;
            transition: all 0.3s ease-in-out;
            font-size: .9rem;
        }
    </style>
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/daterangepicker.css')}}">
@endsection
@section('content')
    <!--=== Start Main ===-->

    <h2 class="section-title mb-5">@lang('site.Add Article')</h2>
    @foreach($article_images as $imageUrl)
        <div class="image-container">
            <img width="100" height="100" src="{{ asset('./images/articles/' . $imageUrl->name) }}" alt="Image">
            <form action="{{ route('admin.articles.removeimg', $imageUrl->id) }}" method="POST">
                @method('DELETE')
                @csrf
                <button type="submit" class="delete-artical-image"><i class="fal fa-times"></i></button>
            </form>
        </div>
    @endforeach
    <form action="{{route('admin.articles.update',$articles->id)}}" method="post" enctype="multipart/form-data" >
        @csrf
        @method('put')
        <div
            class="row">
            <div class="col-lg-6 col-12">
                <div class="dropzone-field">
                    <label for="">@lang('site.upload pictures')</label>
                    <div class="drag-drop-area" id="drag-drop-area1">

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 input-group">
                <label for="date-input1">@lang('site.date')</label>
                <input type="text" class="form-control drgpicker" id="date-input1" name="article_date" value="{{$articles->article_date}}"
                        placeholder="Please Select Date" aria-describedby="button-addon2">
            </div>
            <div class="col-6 input-group">
                <label for="date-input1">@lang('site.Articles Categories')</label>
                <div class="input-group">
                    <select name="category_id" class="form-control select2 no-search fe-14" id="simple-select4">
                        @foreach($categories as $categories)
                            <option value="{{$categories->id}}" @if($categories->id == $articles->category_id ) selected @else @endif  >{{$categories->category_en}}</option>
                        @endforeach
                    </select>
                </div>
                @error("category_id")
                <small class="form-text text-danger">{{$message}}</small>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6 col-lg-12 co-12">
                <h4 class="inner-title mb-4">@lang('site.Arabic')</h4>
                <div class="input-group">
                    <label for="">@lang('site.Article Title')</label>
                    <input value="{{$articles->article_ar}}" type="text" name="article_ar" id="" placeholder="@lang('site.placeholder_article_title')">
                    @error("article_ar")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.Article Description')</label>
                    <textarea class="textarea" id="article_desc_ar" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="article_desc_ar">
                        {{$articles->article_desc_ar}}
                    </textarea>
                    @error("article_desc_ar")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.meta_title')</label>
                    <input value="{{$articles->meta_title_ar}}" type="text" name="meta_title_ar" id="meta_title_ar" placeholder="@lang('site.Please Enter Meta title')">
                    @error("meta_title_ar")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.slug')</label>
                    <input value="{{$articles->tage_ar}}" type="text" name="tage_ar" id="tage_ar" placeholder="@lang('site.Please Enter Slug')">
                    @error("tage_ar")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.meta_description')</label>
                    <textarea class="textarea" id="meta_desc_ar" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="meta_desc_ar">
                        {{$articles->meta_desc_ar}}
                    </textarea>
                    @error("meta_desc_ar")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
            </div>
            <div class="col-xl-6 col-lg-12 co-12">
                <h4 class="inner-title mb-4">@lang('site.English')</h4>
                <div class="input-group">
                    <label for="">@lang('site.Article Title')</label>
                    <input value="{{$articles->article_en}}" type="text" name="article_en" id="article_en" placeholder="@lang('site.PLease Enter Article Title')">
                    @error("article_en")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.Article Description')</label>
                    <textarea class="textarea" id="article_desc_en" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="article_desc_en">
                        {{$articles->article_desc_en}}
                    </textarea>
                    @error("article_desc_en")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.meta_title')</label>
                    <input value="{{$articles->meta_title_en}}" type="text" name="meta_title_en" id="meta_title_en" placeholder="@lang('site.Please Enter Meta title')">
                    @error("meta_title_en")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.slug')</label>
                    <input value="{{$articles->tage_en}}" type="text" name="tage_en" id="tage_en" placeholder="@lang('site.Please Enter Slug')">
                    @error("tage_en")
                    <small class="form-text text-danger">{{$message}}</small>
                    @enderror
                </div>
                <div class="input-group">
                    <label for="">@lang('site.meta_description')</label>
                    <textarea class="textarea" id="meta_desc_en" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="meta_desc_en">
                        {{$articles->meta_desc_en}}
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
    <!-- DateRangePicker JS -->
    <script src="{{asset('assets/tiny/js/daterangepicker.js')}}"></script>
    <script>
        $('.drgpicker').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    </script>
    <!-- Dropzones -->
    <script src="{{asset('js/dropzone.min.js')}}"></script>
    <script src="{{asset('js/uppy.min.js')}}"></script>
    <script>
        var uptarg = document.querySelectorAll('.drag-drop-area');
        var selectedFiles = []; // Array to store selected file objects

        if (uptarg) {
            uptarg.forEach(area => {
                var uppy = Uppy.Core().use(Uppy.Dashboard,
                    {
                        inline: true,
                        target: area,
                        proudlyDisplayPoweredByUppy: false,
                        theme: 'dark',
                        width: 770,
                        height: 190,
                        plugins: ['Webcam']
                    }).use(Uppy.Tus,
                    {
                        endpoint: 'https://master.tus.io/files/'
                    });

                uppy.on('complete', (result) => {
                    const files = result.successful;
                    const newElement = document.getElementsByClassName('dropzone-field')[0];

                    for (let i = 0; i < files.length; i++) {
                        const file = files[i].data; // Access the file object using file.data
                        const fileInput = document.createElement('input');
                        console.log(file);

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);

                        fileInput.type = 'file';
                        fileInput.name = 'images[]';
                        fileInput.files = dataTransfer.files;

                        newElement.appendChild(fileInput);
                    }

                    console.log('Upload complete! We’ve uploaded these files:', files);
                });

            });
        }

    </script>


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
    <script src="//cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>
    <script>
        if(document.getElementById('meta_desc_ar'))
            CKEDITOR.replace('meta_desc_ar', {
                filebrowserUploadMethod: 'form'
            });

        if(document.getElementById('article_desc_ar'))
            CKEDITOR.replace('article_desc_ar', {
                filebrowserUploadMethod: 'form'
            });


        if(document.getElementById('meta_desc_en'))
            CKEDITOR.replace('meta_desc_en', {
                filebrowserUploadMethod: 'form'
            });


        if(document.getElementById('article_desc_en'))
            CKEDITOR.replace('article_desc_en', {
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


