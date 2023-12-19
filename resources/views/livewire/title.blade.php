<div>
    @if($title != "المبيعات" && $title != "المشتريات")
        <div wire:loading class="position-fixed top-0 opacity-25 bg-dark z-3" style="width: 100%; height: 100%;">
            <div class="d-flex justify-content-center" style="height: 100%">
                <i class="spinner-border text-primary m-auto"></i>
            </div>
        </div>
    @endif
    <div class="card bg-white mt-3 d-print-non shadow">
        <div class="card-body p-2">
            <div class="row">
                <div class="col-2">
                    <h4 class="m-0 px-2">{{ $title }}</h4>
                </div>
                <div class="col-2">
{{--                    <input type="date" wire:model.live="date" class="form-control">--}}
                </div>
                <div class="col-2">
                    <h4 class="m-0 px-2">{{ "الخزنة : " . number_format($safeBalance, 2) }}</h4>
                </div>
                <div class="col-3">
                    <h4 class="m-0 px-2">{{ "البنك : " . number_format($bankBalance, 2) }}</h4>
                </div>
                <div class="col-3">
                    <h4 class="m-0 px-2">{{ "الجمله : " . number_format($bankBalance + $safeBalance, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>


</div>
