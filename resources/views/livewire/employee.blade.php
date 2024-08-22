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
                                        <td>السيد</td>
                                        <td>{{$currentEmployee['employeeName']}}</td>
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
                                            @elseif(isset($currentReceipt['service']) && $currentReceipt['service'] != 0)
                                                {{ $currentReceipt['service'] }}
                                            @else
                                                {{ $currentReceipt['discount'] != 0 ? number_format($currentReceipt['discount'] ,2) : number_format($currentReceipt['amount'], 2) }}
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
                            <label for="employeeName" class="form-label">إسم الموظف</label>
                            <input type="text" autocomplete="off" wire:model="employeeName" class="form-control"
                                   placeholder="إسم الموظف ..." id="employeeName">
                            <div>
                                @error('employeeName') <span
                                    class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                            <label for="employeeName" class="form-label">المرتب</label>
                            <input type="text" autocomplete="off" wire:model.live="salary" class="form-control"
                                   placeholder="المرتب"
                                   id="salary">
                            <div>
                                @error('salary') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>

                            <label for="initialBalance" class="form-label">الرصيد الإفتتاحي</label>
                            <input type="text" autocomplete="off" wire:model="initialBalance" class="form-control"
                                   placeholder="الرصيد الإفتتاحي"
                                   id="initialBalance">
                            <div>
                                @error('initialBalance') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>

                            <label for="startingDate">تاريخ الإضافه</label>
                            <input type="date" disabled wire:model.live="startingDate" id="startingDate"
                                   class="form-control text-center">


                            <div class="d-grid mt-2">
                                <button type="submit"
                                        @disabled($employeeName == '' || $salary <= 0) @disabled(!$create)  data-bs-dismiss="modal"
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
                                        <th>المرتب</th>
                                        <th>الرصيد الإفتتاحي</th>
                                        <th>الرصيد الحالي</th>
                                        <th>التحكم</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-white">
                                    @foreach($employees as $employee)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $employee->employeeName }}</td>
                                            <td>{{ number_format($employee->salary, 2) }}</td>
                                            <td>{{ number_format($employee->initialBalance, 2) }}</td>
                                            <td>{{ number_format($employee->currentBalance, 2) }}</td>
                                            <td>
                                                <button
                                                    @disabled(!$update) data-bs-target="#employeeModal"
                                                    class="btn btn-sm btn-info text-white"
                                                    wire:click="edit({{$employee}})">
                                                    <i class="bi bi-pen"></i></button>
                                                /
                                                <button class="btn btn-sm btn-danger"
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
                    <div class="card-body">

                        <label for="employeeName">إسم الموظف</label>
                        <input id="employeeName"
                               @click="$dispatch('reset-employee', { data: 'currentEmployee' })" type="text"
                               readonly
                               style="cursor:pointer;"
                               class="form-control text-center border-danger"
                               wire:model.live="currentEmployee.employeeName">

                        <div class="row">
                            <div class="col-6">
                                <label for="type">نوع العملية</label>
                                <select id="type" class="form-select text-center"
                                        @disabled($editGiftMode || $editDebtMode)
                                        wire:model.live="type">
                                    <option value="gift">حافز او مرتب او سلفيه</option>
                                    <option value="pay">سداد</option>
                                </select>
                            </div>

                            <div class="col-6">
                                <label for="due_date">التاريخ</label>
                                <input type="date" disabled class="form-control text-center" id="due_date"
                                       wire:model.live="due_date">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <label for="amount">المدفوع</label>
                                <input type="text" id="amount" autocomplete="off"
                                       class="form-control text-center"
                                       @disabled($type == "pay" && $editDebtMode && $discount> 0)
                                       placeholder="المبلغ ...."
                                       wire:model.live="amount">

                            </div>

                            <div class="col-6">
                                <label for="payment">طريقة الدفع</label>
                                <select id="payment"
                                        @disabled($type == "pay" && $editDebtMode && $discount> 0) @disabled($banks->count() == 0) class="form-select text-center"
                                        wire:model.live="payment">
                                    <option value="cash">كاش</option>
                                    <option value="bank">بنك</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <label for="bank_id">البنك</label>
                                <select id="bank_id"
                                        @disabled($type == "pay" && $editDebtMode && $discount> 0) @disabled($banks->count() == 0) @disabled($payment == 'cash') class="form-select text-center"
                                        wire:model="bank_id">
                                    @foreach($banks as $bank)
                                        <option value="{{$bank->id}}">{{$bank->bankName}}</option>
                                    @endforeach
                                </select>
                                <div>
                                    @error('bank_id') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="bank">رقم الايصال</label>
                                <input type="text" id="bank" autocomplete="off" class="form-control text-center"
                                       @disabled($type == "pay" && $editDebtMode && $discount> 0) @disabled($payment == 'cash') placeholder="رقم الإيصال ...."
                                       wire:model.live="bank">
                            </div>
                        </div>

                        <div class="row">
                            @if($type == "pay")
                                <div class="col-6">
                                    <label for="discount">التخفيض</label>

                                    <input type="text" id="discount" autocomplete="off" class="form-control text-center"
                                           @disabled($type == "pay" && $editDebtMode && $amount > 0) placeholder="التخفيض ...."
                                           wire:model.live="discount">

                                </div>
                            @endif

                            <div class="col-{{ $type == "pay" ? "6" : "12" }}">

                                <label for="note">ملاحظات</label>

                                <input type="text" id="note" autocomplete="off" class="form-control text-center mb-2"
                                       placeholder="ملاحظات ...."
                                       wire:model.live="note">
                            </div>
                        </div>

                        @if(!session("closed") || $payment == "bank")
                            @if($editGiftMode)
                                <button class="btn btn-success w-100"
                                        @if($type == "gift") wire:click="updateGift()"
                                        @else wire:click="updateDebt()" @endif>تعديل
                                </button>
                            @else
                                <button class="btn btn-primary w-100"
                                        @disabled($payment == "bank" && $bank_id == null) @if($type == "gift") wire:click="payGift()"
                                        @else wire:click="payDebt()" @endif>{{ $type == "gift" ? "دفع" : "سداد" }}
                                </button>
                            @endif
                        @endif

                    </div>
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
                                    <th class="d-none">التحكم</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($gifts as $gift)
                                    <tr>
                                        <td>{{$gift->due_date}}</td>
                                        <td>{{number_format($gift->amount, 2)}}</td>
                                        <td>{{$gift->note}}</td>
                                        <td class="d-none">
                                            <button
                                                @disabled(!$update)
                                                class="btn btn-sm btn-info text-white"
                                                wire:click="editGift({{$gift}})">
                                                <i class="bi bi-pen"></i></button>
                                            /
                                            <button class="btn btn-sm btn-danger"
                                                    wire:click="deleteGiftMessage({{$gift}})">
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

                <div class="card mt-2">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-6"><h6>المعاملات</h6></div>
                                <div class="col-6"><h6>رصيد الموظف
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
                                        <td class="d-none">
                                            @if($debt['invoice_id'] == null && $debt['due_date'] == session("date"))
                                                <button class="btn btn-sm btn-info"
                                                        wire:click="chooseDebt({{ json_encode($debt) }})"><i
                                                        class="bi bi-pen"></i></button>
                                                <button class="btn btn-sm btn-danger"
                                                        wire:click="deleteDebtMessage({{ json_encode($debt) }})"><i
                                                        class="bi bi-trash"></i></button>
                                            @endif
                                        </td>
                                    </tr>

                                    @if($debt['tableName'] == "sale_returns" && floatval($debt['transaction_paid']) != 0)
                                        <tr>
                                            <td style="cursor: pointer"
                                                wire:click="showReceipt({{ json_encode($debt) }})"
                                                data-bs-toggle="modal"
                                                data-bs-target="#debtModal">{{$debt['due_date']}}</td>
                                            <td style="cursor: pointer"
                                                wire:click="showReceipt({{ json_encode($debt) }})"
                                                data-bs-toggle="modal" data-bs-target="#debtModal">
                                                {{ 'مبلغ مرتجعات مبيعات بفاتورة رقم #' . $debt['invoice_id']}}
                                            </td>
                                            <td style="cursor: pointer"
                                                wire:click="showReceipt({{ json_encode($debt) }})"
                                                data-bs-toggle="modal" data-bs-target="#debtModal">
                                                {{number_format($debt['transaction_paid'], 2)}}
                                            </td>
                                            <td class="d-none">
                                                @if($debt['invoice_id'] == null && $debt['due_date'] == session("date"))
                                                    <button class="btn btn-sm btn-info"
                                                            wire:click="chooseDebt({{ json_encode($debt) }})"><i
                                                            class="bi bi-pen"></i></button>
                                                    <button class="btn btn-sm btn-danger"
                                                            wire:click="deleteDebtMessage({{ json_encode($debt) }})"><i
                                                            class="bi bi-trash"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
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
