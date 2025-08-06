@extends('layouts.admin.app')
@section('title',' | ' . __('site.general_settings'))
@section('styles')
    <!-- Quill Editor -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/quill.snow.css')}}">
    <!-- IntlTelInput -->
    <link rel="stylesheet" href="{{asset('assets/css/flag-icon-css/flag-icon.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/css/intlTelInput.css" rel="stylesheet">
@endsection
@section('content')
<h2 class="section-title mb-4">@lang('site.general_settings')</h2>
<form action="{{route('admin.setting.general.store')}}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('post')
    <div class="row">
        <div class="col-sm-6 co-12">
            <div class="image-input">
                <label for="">@lang('site.logo')</label>
                <div class="imageUpload-wrapper"
                     style="background-image: url({{asset('assets/images/no-image.jpg')}})">
                    <div id="imageUpload" class="contain dark" style="background-image: url({{setting('logo') != '' ? asset( setting('logo')) : asset('uploads/img/logo.png') }})">
                        <input type="file" name="logo" id="logo" class="mediaFile">
                        <label for="logo"><i class="fad fa-pencil"></i></label>
                        <button id="clear-input"><i class="fal fa-times"></i></button>
                    </div>
                </div>
                @error('logo')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6 co-12">
            <div class="image-input">
                <label for="">@lang('site.favoico')</label>
                <div class="imageUpload-wrapper"
                     style="background-image: url({{asset('assets/images/no-image.jpg')}})">
                    <div id="imageUpload" class="contain dark" style="background-image: url({{setting('favoico') != '' ? asset(setting('favoico')) : asset('uploads/img/logo.png') }})">
                        <input type="file" name="favoico" id="favoico" class="mediaFile">
                        <label for="favoico"><i class="fad fa-pencil"></i></label>
                        <button id="clear-input"><i class="fal fa-times"></i></button>
                    </div>
                </div>
                @error('favoico')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="app_name_ar">@lang('site.app_name_ar')</label>
                <input type="text" name="app_name_ar" id="app_name_ar"
                       placeholder="@lang('site.site_name')"
                       value="{{setting('app_name_ar')!=''?setting('app_name_ar'):''}}">
                @error('app_name_ar')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="app_name_en">@lang('site.app_name_en')</label>
                <input type="text" name="app_name_en" id="app_name_en"
                       placeholder="@lang('site.site_name')"
                       value="{{setting('app_name_en')!=''?setting('app_name_en'):''}}">
                @error('app_name_en')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="site_link">@lang('site.site_link')</label>
                <input type="url" name="site_link" id="site_link"
                       placeholder="@lang('site.site_link')"
                       value="{{setting('site_link')!=''?setting('site_link'):''}}">
                @error('site_link')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="email">@lang('site.Primary Email')</label>
                <input type="email" name="email" id="email"
                       placeholder="@lang('site.Primary Email')"
                       value="{{setting('email')!=''?setting('email'):''}}">
                @error('email')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="address_en">@lang('site.Address In English')</label>
                <input type="text" name="address_en" id="address_en"
                       placeholder="@lang('site.Address In English')"
                       value="{{setting('address_en')!='' ? setting('address_en'):''}}">
                @error('address_en')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="address_ar">@lang('site.Address In Arabic')</label>
                <input type="text" name="address_ar" id="address_ar"
                       placeholder="@lang('site.Address In Arabic')"
                       value="{{setting('address_ar')!=''?setting('address_ar'):''}}">
                @error('address_ar')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="currency">@lang('site.currency')</label>
                <input type="text" name="currency" id="currency"
                       placeholder="@lang('site.currency')"
                       value="{{setting('currency')!=''?setting('currency'):''}}">
                @error('currency')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="currency_atr">@lang('site.currency_atr')</label>
                <input type="text" name="currency_atr" id="currency_atr"
                       placeholder="@lang('site.currency_atr')"
                       value="{{setting('currency_atr')!='' ?setting('currency_atr') :''}}">
                @error('currency_atr')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 co-12">
            <div class="input-group">
                <label for="intl_phone">@lang('site.phone')</label>
                <div class="international-phone gray">
                    <div class="position-relative">
                        <input type="tel" id="intl_phone" class="form-control phone"
                               placeholder="@lang('site.phone')"
                               value="{{setting('phone')!='' ?setting('phone') : old('phone')}}">
                        <img src="{{asset('assets/images/svgs/perm-phone.svg')}}" alt="" class="icon">
                    </div>
                </div>
                <input type="hidden" name="phone" id="phone">
                @error('phone')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <h4 class="inner-title mt-3 mb-4">@lang('site.terms_conditions')</h4>
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="input-group">
                <label for="">@lang('site.customers')</label>
                <textarea class="textarea" id="customers_terms_conditions" placeholder="customers terms conditions" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="customers_terms_conditions">
                    {{setting('customers_terms_conditions') !='' ? setting('customers_terms_conditions') : old('customers_terms_conditions') }}
                </textarea>
                @error('customers_terms_conditions')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="input-group">
                <label for="">@lang('site.factories')</label>
                <textarea class="textarea" id="factories_terms_conditions" placeholder="factories terms conditions" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="factories_terms_conditions">
                    {{setting('factories_terms_conditions') !='' ? setting('factories_terms_conditions') : old('factories_terms_conditions') }}
                </textarea>
                @error('factories_terms_conditions')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
           <div class="col-lg-6 col-12">
            <div class="input-group">
                <label for="">@lang('site.customers')</label>
                <textarea class="textarea" id="customers_terms_conditions" placeholder="customers terms conditions" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="customers_terms_conditions_ar">
                    {{setting('customers_terms_conditions_ar') !='' ? setting('customers_terms_conditions_ar') : old('customers_terms_conditions_ar') }}
                </textarea>
                @error('customers_terms_conditions_ar')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="input-group">
                <label for="">@lang('site.factories')</label>
                <textarea class="textarea" id="factories_terms_conditions_ar" placeholder="factories terms conditions" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="factories_terms_conditions_ar">
                    {{setting('factories_terms_conditions_ar') !='' ? setting('factories_terms_conditions_ar') : old('factories_terms_conditions_ar') }}
                </textarea>
                @error('factories_terms_conditions_ar')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="input-group">
                <label for="">@lang('site.drivers')</label>
                <textarea class="textarea" id="drivers_terms_conditions" placeholder="drivers terms conditions" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="drivers_terms_conditions">
                    {{setting('drivers_terms_conditions') !='' ? setting('drivers_terms_conditions') : old('drivers_terms_conditions') }}
                </textarea>
                @error('drivers_terms_conditions')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="input-group">
                <label for="">@lang('site.shipping_company')</label>

                <textarea class="textarea" id="shipping_company_terms_conditions" placeholder="shipping_company_terms_conditions" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="shipping_company_terms_conditions">
                    {{setting('shipping_company_terms_conditions') !='' ? setting('shipping_company_terms_conditions') : old('shipping_company_terms_conditions') }}
                </textarea>
                @error('shipping_company_terms_conditions')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="input-group">
                <label for="">@lang('policy')</label>
                <textarea class="textarea" id="policy" placeholder="policy" style="width: 100%; height: 620px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="policy">
                    {{setting('policy') !='' ? setting('policy') : old('policy') }}
                </textarea>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-navy min-width-170 mt-4" title="@lang('site.save')">@lang('site.save')</button>
</form>
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
        <script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
    <script>
        if(document.getElementById('policy'))
            CKEDITOR.replace('policy', {
                filebrowserUploadMethod: 'form'
            });

        if(document.getElementById('customers_terms_conditions'))
            CKEDITOR.replace('customers_terms_conditions', {
                filebrowserUploadMethod: 'form'
            });


        if(document.getElementById('shipping_company_terms_conditions'))
            CKEDITOR.replace('shipping_company_terms_conditions', {
                filebrowserUploadMethod: 'form'
            });


        if(document.getElementById('drivers_terms_conditions'))
            CKEDITOR.replace('drivers_terms_conditions', {
                filebrowserUploadMethod: 'form'
            });


        if(document.getElementById('factories_terms_conditions'))
            CKEDITOR.replace('factories_terms_conditions', {
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
