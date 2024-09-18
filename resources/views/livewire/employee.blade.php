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
                            @if(!empty($currentReceipt) && !empty($currentEmployee))
                                <table class="table note ">
                                    <tbody>
                                    <tr>
                                        <td>التاريخ</td>
                                        <td>{{$currentReceipt['due_date']}}</td>
                                    </tr>
                                    <tr>
                                        <td>السيد</td>
                                        <td>{{$currentEmployee['name']}}</td>
                                    </tr>
                                    <tr>
                                        <td>البيان</td>
                                        <td>{{ $currentReceipt['note']  }}</td>
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
                                            @if(isset($currentReceipt['futureExpense']))
                                                {{$currentReceipt['futureExpense'] != 0 ? number_format($currentReceipt['futureExpense'], 2) : ($currentReceipt['futureIncome'] != 0 ? number_format($currentReceipt['futureIncome'], 2) : ($currentReceipt['expense'] != 0 ? number_format($currentReceipt['expense'], 2) : number_format($currentReceipt['income'], 2)))}}

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


    <x-title :$title/>
    {{--    <livewire:Title :$title />--}}

    <div class="row mt-2">
        @if(empty($currentEmployee))
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <form action="" wire:submit="save({{ $id }})">
                            <label for="name" class="form-label">إسم الموظف</label>
                            <input type="text" autocomplete="off" wire:model.live="name" class="form-control"
                                   placeholder="إسم الموظف ..." id="name">
                            <div>
                                @error('name') <span
                                    class="error text-danger">{{ $message }}</span> @enderror
                            </div>


                            <label for="initialSalesBalance" class="form-label">الرصيد الإفتتاحي</label>
                            <input type="text" autocomplete="off" wire:model="initialSalesBalance" class="form-control"
                                   placeholder="الرصيد الإفتتاحي"
                                   id="initialSalesBalance">
                            <div>
                                @error('initialSalesBalance') <span
                                    class="error text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="d-grid mt-2">
                                <button type="submit"
                                        @disabled($name == '') @disabled(!$create)  data-bs-dismiss="modal"
                                        aria-label="Close"
                                        class="btn btn- btn-{{ $editMode ? 'success' : 'primary' }}">{{ $editMode ? 'تعـــــــــــــــديل' : 'حفـــــــــــــــــــظ' }}</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <div class="card">
                    <div class="card-header">
                        <div class="col"><input autocomplete="off" wire:model.live="search" class="form-control w-50"
                                                placeholder="بحث ......"></div>
                    </div>
                    <div class="card-body">
                        @if(count($employees) > 0 && $read)
                            <div class="scroll">
                                <table class="table text-center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>إسم الموظف</th>
                                        <th>الرصيد الإفتتاحي</th>
                                        <th>الرصيد الحالي</th>
                                        <th>التحكم</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-white">
                                    @foreach($employees as $employee)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $employee->name }}</td>
                                            <td>{{ number_format($employee->initialSalesBalance, 2) }}</td>
                                            <td>{{ number_format($employee->currentBalance, 2) }}</td>
                                            <td>
                                                <button
                                                    @disabled(!$update) data-bs-target="#employeeModal"
                                                    class="btn btn-sm btn-info text-white"
                                                    wire:click="edit({{$employee}})">
                                                    <i class="bi bi-pen"></i></button>

                                                <button class="btn btn-sm btn-danger d-none"
                                                        @disabled(!$delete || count($employee->sales) > 0 || count($employee->gifts) > 0) wire:click="deleteMessage({{$employee}})">
                                                    <i
                                                        class="bi bi-trash"></i></button>
                                                /
                                                <button @disabled(!$update) class="btn btn-sm btn-warning text-white"
                                                        wire:click="getGifts({{$employee}})"><i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-danger text-center">لايوجد موظفين ....</div>
                        @endif

                    </div>
                </div>
            </div>
        @endif

        @if(!empty($currentEmployee))
            <div class="col-4 mb-2">
                <div class="card">
                    <form @if($type == 'gift') wire:submit="payGift()" @else wire:submit="saveDebt()"  @endif>
                        <div class="card-body">

                            <label for="name">إسم الموظف</label>
                            <input id="name"
                                   @click="$dispatch('reset-employee', { data: 'currentEmployee' })" type="text"
                                   readonly
                                   style="cursor:pointer;"
                                   class="form-control text-center border-danger"
                                   wire:model.live="currentEmployee.name">

                            <div class="row">
                                <div class="col-6">
                                    <label for="type">نوع العملية</label>
                                    <select id="type" class="form-select text-center"
                                            @disabled($editGiftMode || $editDebtMode)
                                            wire:model.live="type">
                                        @if($debtType == "deposits")
                                            <option value="pay">توريد للخزنه</option>
                                            <option value="debt">سحب من الامانات</option>
                                        @else
                                            <option value="gift">حافز او مرتب او سلفيه</option>
                                            <option value="debt">دين</option>
                                            <option value="pay">توريد</option>
                                            <option value="discount">خصم</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="col-6">
                                    <label for="amount">المدفوع</label>
                                    <input type="text" id="amount" autocomplete="off"
                                           class="form-control text-center"
                                           @disabled($type == "pay" && $editDebtMode)
                                           placeholder="المبلغ ...."
                                           wire:model.live="amount">

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label for="payment">طريقة الدفع</label>
                                    <select id="payment"
                                            @disabled($type == "pay" && $editDebtMode) @disabled($banks->count() == 0) class="form-select text-center"
                                            wire:model.live="payment">
                                        <option value="cash">كاش</option>
                                        <option value="bank">بنك</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="bank_id">البنك</label>
                                    <select id="bank_id"
                                            @disabled($type == "pay" && $editDebtMode) @disabled($banks->count() == 0) @disabled($payment == 'cash') class="form-select text-center"
                                            wire:model="bank_id">
                                        @foreach($banks as $bank)
                                            <option value="{{$bank->id}}">{{$bank->bankName}}</option>
                                        @endforeach
                                    </select>
                                    <div>
                                        @error('bank_id') <span class="error text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label for="bank">رقم الايصال</label>
                                    <input type="text" id="bank" autocomplete="off" class="form-control text-center"
                                            @disabled($payment == 'cash') @required($payment == "bank")  placeholder="رقم الإيصال ...."
                                           wire:model="bank">
                                </div>
                                <div class="col-6">

                                    <label for="note">ملاحظات</label>

                                    <input type="text" id="note" autocomplete="off" class="form-control text-center mb-2"
                                           placeholder="ملاحظات ...."
                                           wire:model.live="note">
                                </div>
                            </div>

                            @if(!session("closed") || $payment == "bank")

                                @if($type == 'gift')
                                    <button class="btn btn-{{$gift_id == 0 ? 'primary' : 'success'}} w-100"
                                            @disabled(floatval($amount) == 0)
                                            @disabled($payment == "bank" && $bank_id == null) >{{$gift_id == 0 ? 'دفــــع' : 'تعــــديل'}}</button>
                                @else
                                    <button
                                            @disabled($payment == "bank" && $banks->count() == 0)  @disabled(empty($currentEmployee) || $due_date == '') @disabled($amount == 0) class="btn btn-{{$debtId == 0 ? 'primary' : 'success'}} w-100"
                                    >{{$debtId == 0 ? 'دفــــع' : 'تعــــديل'}}</button>
                                @endif
                            @endif

                        </div>
                    </form>
                </div>
            </div>

            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-4">
                                    <h5>المرتبات والحوافز والسلفيات</h5>
                                </div>
                                <div class="col-8">
                                    <h5><span>مدفوعات الشهر </span>
                                        <span>{{ number_format($currentEmployee["gifts"], 2) }}</span></h5>
                                </div>
                            </div>
                        </div>
                        <div class="scroll">
                            <table class="table text-center">
                                <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>المبلغ</th>
                                    <th>البيان</th>
                                    <th>التحكم</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($gifts as $gift)
                                    <tr>
                                        <td>{{$gift->due_date}}</td>
                                        <td>{{number_format($gift->amount, 2)}}</td>
                                        <td>{{$gift->note}}</td>
                                        <td>
                                            @if($gift['due_date'] == session("date") && !session("closed"))
                                                <button
                                                    @disabled(!$update)
                                                    class="btn btn-sm btn-info text-white"
                                                    wire:click="editGift({{$gift}})">
                                                    <i class="bi bi-pen"></i></button>
                                                /
                                                <button @disabled(!$delete) class="btn btn-sm btn-danger"
                                                        wire:click="deleteGiftMessage({{$gift}})">
                                                    <i
                                                        class="bi bi-trash"></i></button>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-4"><h6>المعاملات</h6></div>
                                <div class="col-4"><h6>رصيد الموظف
                                        : {{ number_format($currentBalance, 2) }}
                                </div>
                                <div class="col-4">
                                    <div class="row d-flex align-items-center">
                                        <div class="col-6">
                                            <label for="debType"><h6>نوع المعاملات</h6></label>
                                        </div>
                                        <div class="col-6">
                                            <select class="form-select" id="debType" wire:model.live="debtType"
                                                    wire:change="getGifts()">
                                                <option value="sales">مبيعات</option>
                                                <option value="deposits">العهد والامانات</option>
                                                <option value="purchases">مشتريات</option>
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
                                            {{ $debt['note'] }}
                                        </td>
                                        <td style="cursor: pointer" wire:click="showReceipt({{ json_encode($debt) }})"
                                            data-bs-toggle="modal" data-bs-target="#debtModal">
                                            {{$debt['futureExpense'] != 0 ? number_format($debt['futureExpense'], 2) : ($debt['futureIncome'] != 0 ? number_format($debt['futureIncome'], 2) : ($debt['expense'] != 0 ? number_format($debt['expense'], 2) : number_format($debt['income'], 2)))}}
                                        </td>
                                        <td>
                                            @if($debt['due_date'] == session("date") && !session("closed") && $debt['invoice_id'] == null)
                                                <button class="btn btn-sm btn-info"
                                                        wire:click="chooseDebt({{ json_encode($debt) }})"><i
                                                        class="bi bi-pen"></i></button>
                                                <button class="btn btn-sm btn-danger"
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
