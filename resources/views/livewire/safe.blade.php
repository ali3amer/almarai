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
                                <input type="text" wire:model.live="bankName" placeholder="إسم البنك ....."
                                       id="bankName" class="form-control">
                            </div>

                            <div>
                                <label for="accountName">إسم الحساب</label>
                                <input type="text" wire:model.live="accountName" placeholder="إسم الحساب ....."
                                       id="accountName" class="form-control">
                            </div>

                            <div class="mt-1">
                                <label for="number">رقم الحساب</label>
                                <input type="text" wire:model.live="number" placeholder="رقم الحساب ....." id="number"
                                       class="form-control">
                            </div>

                            <div class="mt-1">
                                <label for="initialBalance">الرصيد الإفتتاحي</label>
                                <input type="text" wire:model.live="initialBalance" placeholder="الرصيد الإفتتاحي ....."
                                       id="initialBalance" class="form-control">
                            </div>

                            <div class="my-1">
                                <label for="currentBalance">الرصيد الحالي</label>
                                <input type="text" wire:model.live="currentBalance" placeholder="الرصيد الحالي ....."
                                       id="currentBalance" class="form-control">
                            </div>
                            <button wire:click="saveBank()" class="btn btn-primary w-100 mt-1">حفــــــــــــــــــظ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <x-title :$title/>

    <div class="row">
        <div class="col-5">
            <div class="card my-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <label for="safe">رصيد الخزنه</label>
                            <input disabled id="safe" type="text" value="{{number_format($safe, 2)}}"
                                   class="form-control text-center mt-2" placeholder="رصيد الخزنة ...........">
                        </div>

                        <div class="col-4">
                            <label for="bank">رصيد البنك</label>
                            <input disabled id="bank" type="text" value="{{number_format($bank, 2)}}"
                                   class="form-control text-center mt-2" placeholder="رصيد البنك ...........">
                        </div>

                        <div class="col-4">
                            <label for="amount">الجمله</label>
                            <input disabled id="amount" type="text" value="{{number_format($safe + $bank, 2)}}"
                                   class="form-control text-center mt-2" placeholder="الجمله ...........">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bankModal"><i
                                class="bi bi-bag-plus"></i></button>
                    </div>
                    <div class="scroll">
                        <table class="table text-center">
                            <thead>
                            <tr>
                                <th>إسم البنك</th>
                                <th>إسم الحساب</th>
                                <th>رقم الحساب</th>
                                <th>الرصيد الافتتاحي</th>
                                <th>الرصيد الحالي</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($banks as $bank)
                                <tr>
                                    <td>{{$bank->bankName}}</td>
                                    <td>{{$bank->accountName}}</td>
                                    <td>{{$bank->number}}</td>
                                    <td>{{number_format($bank->initialBalance, 2)}}</td>
                                    <td>{{number_format($bank->currentBalance, 2)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-7">
            <div class="card my-2">
                <div class="card-body">
                    <form wire:submit="saveTransfer()">
                        <div class="row">
                            <div class="col-4">
                                <label for="type">نوع التحويل</label>
                                <select id="type" class="form-select text-center" wire:model.live="transfer_type">
                                    <option value="cash_to_bank">من كاش الى بنك</option>
                                    <option value="bank_to_cash">من بنك الى كاش</option>
                                </select>
                            </div>

                            <div class="col-4">
                                <label for="transfer_amount">المبلغ</label>
                                <input type="text" id="transfer_amount" wire:model.live="transfer_amount"
                                       class="form-control text-center" placeholder="المبلغ ....">
                            </div>

                            <div class="col-4">
                                <label for="transfer_number">رقم الاشعار</label>
                                <input type="text" wire:model.live="transfer_number" id="transfer_amount"
                                       class="form-control text-center" placeholder="رقم الاشعار ....">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-3">
                                <label for="transfer_date">تاريخ التحويل</label>
                                <input type="date" wire:model="transfer_date" id="transfer_date"
                                       class="form-control text-center" placeholder="رقم الاشعار ....">
                            </div>

                            <div class="col-3">
                                <label for="type">البنك</label>
                                <select id="type" class="form-select text-center" wire:model.live="bank_id">
                                    @foreach($banks as $bank)
                                        <option value="{{$bank->id}}">{{$bank->bankName}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-3">
                                <label for="note">ملاحظات</label>
                                <input type="text" wire:model.live="note" id="note"
                                       class="form-control text-center" placeholder="محلاظات ....">
                            </div>

                            <div class="col-2 d-flex align-items-end">
                                <button
                                    @disabled($transfer_number == 0 || $transfer_amount == 0) class="btn w-100 btn-{{$transferId == 0 ? 'primary' : 'success'}}"
                                    type="submit">{{$transferId == 0 ? 'حــــفظ' : 'تعـــديل'}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="scroll">
                        <table class="table table-responsive text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>التاريخ</th>
                                <th>البنك</th>
                                <th>نوع التحويل</th>
                                <th>المبلغ</th>
                                <th>رقم الإشعار</th>
                                <th>ملاحظات</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transfers as $transfer)
                                <tr>
                                    <td>{{$loop->index + 1}}</td>
                                    <td>{{$transfer->transfer_date}}</td>
                                    <td>{{$transfer->bank->bankName}}</td>
                                    <td>{{$transfer->transfer_type == 'cash_to_bank' ? 'من كاش الى بنك' : 'من بنك الى كاش'}}</td>
                                    <td>{{number_format($transfer->transfer_amount, 2)}}</td>
                                    <td>{{$transfer->transfer_number}}</td>
                                    <td>{{$transfer->note}}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm text-white"
                                                wire:click="editTransfer({{$transfer}})"><i class="bi bi-pen"></i></button>
                                        /
                                        <button class="btn btn-danger btn-sm" wire:click="deleteTransfer({{$transfer}})"><i
                                                class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
