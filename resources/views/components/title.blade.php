{{--This Is Real Title--}}
<div wire:ignore.self class="modal fade" id="changeDate" tabindex="-1" aria-labelledby="changeDateLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h1 class="modal-title fs-5" id="bankModalLabel">تغير التاريخ</h1>
            </div>
            <div class="modal-body">
                <div class="card">
                    <form method="post" action="{{ route("changeDate") }}">
                        @csrf
                        <div class="card-body">
                            <div class="card-title"><h5>تغير التاريخ</h5></div>

                            <p>سوف يتم تغيير التاريخ في كل النظام هل أنت متاكد من هذا الإجراء؟</p>
                            <input type="date" value="{{session("date")}}" class="form-control" name="date">

                            <button type="submit" class="btn btn-primary w-100 mt-1">حفــــــــــــــــــظ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if($title != "المبيعات" && $title != "المشتريات")
    <div wire:loading class="position-fixed top-0 opacity-25 bg-dark z-3" style="width: 100%; height: 100%;">
        <div class="d-flex justify-content-center" style="height: 100%">
            <i class="spinner-border text-primary m-auto"></i>
        </div>
    </div>
@endif
<div class="card bg-white mt-2 d-print-non shadow">
    <div class="card-body p-1">
        <div class="row align-items-center">
            <div class="col-2">
                <h6 class="m-0 px-2">{{ $title }}</h6>
            </div>
            <div class="col-2">
                <input type="text" readonly style="cursor:pointer;" value="{{ session("date") }}" class="form-control text-center form-control-sm " data-bs-toggle="modal" data-bs-target="#changeDate">
            </div>
            <div class="col-2">
                <h6 class="m-0 px-2">{{ "الخزنة : " . number_format($safeBalance, 2) }}</h6>
            </div>
            <div class="col-3">
                <h6 class="m-0 px-2">{{ "البنك : " . number_format($bankBalance, 2) }}</h6>
            </div>
            <div class="col-3">
                <h6 class="m-0 px-2">{{ "الجمله : " . number_format($bankBalance + $safeBalance, 2) }}</h6>
            </div>
        </div>
    </div>
</div>

