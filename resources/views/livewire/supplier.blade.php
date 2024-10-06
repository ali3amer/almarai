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
                            @if(!empty($currentReceipt) && !empty($currentSupplier))
                                <table class="table note ">
                                    <tbody>
                                    <tr>
                                        <td>التاريخ</td>
                                        <td>{{$currentReceipt['due_date']}}</td>
                                    </tr>
                                    <tr>
                                        <td>السيد</td>
                                        <td>{{$currentSupplier['name']}}</td>
                                    </tr>
                                    <tr>
                                        <td>البيان</td>
                                        <td>{{ $currentReceipt['note'] }}</td>
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
                                            @if(isset($currentReceipt['debit']))
                                                {{$currentReceipt['debit'] != 0 ? number_format($currentReceipt['debit'], 2) : number_format($debt['credit'], 2) }}
                                            @else
                                                {{ number_format($currentReceipt['amount'], 2) }}
                                            @endif
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


    <x-title :$title :$show/>
    {{--    <livewire:Title :$title />--}}

    <div class="row mt-2">
        @if(empty($currentSupplier))
            <div class="col-3">
                <div class="card bg-white">
                    <div class="card-body">
                        <form action="" wire:submit="save({{ $id }})">
                            <label for="name" class="form-label">إسم المورد</label>
                            <input type="text" wire:model="name" autocomplete="off" class="form-control"
                                   placeholder="إسم المورد ..."
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
                            <label for="initialPurchasesBalance" class="form-label">الرصيد الافتتاحي للمشتريات</label>
                            <input type="text" wire:model="initialPurchasesBalance" autocomplete="off"
                                   class="form-control"
                                   placeholder="الرصيد الافتتاحي للمشتريات ..." id="initialPurchasesBalance">
                            <div>
                                @error('initialPurchasesBalance') <span
                                    class="error text-danger">{{ $message }}</span> @enderror
                            </div>

                            <label for="initialSalesBalance" class="form-label">الرصيد الافتتاحي للمبيعات</label>
                            <input type="text" wire:model="initialSalesBalance" autocomplete="off" class="form-control"
                                   placeholder="الرصيد الافتتاحي للمبيعات ..." id="initialSalesBalance">
                            <div>
                                @error('initialSalesBalance') <span
                                    class="error text-danger">{{ $message }}</span> @enderror
                            </div>

                            <label for="initialDepositsBalance" class="form-label">الرصيد الافتتاحي للأمانات</label>
                            <input type="text" wire:model="initialDepositsBalance" autocomplete="off"
                                   class="form-control"
                                   placeholder="الرصيد الافتتاحي للأمانات ..." id="initialPurchasesBalance">
                            <div>
                                @error('initialDepositsBalance') <span
                                    class="error text-danger">{{ $message }}</span> @enderror
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
                                    @disabled(!$create) class="btn btn- btn-{{$id == 0 ? 'primary' : 'success'}}">{{$id == 0 ? 'حفـــــــــــــــــــظ' : 'تعـــــــــــــــــديل'}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div class="card">
                    <div class="card-header">
                        <input wire:model.live="search" autocomplete="off" class="form-control w-50"
                               placeholder="بحث ......">
                    </div>

                    <div class="card-body">
                        @if(count($suppliers) > 0 && $read)
                            <div class="scroll">
                                <table class="table text-center">
                                    <thead>
                                    <tr>
                                        <th>إسم المورد</th>
                                        <th>الهاتف</th>
                                        <th>الرصيد الحالي</th>
                                        <th class="d-none">نقدي</th>
                                        <th>التحكم</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($suppliers as $supplier)
                                        <tr>
                                            <td>{{ $supplier->name }}</td>
                                            <td>{{ $supplier->phone }}</td>
                                            <td>{{ number_format($supplier->currentPurchasesBalance - $supplier->currentSalesBalance, 2) }}</td>
                                            <td class="d-none">{{ $supplier->cash ? "نعم" : "لا" }}</td>
                                            <td>
                                                <button
                                                    @disabled(!$update) class="btn btn-sm btn-info text-white"
                                                    wire:click="edit({{$supplier}})"><i class="bi bi-pen"></i></button>

                                                <button
                                                    @disabled(!$delete || count($supplier->sales) > 0) class="btn btn-sm btn-danger d-none"
                                                    wire:click="deleteMessage({{$supplier}})"><i
                                                        class="bi bi-trash"></i>
                                                </button>
                                                /
                                                <button @disabled(!$update) class="btn btn-sm btn-warning text-white"
                                                        wire:click="showDebts({{$supplier}})"><i class="bi bi-eye"></i>
                                                </button>

                                                /
                                                <button @disabled(!$update)
                                                        class="btn btn-sm btn-{{$supplier->blocked ? 'danger' : 'success'}} text-white"
                                                        wire:click="changeBlocked({{$supplier}})"><i
                                                        class="bi bi-{{$supplier->blocked ? 'lock' : 'unlock'}}"></i>
                                                </button>


                                                <button @disabled(!$update)
                                                        class="btn d-none btn-sm btn-{{$supplier->cash ? 'danger' : 'primary'}} text-white"
                                                        wire:click="changeCash({{$supplier}})"><i
                                                        class="bi bi-cash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-danger text-center">لايوجد موردين ....</div>
                        @endif

                    </div>
                </div>
            </div>
        @else

            <div class="col-12">
                <div class="card bg-white my-1 shadow">
                    <div class="card-body p-2 invoice" style="page-break-after: unset" dir="rtl">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <h6 class="m-0 px-2">المشتريات
                                    : {{ number_format($currentSupplier['purchasesBalance'], 2) }}</h6>
                            </div>

                            <div class="col-3">
                                <h6 class="m-0 px-2">المبيعات
                                    : {{ number_format($currentSupplier['salesBalance'], 2) }}</h6>
                            </div>

                            <div class="col-3">
                                <h6 class="m-0 px-2">العهد
                                    : {{ number_format($currentSupplier['depositsBalance'], 2) }}</h6>
                            </div>

                            <div class="col-3">
                                <h6 class="m-0 px-2">
                                    الجمله
                                    : {{ number_format( $currentSupplier['purchasesBalance'] - ($currentSupplier['salesBalance'] + $currentSupplier['depositsBalance']), 2) }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="card">
                    <form wire:submit="saveDebt()">
                        <div class="card-body">
                            <div class="card-title">
                                <div class="row">
                                    <div class="col-3 d-flex align-items-center">
                                        <h6>سداد</h6>
                                    </div>
                                    <div class="col-9">
                                        <input type="text" style="cursor:pointer;"
                                               wire:click="resetData('currentSupplier')"
                                               readonly value="{{$currentSupplier['name']}}"
                                               class="border-danger form-control text-center" placeholder="إسم العيل">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label for="payment">نوع العملية</label>
                                    <select class="form-select text-center" wire:model.live="type">
                                        @if($debtType == "deposits")
                                            <option value="pay">توريد للخزنه</option>
                                            <option value="debt">سحب من الامانات</option>
                                        @else
                                            <option value="debt">دين</option>
                                            <option value="pay">توريد</option>
                                            <option value="discount">خصم</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="amount">المبلغ المدفوع</label>
                                    <input type="text"
                                           wire:model.live="amount"
                                           autocomplete="off" id="amount"
                                           class="form-control text-center"
                                           placeholder="المدفوع ....">
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-6">
                                    <label for="payment">طريقة الدفع</label>
                                    <select
                                        @disabled($banks->count() == 0) @disabled($debtId != 0 && $type == "discount") class="form-select text-center"
                                        wire:model.live="payment">
                                        <option value="cash">كاش</option>
                                        <option value="bank">بنك</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="payment">البنك</label>
                                    <select @disabled($payment == 'cash') class="form-select text-center"
                                            wire:model.live="bank_id">
                                        @foreach($banks as $bank)
                                            <option value="{{$bank->id}}">{{$bank->bankName}}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label for="bank">رقم الايصال</label>
                                    <input
                                        @disabled($payment == 'cash') @required($payment == "bank") autocomplete="off"
                                        type="text"
                                        wire:model="bank" id="bank"
                                        class="form-control text-center mb-2"
                                        placeholder="رقم الايصال ....">

                                </div>

                                <div class="col-6">
                                    <label for="note">ملاحظات</label>
                                    <input autocomplete="off" type="text"
                                           wire:model="note" id="note"
                                           class="form-control text-center mb-2"
                                           placeholder="ملاحظات ....">
                                </div>
                            </div>

                            <div class="row">

                                @if(!session("closed") || $payment == "bank")
                                    <button
                                        @disabled($payment == "bank" && $banks->count() == 0)  @disabled(empty($currentSupplier) || $due_date == '') @disabled($amount == 0) class="btn btn-{{$debtId == 0 ? 'primary' : 'success'}} w-100"
                                    >{{$debtId == 0 ? 'دفــــع' : 'تعــــديل'}}</button>

                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row d-flex  align-items-center">
                                <div class="col-4"><h6>المعاملات</h6></div>
                                <div class="col-4"><h6>الرصيد : {{ number_format($currentBalance, 2) }}</h6></div>
                                <div class="col-4">
                                    <div class="row d-flex align-items-center">
                                        <div class="col-6">
                                            <label for="debType"><h6>نوع المعاملات</h6></label>
                                        </div>
                                        <div class="col-6">
                                            <select class="form-select" id="debType" wire:model.live="debtType"
                                                    wire:change="showDebts()">
                                                <option value="purchases">مشتريات</option>
                                                <option value="sales">مبيعات</option>
                                                <option value="deposits">العهد والامانات</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
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
                                        <td style="cursor: pointer" wire:click="showReceipt({{ json_encode($debt) }})"
                                            data-bs-toggle="modal"
                                            data-bs-target="#debtModal">{{$debt['due_date']}}</td>
                                        <td style="cursor: pointer" wire:click="showReceipt({{ json_encode($debt) }})"
                                            data-bs-toggle="modal" data-bs-target="#debtModal">
                                            {{ $debt['note']  }}
                                        </td>
                                        <td style="cursor: pointer" wire:click="showReceipt({{ json_encode($debt) }})"
                                            data-bs-toggle="modal" data-bs-target="#debtModal">
                                            {{$debt['debit'] != 0 ? number_format($debt['debit'], 2) : number_format($debt['credit'], 2) }}
                                        </td>
                                        <td>
                                            @if($debt['due_date'] == session("date") && !session("closed") && $debt['invoice_id'] == null)
                                                <button @disabled(!$update) class="btn btn-sm btn-info"
                                                        wire:click="chooseDebt({{ json_encode($debt) }})"><i
                                                        class="bi bi-pen"></i></button>
                                                <button @disabled(!$delete) class="btn btn-sm btn-danger"
                                                        wire:click="deleteDebtMessage({{ json_encode($debt['id']) }})">
                                                    <i
                                                        class="bi bi-trash"></i></button>
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
