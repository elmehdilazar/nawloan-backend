@extends('layouts.admin.app')
@section('title',' | '. __('site.create_coupons'))
@section('styles')
    <!-- Quill Editor -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/quill.snow.css')}}">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/daterangepicker.css')}}">
@endsection
@section('content')
    <!--=== Start Main ===-->

    <h2 class="section-title mb-5">@lang('site.create_coupons')</h2>
    @if(session('error_message'))
        <div class="alert alert-danger">{{ session('error_message') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{route('admin.coupons.store')}}" method="post">
        @csrf
        <div class="row">
                <div class="col-xl-7 col-lg-9 co-12">
                    <div class="input-group">
                        <label for="">@lang('site.coupon_name')</label>
                        <input required type="text" name="name" id="name" placeholder="@lang('site.placeholder_coupon_name')">
                       @error("name")
                        <small class="form-text text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-group">
                        <label for="">@lang('site.coupon_code')</label>
                        <input required type="text" name="code" id="code" placeholder="@lang('site.placeholder_coupon_code')">
                        @error("code")
                        <small class="form-text text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-group">
                        <label for="desc_ar">@lang('site.coupon_description_ar')</label>
                        <textarea name="desc_ar" id="desc_ar" rows="3" placeholder="@lang('site.placeholder_coupon_description_ar')">{{ old('desc_ar') }}</textarea>
                        @error('desc_ar')
                        <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="input-group">
                        <label for="desc_en">@lang('site.coupon_description_en')</label>
                        <textarea name="desc_en" id="desc_en" rows="3" placeholder="@lang('site.placeholder_coupon_description_en')">{{ old('desc_en') }}</textarea>
                        @error('desc_en')
                        <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="input-group">
                        <label for="">@lang('site.number_available')</label>
                        <input required type="number" min="0" max="1000" name="number_availabe" id="number_availabe" placeholder="@lang('site.placeholder_number_available')">
                        @error("number_availabe")
                        <small class="form-text text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-group">
                        <label for="date-input1">@lang('site.date_range')</label>
                        <div class="flex-align-center gap-15">
                            <div class="col p-0">
                                <input type="text" name="start_date" class="form-control drgpicker">
                                @error("start_date")
                                <small class="form-text text-danger">{{$message}}</small>
                                @enderror
                            </div>
                            <div class="col p-0">
                                <input type="text" name="expiry_date" class="form-control drgpicker">
                                @error("expiry_date")
                                <small class="form-text text-danger">{{$message}}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <h2 class="section-title mt-3 mb-4">@lang('site.discount_details')</h2>
                    <div class="radio-group mb-1">
                        <label for="">@lang('site.coupon_type')</label>
                        <ul class="radio-list">
                            <li>
                                <div class="form-radio">
                                    <input value="percentage" class="radio-input" name="type" type="radio" id="flexRadio1"
                                           checked>
                                    <label class="radio-label" for="flexRadio1">% @lang('site.percentage')</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-radio">
                                    <input value="fixed" class="radio-input" name="type" type="radio" id="flexRadio2">
                                    <label class="radio-label" for="flexRadio2">@lang('site.fixed_price')</label>
                                </div>
                            </li>
                        </ul>
                        @error("type")
                        <small class="form-text text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="input-group">
                        <input required type="number" min="0" name="discount" id="discount" placeholder="0" >
                        @error("discount")
                        <small class="form-text text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="radio-group">
                        <label for="">@lang('site.applies_to')</label>
                        <ul class="radio-list">
                            <li>
                                <div class="form-radio">
                                    <input value="customer" class="radio-input" name="apply_to" type="radio" id="flexRadio3" checked>
                                    <label class="radio-label"  for="flexRadio3">@lang('site.customer')</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-radio">
                                    <input value="enterprise" class="radio-input" name="apply_to" type="radio" id="flexRadio4">
                                    <label class="radio-label" for="flexRadio4">@lang('site.enterprise')</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-radio">
                                    <input value="all" class="radio-input" name="apply_to" type="radio" id="flexRadio5">
                                    <label class="radio-label" for="flexRadio5">@lang('site.all')</label>
                                </div>
                            </li>
                        </ul>
                        @error("apply_to")
                        <small class="form-text text-danger">{{$message}}</small>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-navy shadow-none min-width-170 mt-4">@lang('site.add')</button>
                </div>
            </div>
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
    <!-- Quill Editor -->
    <script src="{{asset('assets/tiny/js/quill.min.js')}}"></script>
@endsection
