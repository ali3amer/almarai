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
                            @if(!empty($currentDebt) && !empty($currentSupplier))
                                <table class="table note ">
                                    <tbody>
                                    <tr>
                                        <td>السيد</td>
                                        <td>{{$currentSupplier['supplierName']}}</td>
                                    </tr>
                                    <tr>
                                        <td>البيان</td>
                                        <td>{{$currentDebt['note']}}</td>
                                    </tr>
                                    <tr>
                                        <td>نوع العملية</td>
                                        <td>{{$currentDebt['type'] == 'pay' ? 'دفع' : 'سحب'}}</td>
                                    </tr>
                                    @if($currentDebt['payment'] == 'cash')
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
                                            <td>{{ $currentDebt['bank'] }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>المبلغ</td>
                                        <td>{{ $currentDebt['type'] == 'pay' ? $currentDebt['paid'] : $currentDebt['debt'] }}</td>
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
        @if(empty($currentSupplier))
            <div class="col-4">
                <div class="card bg-white">
                    <div class="card-body">
                        <form action="" wire:submit="save({{ $id }})">
                            <label for="supplierName" class="form-label">إسم المورد</label>
                            <input type="text" wire:model="supplierName" autocomplete="off" class="form-control"
                                   placeholder="إسم المورد ..."
                                   id="supplierName">
                            <div>
                                @error('supplierName') <span class="error text-danger">{{ $message }}</span> @enderror
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

                            <label for="initialSalesBalance" class="form-label">الرصيد الافتتاحي للمبيعات</label>
                            <input type="text" wire:model="initialSalesBalance" autocomplete="off" class="form-control"
                                   placeholder="الرصيد الافتتاحي للمبيعات ..." id="initialSalesBalance">
                            <div>
                                @error('initialSalesBalance') <span
                                    class="error text-danger">{{ $message }}</span> @enderror
                            </div>

                            <label for="startingDate">تاريخ بداية التعامل</label>
                            <input type="date" wire:model.live="startingDate" id="startingDate"
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
                        @if(count($suppliers) > 0 && $read)
                            <div class="scroll">
                                <table class="table text-center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>إسم المورد</th>
                                        <th>الهاتف</th>
                                        <th>الرصيد الافتتاحي</th>
                                        <th>الرصيد الافتتاحي للمبيعات</th>
                                        <th>نقدي</th>
                                        <th>التحكم</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($suppliers as $supplier)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $supplier->supplierName }}</td>
                                            <td>{{ $supplier->phone }}</td>
                                            <td>{{ number_format($supplier->initialBalance, 2) }}</td>
                                            <td>{{ number_format($supplier->initialSalesBalance, 2) }}</td>
                                            <td>{{ $supplier->cash ? "نعم" : "لا" }}</td>
                                            <td>
                                                <button
                                                    @disabled(!$update) class="btn btn-sm btn-info text-white"
                                                    wire:click="edit({{$supplier}})"><i class="bi bi-pen"></i></button>
                                                /
                                                <button
                                                    @disabled(!$delete || count($supplier->sales) > 0) class="btn btn-sm btn-danger"
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

                                                /
                                                <button  @disabled(!$update)
                                                         class="btn btn-sm btn-{{$supplier->cash ? 'danger' : 'primary'}} text-white"
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
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-3 d-flex align-items-center">
                                    <h6>سداد</h6>
                                </div>
                                <div class="col-9">
                                    <input type="text" style="cursor:pointer;" wire:click="resetData('currentSupplier')"
                                           readonly value="{{$currentSupplier['supplierName']}}"
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
                                <select @disabled($banks->count() == 0) class="form-select text-center" wire:model.live="payment">
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

                            @if($type == "pay")
                                <div class="col-6">
                                    <label for="discount">التخفيض</label>
                                    <input autocomplete="off" type="text" autocomplete="off"
                                           wire:model.live="discount" id="discount"
                                           class="form-control text-center mb-2"
                                           placeholder="التخفيض ....">
                                </div>
                            @endif

                            <div class="col-{{ $type == "pay" ? '6' : '12' }}">
                                <label for="note">ملاحظات</label>
                                <input autocomplete="off" type="text" autocomplete="off"
                                       wire:model="note" id="note"
                                       class="form-control text-center mb-2"
                                       placeholder="ملاحظات ....">
                            </div>
                        </div>

                        <button
                            @disabled($payment == "bank" && $banks->count() == 0) @disabled(empty($currentSupplier) || $due_date == '') @disabled($debt_amount == 0 && $discount == 0) class="btn btn-{{$debtId == 0 ? 'primary' : 'success'}} w-100"
                            wire:click="saveDebt()">{{$debtId == 0 ? 'دفــــع' : 'تعــــديل'}}</button>

                    </div>
                </div>
            </div>

            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row d-flex  align-items-center">
                                <div class="col-4"><h6>المعاملات</h6></div>
                                <div class="col-4"><h6>رصيد المورد : {{ number_format($currentBalance, 2) }}</h6></div>
                                <div class="col-4">
                                    <div class="row d-flex align-items-center">
                                        <div class="col-6">
                                            <label for="debType"><h6>نوع المعاملات</h6></label>
                                        </div>
                                        <div class="col-6">
                                            <select class="form-select" id="debType" wire:model.live="debtType" wire:change="showDebts()">
                                                <option value="purchases">مشتريات</option>
                                                <option value="sales">مبيعات</option>
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
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($debts as $debt)
                                    <tr style="cursor: pointer" wire:click="chooseDebt({{$debt}})"
                                        data-bs-toggle="modal" data-bs-target="#debtModal">
                                        <td>{{$debt->due_date}}</td>
                                        <td>{{$debt->note}}</td>
                                        <td>
                                            @if($debt->paid == 0 && $debt->debt == 0)
                                                {{ $debt->discount }}
                                            @else
                                                {{$debt->type == 'pay' ? number_format($debt->paid, 2) : number_format($debt->debt, 2)}}
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
