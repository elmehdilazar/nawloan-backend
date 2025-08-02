@extends('layouts.admin.app')
@section('title',' | ' .  __('site.account_edit'))
@section('styles')
    <!-- IntlTelInput -->
    <link rel="stylesheet" href="{{asset('assets/css/flag-icon-css/flag-icon.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/css/intlTelInput.css" rel="stylesheet">
@endsection
@section('content')
<h2 class="section-title mb-5">@lang('site.account_edit')</h2>
<div class="row">
    <div class="col-xl-6 col-lg-8 co-12">
        <form action="{{route('admin.account.update',$user->id)}}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="image-input">
                        <label for="">Profile Picture</label>
                        <div class="imageUpload-wrapper"
                             style="background-image: url({{asset('assets/images/no-image.jpg')}})">
                            <div id="imageUpload" style="background-image: url({{$user->userData->image !='' ? asset($user->userData->image) : asset('uploads/users/default.png')}})">
                                <input type="file" name="image" id="image" class="mediaFile">
                                <label for="image"><i class="fad fa-pencil"></i></label>
                                <button id="clear-input"><i class="fal fa-times"></i></button>
                            </div>
                        </div>
                        @error('image')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="input-group">
                <label for="">@lang('site.name')</label>
                <div class="position-relative">
                    <img src="{{asset('assets/images/svgs/person-fill-bland.svg')}}" alt="">
                    <input type="text" name="name" id="name"
                           placeholder="@lang('site.name')" aria-describedby="helpName"
                           value="{{$user->name}}">
                </div>
                @error('name')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="intl_phone">@lang('site.phone')</label>
                <div class="international-phone gray">
                    <div class="position-relative">
                        <input type="tel" id="intl_phone" class="form-control phone"
                               placeholder="@lang('site.please_enter') @lang('site.phone')" value="{{$user->phone}}">
                        <img src="{{asset('assets/images/svgs/perm-phone.svg')}}" alt="" class="icon">
                    </div>
                </div>
                <input type="hidden" name="phone" id="phone" value="{{$user->phone}}">
                @error('phone')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-navy shadow-none min-width-170 mt-4">@lang('site.save')</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Custom ImageInput -->
    <script src="{{asset('assets/js/custom-imageInput.js')}}"></script>
    <!-- IntlTelInput -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.7/js/intlTelInput.js"></script>
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
