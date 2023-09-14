<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @livewireStyles
    <title>{{ $title ?? 'Page Title' }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

</head>
<body dir="rtl" lang="ar">

<livewire:navbar/>

<div class="container-fluid">
    {{ $slot }}
</div>
@livewireScripts
</body>

<script>
    $('#print').click(
        $('.printThis').printThis()
    );
</script>
</html>
