<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @livewireStyles
    <title>نظام إدارة كاشير</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/table.css') }}"/>


</head>
<body dir="rtl" lang="ar">
<livewire:navbar/>

<div class="container-fluid">
    {{ $slot }}
</div>

@livewireScripts
<script src="{{asset('js/sweetalert2.js')}}"></script>
<x-livewire-alert::scripts />
{{--<script src="{{ asset('vendor/livewire-alert/livewire-alert.js') }}"></script>--}}
<x-livewire-alert::flash />
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="{{asset('js/printThis.js')}}"></script>
<script src="{{asset('js/scripts.js')}}"></script>
</body>
</html>
