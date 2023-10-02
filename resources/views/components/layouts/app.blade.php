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
</body>
</html>
