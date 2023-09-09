<div>

    <div wire:ignore.self class="modal fade" id="bankModal" tabindex="-1" aria-labelledby="bankModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="bankModalLabel">البنوك</h1>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title"><h5>{{ $id == 0 ? 'أضف بنك' : 'تعديل بنك' }}</h5></div>
                            <div>
                                <label for="bankName">إسم البنك</label>
                                <input type="text" wire:model.live="bankName" placeholder="إسم البنك ....." id="bankName" class="form-control">
                            </div>

                            <div class="mt-1">
                                <label for="number">رقم الحساب</label>
                                <input type="text" wire:model.live="number" placeholder="رقم الحساب ....." id="number" class="form-control">
                            </div>

                            <div class="mt-1">
                                <label for="firstBalance">الرصيد الإفتتاحي</label>
                                <input type="text" wire:model.live="firstBalance" placeholder="الرصيد الإفتتاحي ....." id="firstBalance" class="form-control">
                            </div>

                            <div class="my-1">
                                <label for="currentBalance">الرصيد الحالي</label>
                                <input type="text" wire:model.live="currentBalance" placeholder="الرصيد الحالي ....." id="currentBalance" class="form-control">
                            </div>
                            <button  wire:click="saveBank()" class="btn btn-primary w-100 mt-1">حفــــــــــــــــــظ</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <x-title :$title />

    <div class="row mt-2">
        <div class="col-4">
            <div class="card my-2">
                <div class="card-body">
                    <input disabled type="text" class="form-control" placeholder="الرصيد ...........">
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                   <div class="card-title">
                       <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bankModal"><i class="bi bi-bag-plus"></i></button>
                   </div>

                    <table class="table">
                        <thead>
                        <tr>
                            <th>إسم البنك</th>
                            <th>رقم الحساب</th>
                            <th>الرصيد الافتتاحي</th>
                            <th>الرصيد الحالي</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($banks as $bank)
                                <tr>
                                    <td>{{$bank->bankName}}</td>
                                    <td>{{$bank->number}}</td>
                                    <td>{{$bank->firstBalance}}</td>
                                    <td>{{$bank->currentBalance}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
