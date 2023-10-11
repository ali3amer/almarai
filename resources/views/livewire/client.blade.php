<div>
    <x-title :$title></x-title>

    <div class="row mt-2">
        @if(empty($currentClient))
            <div class="col-4">
                <div class="card bg-white">
                    <div class="card-body">
                        <form action="" wire:submit="save({{ $id }})">
                            <label for="clientName" class="form-label">إسم العميل</label>
                            <input type="text" wire:model="clientName" autocomplete="off" class="form-control"
                                   placeholder="إسم العميل ..."
                                   id="clientName">
                            <div>
                                @error('clientName') <span class="error text-danger">{{ $message }}</span> @enderror
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
                                    @disabled(!Auth::user()->hasPermission('clients-create')) class="btn btn- btn-{{$id == 0 ? 'primary' : 'success'}}">{{$id == 0 ? 'حفـــــــــــــــــــظ' : 'تعـــــــــــــــــديل'}}</button>
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
                        @if(count($clients) > 0 && Auth::user()->hasPermission('clients-read'))
                            <div class="scroll">
                                <table class="table text-center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>إسم العميل</th>
                                        <th>الهاتف</th>
                                        <th>الرصيد الافتتاحي</th>
                                        <th>الرصيد الحالي</th>
                                        <th>التحكم</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($clients as $client)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $client->clientName }}</td>
                                            <td>{{ $client->phone }}</td>
                                            <td>{{ number_format($client->initialBalance, 2) }}</td>
                                            <td>{{ number_format(0, 2) }}</td>
                                            <td>
                                                <button
                                                    @disabled(!Auth::user()->hasPermission('clients-update')) class="btn btn-sm btn-info text-white"
                                                    wire:click="edit({{$client}})"><i class="bi bi-pen"></i></button>
                                                /
                                                <button
                                                    @disabled(!Auth::user()->hasPermission('clients-delete') || count($client->sales) > 0) class="btn btn-sm btn-danger"
                                                    wire:click="deleteMessage({{$client}})"><i class="bi bi-trash"></i>
                                                </button>
                                                /
                                                <button class="btn btn-sm btn-warning text-white"
                                                        wire:click="showDebts({{$client}})"><i class="bi bi-eye"></i>
                                                </button>

                                                /
                                                <button
                                                    class="btn btn-sm btn-{{$client->blocked ? 'danger' : 'success'}} text-white"
                                                    wire:click="changeBlocked({{$client}})"><i
                                                        class="bi bi-{{$client->blocked ? 'lock' : 'unlock'}}"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-danger text-center">لايوجد عملاء ....</div>
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
                                    <input type="text" style="cursor:pointer;" wire:click="resetData('currentClient')"
                                           readonly value="{{$currentClient['clientName']}}"
                                           class="border-danger form-control text-center" placeholder="إسم العيل">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <label for="payment">نوع العملية</label>
                                <select class="form-select text-center" wire:model.live="type">
                                    <option value="debt">دين</option>
                                    <option value="pay">توريد</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="due_date">التاريخ</label>
                                <input type="date" wire:model.live="due_date" id="due_date"
                                       class="form-control text-center">
                            </div>
                        </div>
                        <div class="row my-2">
                            <div class="col-6">
                                <label for="debt_amount">المبلغ المدفوع</label>
                                <input type="text" wire:model.live="debt_amount" autocomplete="off" id="debt_amount"
                                       class="form-control text-center"
                                       placeholder="المدفوع ....">
                            </div>
                            <div class="col-6">
                                <label for="payment">طريقة الدفع</label>
                                <select class="form-select text-center" wire:model.live="payment">
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
                        </div>

                        <label for="note">ملاحظات</label>
                        <input autocomplete="off" type="text" autocomplete="off"
                               wire:model="note" id="note"
                               class="form-control text-center mb-2"
                               placeholder="ملاحظات ....">

                        <button
                            @disabled(($type == 'debt' && ($payment == 'bank' && $debt_amount > $bankBalance))) @disabled(($type == 'debt' && ($payment == 'cash' && $debt_amount > $safeBalance))) @disabled(empty($currentClient) || $debt_amount == 0 || $due_date == '') class="btn btn-{{$debtId == 0 ? 'primary' : 'success'}} w-100"
                            wire:click="saveDebt()">{{$debtId == 0 ? 'دفــــع' : 'تعــــديل'}}</button>

                    </div>
                </div>
            </div>

            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-3"><h6>المعاملات</h6></div>
                                <div class="col-3"><h6>رصيد العميل
                                        : {{ number_format(0, 2) }}</div>
                                <div class="col-3"><h6>رصيد الخزنة : {{ number_format($safeBalance, 2) }}</h6></div>
                                <div class="col-3"><h6>رصيد البنك : {{ number_format($bankBalance, 2) }}</h6></div>
                            </div>
                        </div>
                        <div class="scroll">
                            <table class="table text-center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>البيان</th>
                                    <th>طريقة الدفع</th>
                                    <th>الإيصال</th>
                                    <th>المبلغ</th>
                                    <th>التحكم</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($debts as $debt)
                                    <tr>
                                        <td>{{$debt->id}}</td>
                                        <td>{{$debt->due_date}}</td>
                                        <td>{{$debt->note}}</td>
                                        <td>{{$debt->payment == 'cash' ? 'كاش' : 'بنك'}}</td>
                                        <td>{{$debt->bank}}</td>
                                        <td>{{$debt->type == 'pay' ? $debt->paid : $debt->debt}}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info text-white"
                                                    wire:click="chooseDebt({{$debt}})"><i class="bi bi-pen"></i>
                                            </button>
                                            /
                                            <button class="btn btn-sm btn-danger"
                                                    wire:click="deleteDebtMessage({{$debt}})">
                                                <i
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
        @endif
    </div>
</div>
