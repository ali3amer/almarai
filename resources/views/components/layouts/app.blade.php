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
