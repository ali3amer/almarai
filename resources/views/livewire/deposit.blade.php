<div>

    <div wire:ignore.self class="modal fade" id="debtModal" tabindex="-1" aria-labelledby="debtModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <button class="btn btn-primary" id="printNote"><i class="bi bi-printer"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body bg-white">
                            @if(!empty($currentReceipt) && !empty($currentDeposit))
                                <table class="table note ">
                                    <tbody>
                                    <tr>
                                        <td>السيد</td>
                                        <td>{{$currentDeposit['name']}}</td>
                                    </tr>
                                    <tr>
                                        <td>البيان</td>
                                        <td>{{$currentReceipt['note']}}</td>
                                    </tr>
                                    <tr>
                                        <td>نوع العملية</td>
                                        <td>{{$currentReceipt['type'] == 'pay' ? 'دفع' : 'سحب'}}</td>
                                    </tr>
                                    @if($currentReceipt['payment'] == 'cash')
                                        <tr>
                                            <td>وسيلة الدفع</td>
                                            <td>كاش</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>وسيلة الدفع</td>
                                            <td>بنك</td>
                                        </tr>
                                        <tr>
                                            <td>الايصال</td>
                                            <td>{{ $currentReceipt['bank'] }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>المبلغ</td>
                                        <td>
                                            {{ number_format($currentReceipt['amount'], 2) }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <x-title :$title/>
    {{--    <livewire:Title :$title />--}}

    <div class="row mt-2">
        @if(empty($currentDeposit))
            <div class="col-4">
                <div class="card bg-white">
                    <div class="card-body">
                        <form action="" wire:submit="save({{ $id }})">
                            <label for="name" class="form-label">الإسم</label>
                            <input type="text" wire:model="name" autocomplete="off" class="form-control"
                                   placeholder="الإسم ..."
                                   id="name">
                            <div>
                                @error('name') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                            <label for="phone" class="form-label">الهاتف</label>
                            <input type="text" wire:model="phone" class="form-control" autocomplete="off"
                                   placeholder="الهاتف ..."
                                   id="phone">
                            <div>
                                @error('phone') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                            <label for="initialBalance" class="form-label">الرصيد الافتتاحي</label>
                            <input type="text" wire:model="initialBalance" autocomplete="off" class="form-control"
                                   placeholder="الرصيد الافتتاحي ..." id="initialBalance">
                            <div>
                                @error('initialBalance') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>

                            <label for="startingDate">تاريخ بداية التعامل</label>
                            <input type="date" disabled wire:model.live="startingDate" id="startingDate"
                                   class="form-control text-center">


                            @if($blocked == true)
                                <label for="note" class="form-label">سبب الإيقاف</label>
                                <input type="text" wire:model="note" autocomplete="off" class="form-control"
                                       placeholder="سبب الإيقاف ..." id="note">
                                <div>
                                    @error('note') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <div class="d-grid mt-2">
                                <button
                                    @disabled(!$create) class="btn btn- btn-{{$id == 0 ? 'primary' : 'success'}}">{{$id == 0 ? 'حفـــــــــــــــــــظ' : 'تعـــــــــــــــــديل'}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <div class="card">
                    <div class="card-header">
                        <input wire:model.live="search" autocomplete="off" class="form-control w-50"
                               placeholder="بحث ......">
                    </div>

                    <div class="card-body">
                        @if(count($deposits) > 0 && $read)
                            <div class="scroll">
                                <table class="table text-center">
                                    <thead>
                                    <tr>
                                        <th>الإسم</th>
                                        <th>الهاتف</th>
                                        <th>الرصيد الافتتاحي</th>
                                        <th>الرصيد الحالي</th>
                                        <th>التحكم</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($deposits as $deposit)
                                        <tr>
                                            <td>{{ $deposit->name }}</td>
                                            <td>{{ $deposit->phone }}</td>
                                            <td>{{ number_format($deposit->initialDepositBalance, 2) }}</td>
                                            <td>{{ number_format($deposit->currentDepositsBalance, 2) }}</td>
                                            <td>
                                                <button
                                                    @disabled(!$update) class="btn btn-sm btn-info text-white"
                                                    wire:click="edit({{$deposit}})"><i class="bi bi-pen"></i></button>

                                                <button
                                                    @disabled(!$delete) class="btn btn-sm btn-danger d-none"
                                                    wire:click="deleteMessage({{$deposit}})"><i class="bi bi-trash"></i>
                                                </button>
                                                /
                                                <button @disabled(!$update) class="btn btn-sm btn-warning text-white"
                                                        wire:click="showDebts({{$deposit}})"><i class="bi bi-eye"></i>
                                                </button>

                                                /
                                                <button @disabled(!$update)
                                                        class="btn btn-sm btn-{{$deposit->blocked ? 'danger' : 'success'}} text-white"
                                                        wire:click="changeBlocked({{$deposit}})"><i
                                                        class="bi bi-{{$deposit->blocked ? 'lock' : 'unlock'}}"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-danger text-center">لايوجد ....</div>
                        @endif

                    </div>
                </div>
            </div>
        @else
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-3 d-flex align-items-center">
                                    <h6>سداد</h6>
                                </div>
                                <div class="col-9">
                                    <input type="text" style="cursor:pointer;" wire:click="resetData('currentDeposit')"
                                           readonly value="{{$currentDeposit['name']}}"
                                           class="border-danger form-control text-center" placeholder="إسم العيل">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <label for="payment">نوع العملية</label>
                                <select class="form-select text-center" wire:model.live="type">
                                    <option value="pay">توريد للخزنه</option>
                                    <option value="debt">سحب من الامانات</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="due_date">التاريخ</label>
                                <input type="date" disabled wire:model.live="due_date" id="due_date"
                                       class="form-control text-center">
                            </div>
                        </div>
                        <div class="row my-2">
                            <div class="col-6">
                                <label for="amount">المبلغ المدفوع</label>
                                <input type="text"
                                       wire:model.live="amount" autocomplete="off" id="amount"
                                       class="form-control text-center"
                                       placeholder="المدفوع ....">
                            </div>
                            <div class="col-6">
                                <label for="payment">طريقة الدفع</label>
                                <select
                                    @disabled($banks->count() == 0) @disabled($debtId !=0) class="form-select text-center"
                                    wire:model.live="payment">
                                    <option value="cash">كاش</option>
                                    <option value="bank">بنك</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <label for="payment">البنك</label>
                                <select @disabled($payment == 'cash') class="form-select text-center"
                                        wire:model.live="bank_id">
                                    @foreach($banks as $bank)
                                        <option value="{{$bank->id}}">{{$bank->bankName}}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-6">
                                <label for="bank">رقم الايصال</label>
                                <input @disabled($payment == 'cash') autocomplete="off" type="text" autocomplete="off"
                                       wire:model="bank" id="bank"
                                       class="form-control text-center mb-2"
                                       placeholder="رقم الايصال ....">

                            </div>

                            <div class="col-12">
                                <label for="note">ملاحظات</label>
                                <input autocomplete="off" type="text"
                                       wire:model="note" id="note"
                                       class="form-control text-center mb-2"
                                       placeholder="ملاحظات ....">
                            </div>
                        </div>

                        <div class="row">
                            @if(!session("closed") || $payment == "bank")

                                <div class="col-12 d-flex align-items-end">
                                    <button data-bs-toggle="modal" data-bs-target="#debtModal"
                                            @disabled($payment == "bank" && $banks->count() == 0) @disabled(empty($currentDeposit) || $due_date == '') @disabled($amount == 0) class="btn btn-{{$debtId == 0 ? 'primary' : 'success'}} w-100"
                                            wire:click="saveDebt()">{{$debtId == 0 ? 'دفــــع' : 'تعــــديل'}}</button>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-6"><h6>المعاملات</h6></div>
                                <div class="col-6"><h6>رصيد العميل
                                        : {{ number_format($currentBalance, 2) }}</div>
                            </div>
                        </div>
                        <div class="scroll">
                            <table class="table text-center">
                                <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>البيان</th>
                                    <th>المبلغ</th>
                                    <th>التحكم</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($debts as $debt)
                                    <tr>
                                        <td style="cursor: pointer" wire:click="showReceipt({{$debt}})"
                                            data-bs-toggle="modal" data-bs-target="#debtModal">{{$debt->due_date}}</td>
                                        <td style="cursor: pointer" wire:click="showReceipt({{$debt}})"
                                            data-bs-toggle="modal" data-bs-target="#debtModal">{{$debt->note}}</td>
                                        <td style="cursor: pointer" wire:click="showReceipt({{$debt}})"
                                            data-bs-toggle="modal" data-bs-target="#debtModal">
                                            {{number_format($debt->amount, 2)}}
                                        </td>
                                        <td>
                                            @if($debt->due_date == session("date"))
                                                <button @disabled(!$update) class="btn btn-sm btn-info"
                                                        wire:click="chooseDebt({{$debt}})"><i
                                                        class="bi bi-pen"></i>
                                                </button>

                                                <button @disabled(!$delete) class="btn btn-sm btn-danger"
                                                        wire:click="deleteDebtMessage({{$debt}})"><i
                                                        class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
