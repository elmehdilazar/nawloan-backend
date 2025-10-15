@extends('layouts.login')
@section('title', ' | ' . __('site.login'))
@section('styles')
    {{-- <link rel="stylesheet" href="{{asset('assets/css/login.css')}}"> --}}
@endsection
@section('content')
    <!--=== Start Wrapper ===-->
    <div class="wrapper auth-wrapper">
        <div class="auth-background">
            <img src="{{ asset('assets/images/logo-light.png') }}" class="logo" alt="">
            <img src="{{ asset('assets/images/auth.jpg') }}" alt="">
        </div>
        <div class="auth-field">
            <div class="form-group flex-column">
                <form method="POST" action="{{ route('login') }}" class="login100-form validate-form">
                    @csrf
                    <div class="form-title text-center mb-5">
                        <h2 class="mb-3">@lang('site.welcome_to_nawloan')</h2>
                        <p class="mb-0">@lang('site.welcome_back')</p>
                        @if (session('errors'))
                            <div class="alert alert-danger" role="alert">
                                @lang('site.invalid_credentials')
                            </div>
                        @endif
                    </div>
                    <div class="input-group">
                        <label for="intl_phone">{{ __('site.phone') }}</label>
                        <div class="international-phone gray">
                            <div class="position-relative">
                                <input type="tel" id="intl_phone" class="form-control phone"
                                    placeholder="@lang('site.phone')" value="{{ old('phone') }}">
                                <img src="{{ asset('assets/images/svgs/perm-phone.svg') }}" alt="" class="icon">
                            </div>
                        </div>
                        <input type="hidden" name="phone" id="phone">
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="input-group mb-0">
                        <label for="password">{{ __('site.password') }}</label>
                        <div class="position-relative">
                            <input id="password" type="password"
                                class="form-control icon-winput @error('password') is-invalid @enderror"
                                placeholder="{{ __('site.password') }}" name="password" required
                                autocomplete="current-password">
                            <img src="{{ asset('assets/images/svgs/lock.svg') }}" alt="" class="icon">
                        </div>
                    </div>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="show_password">
                        <label class="form-check-label" for="show_password">
                            {{ __('site.show_password') }}
                        </label>
                    </div>
                    <div class="flex-space mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('site.remember_me') }}
                            </label>
                        </div>

                        <a href="#">Forgot Password</a>
                    </div>
                    <div class="flex-col-center mt-5">
                        <button type="submit" class="btn btn-navy shadow-none">{{ __('site.login') }}</button>
                        {{--                        @if (Route::has('register')) --}}
                        {{--                        <a href="{{ route('register') }}" class="btn btn-transparent navy mt-4 shadow-none">{{ __('site.register') }}</a> --}}
                        {{--                        @endif --}}
                    </div>
                </form>
            </div>
            <a href="#">Term of use. Privacy policy</a>
        </div>
    </div>
    <!--=== End Wrapper ===-->
    {{-- <div class="limiter"> --}}
    {{--    <div class="container-login100"> --}}
    {{--        <div class="wrap-login100"> --}}
    {{--                <form method="POST" action="{{ route('login') }}"class="login100-form validate-form"> --}}
    {{--                    @csrf --}}
    {{--                    <span class="login100-form-title p-b-43" style="color:#4669b2;"> --}}
    {{--                        @lang('site.welcome_to_nawloan') --}}
    {{--                    </span> --}}
    {{--                    <span class="login100-form-title p-b-43 text-muted" style="font-size: 15px;"> --}}
    {{--                        @lang('site.welcome_back') --}}
    {{--                    </span> --}}
    {{--                    @if (session('errors')) --}}
    {{--                    <div class="alert alert-danger" role="alert"> --}}
    {{--                        @lang('site.invalid_credentials') --}}
    {{--                    </div> --}}
    {{--                    @endif --}}

    {{--                    <div class="row mb-2 "> --}}
    {{--                        <label for="login" class="col-form-label text-md-end">{{ __('site.phone') }}</label> --}}
    {{--                        <div class="input-group"> --}}
    {{--                            <input type="tel" class="form-control phone"  placeholder="@lang('site.phone')" --}}
    {{--                                value="{{old('phone')}}"> --}}
    {{--                            <span class="input-group-append"> --}}
    {{--                                <div class="input-group-text bg-transparent"><i class="fas fa-phone"></i></div> --}}
    {{--                            </span> --}}
    {{--                        </div> --}}
    {{--                        <input type="hidden" name="phone" id="phone"> --}}
    {{--                        @error('phone') --}}
    {{--                        <span class="invalid-feedback" role="alert"> --}}
    {{--                            <strong>{{ $message }}</strong> --}}
    {{--                        </span> --}}
    {{--                        @enderror --}}
    {{--                    </div> --}}

    {{--                    <div class="row mb-2"> --}}
    {{--                        <label for="password" class="col-form-label text-md-end">{{ __('site.password') }}</label> --}}

    {{--                        <div class="input-group"> --}}
    {{--                        <input id="password" type="password" --}}
    {{--                            class="form-control icon-winput @error('password') is-invalid @enderror" --}}
    {{--                            placeholder="{{ __('site.password')}}" name="password" required --}}
    {{--                            autocomplete="current-password"> --}}
    {{--                            <span class="input-group-append"> --}}
    {{--                                <div class="input-group-text bg-transparent"><i class="fa fa-lock"></i></div> --}}
    {{--                            </span> --}}
    {{--                    </div> --}}

    {{--                        @error('password') --}}
    {{--                        <span class="invalid-feedback" role="alert"> --}}
    {{--                            <strong>{{ $message }}</strong> --}}
    {{--                        </span> --}}
    {{--                        @enderror --}}
    {{--                    </div> --}}

    {{--                    <div class="row mb-2 d-flex justify-content-between"> --}}
    {{--                        <div class="form-check" style="padding-top: 0.5rem;"> --}}
    {{--                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{old('remember') ? 'checked' : ''}}> --}}
    {{--                            <label class="form-check-label" for="remember"> --}}
    {{--                                {{ __('site.remember_me') }} --}}
    {{--                            </label> --}}
    {{--                        </div> --}}
    {{--                        @if (Route::has('password.request')) --}}
    {{--                        <a class="btn btn-link" href="{{ route('password.request') }}"> --}}
    {{--                            {{ __('site.forget_password') }} --}}
    {{--                        </a> --}}
    {{--                        @endif --}}
    {{--                    </div> --}}

    {{--                    <div class="row mb-0 justify-content-center" > --}}
    {{--                        <div class="col-md-6"style="display: grid;"> --}}
    {{--                        <button type="submit" class="btn btn-primary mb-1"> --}}
    {{--                            {{ __('site.login') }} --}}
    {{--                        </button> --}}
    {{--                         @if (Route::has('register')) --}}
    {{--                        <a class="btn btn-outline-primary mb-1" href="{{ route('register') }}"> --}}
    {{--                            {{ __('site.register') }} --}}
    {{--                        </a> --}}
    {{--                        @endif --}}
    {{--                        </div> --}}
    {{--                    </div> --}}
    {{--                </form> --}}
    {{--                <div class="login100-more" style="background-image: url({{asset('uploads/img/login.png')}});"> --}}
    {{--				</div> --}}
    {{--			</div> --}}
    {{--		</div> --}}
    {{--	</div> --}}
    {{-- </div> --}}
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('show_password');
    var input  = document.getElementById('password');
    if (toggle && input) {
        toggle.addEventListener('change', function () {
            input.type = this.checked ? 'text' : 'password';
        });
    }
});
</script>