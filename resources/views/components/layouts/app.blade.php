<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @livewireStyles
    <title>نظام إدارة كاشير</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/table.css') }}"/>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }


        .dropdown .dropbtn {
            font-size: 16px;
            border: none;
            outline: none;
            color: white;
            background-color: inherit;
            font-family: inherit;
            margin: 0;
        }

        .dropdown:hover .dropbtn {
            color: yellow;
        }

        .dropdown-content {
            display: none;
            top: 30px;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            float: none;
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .dropdown-content a:hover {
            background-color: #ddd;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>

</head>
<body dir="rtl" lang="ar">
<livewire:navbar/>

<div class="container-fluid">
    {{ $slot }}
</div>
{{--<div id="idle-timer"--}}
{{--     style="position: fixed; bottom: 0; width: 100%; text-align: center; background-color: #f8d7da; padding: 10px;">--}}
{{--    الوقت المتبقي قبل تسجيل الخروج: <span id="timer-display"></span>--}}
{{--</div>--}}
@livewireScripts
<script src="{{asset('js/sweetalert2.js')}}"></script>
<x-livewire-alert::scripts/>
{{--<script src="{{ asset('vendor/livewire-alert/livewire-alert.js') }}"></script>--}}
<x-livewire-alert::flash/>
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="{{asset('js/printThis.js')}}"></script>
<script src="{{asset('js/scripts.js')}}"></script>

{{--<script>--}}
{{--    function initializeIdleTimer() {--}}
{{--        let idleTime = 0;--}}
{{--        const maxIdleTime = 5 * 60 * 1000; // 5 دقائق بالمللي ثانية--}}
{{--        const timerDisplay = document.getElementById('timer-display');--}}
{{--        const idleTimerElement = document.getElementById('idle-timer');--}}
{{--        let logoutTriggered = false; // منع تسجيل الخروج المتكرر--}}

{{--        // وظيفة لتحديث العداد--}}
{{--        function updateTimerDisplay() {--}}
{{--            const timeLeft = Math.max(0, maxIdleTime - idleTime);--}}
{{--            const minutes = Math.floor(timeLeft / 60000);--}}
{{--            const seconds = Math.floor((timeLeft % 60000) / 1000);--}}
{{--            timerDisplay.textContent = `${minutes} دقيقة و ${seconds} ثانية`;--}}

{{--            // إظهار العداد في آخر دقيقة فقط--}}
{{--            if (timeLeft <= 60000 && !logoutTriggered) {--}}
{{--                idleTimerElement.style.display = 'block'; // إظهار العداد--}}
{{--            } else {--}}
{{--                idleTimerElement.style.display = 'none'; // إخفاء العداد--}}
{{--            }--}}
{{--        }--}}

{{--        // وظيفة لإعادة تعيين الوقت عندما يكون هناك تفاعل من المستخدم--}}
{{--        function resetIdleTimer() {--}}
{{--            if (!logoutTriggered) {--}}
{{--                idleTime = 0;--}}
{{--                updateTimerDisplay();--}}
{{--            }--}}
{{--        }--}}

{{--        // وظيفة لتسجيل الخروج عند الوصول للحد الأقصى من الخمول--}}
{{--        function checkIdleTime() {--}}
{{--            idleTime += 1000; // إضافة ثانية (1000 مللي ثانية)--}}
{{--            updateTimerDisplay();--}}
{{--            if (idleTime >= maxIdleTime && !logoutTriggered) {--}}
{{--                logoutTriggered = true; // تأكيد تسجيل الخروج لتجنب التكرار--}}
{{--                document.getElementById('logout-form').submit();--}}
{{--            }--}}
{{--        }--}}

{{--        // تعيين مؤقت لفحص وقت الخمول كل ثانية--}}
{{--        setInterval(checkIdleTime, 1000);--}}

{{--        // الأحداث التي تعيد ضبط الوقت عند حدوث تفاعل--}}
{{--        window.onload = resetIdleTimer;--}}
{{--        window.onmousemove = resetIdleTimer;--}}
{{--        window.onkeypress = resetIdleTimer;--}}
{{--        window.onscroll = resetIdleTimer;--}}
{{--        window.onclick = resetIdleTimer;--}}

{{--        // فحص حالة رؤية الصفحة--}}
{{--        document.addEventListener('visibilitychange', function () {--}}
{{--            if (!document.hidden && !logoutTriggered) {--}}
{{--                resetIdleTimer(); // إذا عاد المستخدم إلى علامة التبويب الرئيسية، أعد ضبط المؤقت--}}
{{--            }--}}
{{--        });--}}

{{--        // تحديث العرض لأول مرة--}}
{{--        updateTimerDisplay();--}}
{{--    }--}}

{{--    initializeIdleTimer();--}}

{{--    // تشغيل السكريبت عند تحميل الصفحة لأول مرة--}}
{{--    document.addEventListener('DOMContentLoaded', function () {--}}
{{--        initializeIdleTimer();--}}
{{--    });--}}

{{--    // إعادة تشغيل السكريبت بعد أي تحديث أو تنقل عبر Livewire--}}
{{--    document.addEventListener('livewire:navigate', function () {--}}
{{--        initializeIdleTimer();--}}
{{--    });--}}

{{--</script>--}}


</body>
</html>
