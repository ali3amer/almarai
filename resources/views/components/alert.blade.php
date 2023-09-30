<div>
    @if(session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="alert alert-success text-center d-print-none">{{ session('success') }}</div>
    @endif
</div>
