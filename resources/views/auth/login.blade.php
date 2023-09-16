<!DOCTYPE html>
<html lang="">
<head>
    <style>
        body {
            margin: 0;
            background: #0d1021;
            height: 100vh;
            display: grid;
            place-items: center;
        }

        :root {
            --font-color: #00ccfe;
        }

        .main {
            position: relative;
            overflow: hidden;
            border-radius: 50px 0px 50px 0px;
        }

        .main span:nth-child(1) {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(to right, #2e0541, #870895);
            animation: animate1 2s linear infinite;
        }

        @keyframes animate1 {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }

        .main span:nth-child(2) {
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(to bottom, #2e0541, #2e0541);
            animation: animate2 2s linear infinite;
            animation-delay: 1s;
        }

        @keyframes animate2 {
            0% {
                transform: translateY(-100%);
            }
            100% {
                transform: translateY(100%);
            }
        }

        .main span:nth-child(3) {
            position: absolute;
            bottom: 0;
            right: 0;
            height: 3px;
            width: 100%;
            background: linear-gradient(to left, #215b83, #0a5b78);
            animation: animate3 2s linear infinite;

        }

        @keyframes animate3 {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }

        .main span:nth-child(4) {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(to top, #009fc9, #0a5b78);
            animation: animate4 2s linear infinite;
            animation-delay: 1s;

        }

        @keyframes animate4 {
            0% {
                transform: translateY(100%);
            }
            100% {
                transform: translateY(-100%);
            }
        }

        a {
            text-decoration: none;
            color: var(--font-color);
        }

        .card {
            font-family: sans-serif;
            width: 300px;
            border-radius: 50px 0px 50px 0px;
            background-color: transparent;
            padding: 1.8rem;
        }

        .title {
            text-align: center;
            color: var(--font-color);
            margin: 0;
        }

        .subtitle {
            text-align: center;
            color: white
        }

        .email-login {
            display: flex;
            flex-direction: column;
        }

        .email-login label {
            color: var(--font-color);
            margin-top: 10px;
        }

        input[type="text"],
        input[type="password"] {
            padding: 15px 20px;
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .cta-btn {
            background-color: var(--font-color);
            color: white;
            padding: 18px 20px;
            margin-top: 10px;
            margin-bottom: 20px;
            width: 100%;
            border-radius: 10px;
            border: none;
        }

        .forgot-pass {
            text-align: center;
            display: block;
        }
    </style>
    <title>تسجيل دخول</title>

</head>
<body dir="rtl">

<div class="main">
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <div class="card">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <h2 class="title">تسجيل الدخول</h2>
            <br/>
            <div class="email-login">
                <label for="username">إسم المستخدم</label>
                <input id="username" type="text" placeholder="إسم المستخدم" class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username') }}" name="username">
                @error('username')
                   <strong style="color: red">{{ $message }}</strong>
                @enderror
                <label for="password">كلمة المرور</label>
                <input id="password" type="password" placeholder="كلمة المرور" class="@error('password') is-invalid @enderror"
                       value="{{ old('password') }}" name="password">
                @error('password')
                <strong style="color: red">{{ $message }}</strong>
                @enderror
            </div>
            <button class="cta-btn" type="submit">
                تسجيل دخول
            </button>
        </form>
    </div>
</div>
</body>
</html>


{{--@extends('layouts.app')--}}


{{--@section('content')--}}
{{--    <div class="container">--}}
{{--        <div class="row justify-content-center">--}}
{{--            <div class="col-md-8">--}}
{{--                <div class="card">--}}

{{--                    <div class="card-body">--}}
{{--                        <form method="POST" action="{{ route('login') }}">--}}
{{--                            @csrf--}}

{{--                            <div class="form-group row align-items-center">--}}


{{--                                <div class="col-md-6">--}}
{{--                                    <label for="username">{{ __('Username') }}</label>--}}
{{--                                    <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username">--}}

{{--                                    @error('username')--}}
{{--                                    <span class="invalid-feedback" role="alert">--}}
{{--                                    <strong>{{ $message }}</strong>--}}
{{--                                </span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="row mb-3">--}}
{{--                                <div class="col-md-6">--}}
{{--                                    <label for="password">{{ __('Password') }}</label>--}}
{{--                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">--}}

{{--                                    @error('password')--}}
{{--                                    <span class="invalid-feedback" role="alert">--}}
{{--                                        <strong>{{ $message }}</strong>--}}
{{--                                    </span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="row mb-3">--}}
{{--                                <div class="col-md-6 offset-md-4">--}}
{{--                                    <div class="form-check">--}}
{{--                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>--}}

{{--                                        <label class="form-check-label" for="remember">--}}
{{--                                            {{ __('Remember Me') }}--}}
{{--                                        </label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="row mb-0">--}}
{{--                                <div class="col-md-8 offset-md-4">--}}
{{--                                    <button type="submit" class="btn btn-primary">--}}
{{--                                        {{ __('Login') }}--}}
{{--                                    </button>--}}

{{--                                    @if (Route::has('password.request'))--}}
{{--                                        <a class="btn btn-link" href="{{ route('password.request') }}">--}}
{{--                                            {{ __('Forgot Your Password?') }}--}}
{{--                                        </a>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </form>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endsection--}}
