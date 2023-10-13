<div>

    <!-- Show Sale -->

    <div wire:ignore.self class="modal fade" id="saleModal" tabindex="-1" aria-labelledby="saleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="saleModalLabel">فاتوره</h1>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <table class="table text-center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>إسم المنتج</th>
                                    <th>سعر الوحده</th>
                                    <th>الكميه</th>
                                    <th>الجمله</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($details))
                                    @foreach($details as $detail)
                                        <tr>
                                            <td>{{$detail['id']}}</td>
                                            <td>{{$detail['product']['productName']}}</td>
                                            <td>{{number_format($detail['price'], 2)}}</td>
                                            <td>{{$detail['quantity']}}</td>
                                            <td>{{number_format($detail['price'] * $detail['quantity'], 2)}}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td rowspan="4">الجمله</td>
                                        <td>{{number_format($details[0]->sale->total_amount, 2)}}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-title :$title></x-title>

    <div class="row mt-2">
        @if(empty($currentEmployee))
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <form action="" wire:submit="save({{ $id }})">
                            <label for="employeeName" class="form-label">إسم الموظف</label>
                            <input type="text" autocomplete="off" wire:model.live="employeeName" class="form-control"
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
                            <div class="d-grid mt-2">
                                <button type="submit"
                                        @disabled($employeeName == '' || $salary <= 0)  data-bs-dismiss="modal"
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
                        @if(count($employees) > 0 && Auth::user()->hasPermission('employees-read'))
                            <div class="scroll">
                                <table class="table text-center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>إسم الموظف</th>
                                        <th>المرتب</th>
                                        <th>التحكم</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-white">
                                    @foreach($employees as $employee)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $employee->employeeName }}</td>
                                            <td>{{ number_format($employee->salary, 2) }}</td>
                                            <td>
                                                <button
                                                    @disabled(!Auth::user()->hasPermission('employees-update')) data-bs-target="#employeeModal"
                                                    class="btn btn-sm btn-info text-white"
                                                    wire:click="edit({{$employee}})">
                                                    <i class="bi bi-pen"></i></button>
                                                /
                                                <button class="btn btn-sm btn-danger"
                                                        @disabled(!Auth::user()->hasPermission('employees-delete') || count($employee->sales) > 0 || count($employee->gifts) > 0) wire:click="deleteMessage({{$employee}})">
                                                    <i
                                                        class="bi bi-trash"></i></button>
                                                /
                                                <button class="btn btn-sm btn-warning text-white"
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
            <div class="col-12 mb-2">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <label for="employeeName">إسم الموظف</label>
                                <input id="employeeName" @click="$dispatch('reset-employee', { data: 'currentEmployee' })" type="text" readonly
                                       style="cursor:pointer;"
                                       class="form-control text-center border-danger"
                                       wire:model.live="currentEmployee.employeeName">
                            </div>
                            <div class="col-2">
                                <label for="gift_date">التاريخ</label>
                                <input type="date" class="form-control text-center" id="gift_date"
                                       wire:model.live="gift_date">
                            </div>
                            <div class="col-2">
                                <label for="payment">طريقة الدفع</label>
                                <select id="payment" class="form-select text-center"
                                        wire:model.live="payment">
                                    <option value="cash">كاش</option>
                                    <option value="bank">بنك</option>
                                </select>
                                <div>
                                    @error('payment')
                                    <span class="error text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-2">
                                <label for="bank_id">البنك</label>
                                <select id="bank_id" @disabled($payment == 'cash') class="form-select text-center"
                                        wire:model="bank_id">
                                    @foreach($banks as $bank)
                                        <option value="{{$bank->id}}">{{$bank->bankName}}</option>
                                    @endforeach
                                </select>
                                <div>
                                    @error('bank_id') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-3">
                                <label for="payment">رقم الايصال</label>
                                <input type="text" id="payment" autocomplete="off" class="form-control text-center"
                                       @disabled($payment == 'cash') placeholder="رقم الإيصال ...."
                                       wire:model.live="bank">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-2">
                                <label for="gift_amount">المرتب</label>
                                <input type="text" id="gift_amount"  wire:keydown="calcRemainder()" autocomplete="off" class="form-control text-center"
                                        placeholder="المبلغ ...."
                                       wire:model.live="gift_amount">
                            </div>

                            <div class="col-2">
                                <label for="gift_amount">سداد</label>
                                <input type="text" id="paid" wire:keydown="calcRemainder()" autocomplete="off" class="form-control text-center"
                                       placeholder="سداد ...."
                                       wire:model.live="paid">
                            </div>

                            <div class="col-3">
                                <label for="gift_amount">متبقي المرتب</label>
                                <input type="text" id="remainder" disabled autocomplete="off" class="form-control text-center"
                                       placeholder="سداد ...."
                                       wire:model.live="remainder">
                            </div>

                            <div class="col-3">
                                <label for="note">ملاحظات</label>

                                <input type="text" id="note" autocomplete="off" class="form-control text-center"
                                       placeholder="ملاحظات ...."
                                       wire:model.live="note">
                            </div>

                            <div class="col-2 d-flex align-items-end">
                                @if($editGiftMode)
                                    <button class="btn btn-success w-100"
                                            wire:click="updateGift({{$gift_id}})">تعديل
                                    </button>
                                @else
                                    <button class="btn btn-primary w-100" wire:click="payGift()">دفع</button>
                                @endif
                            </div>

                        </div>


                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <h5>المرتبات</h5>
                        </div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>التاريخ</th>
                                <th>المبلغ</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($gifts as $gift)
                                <tr>
                                    <td>{{$gift->index + 1}}</td>
                                    <td>{{$gift->gift_date}}</td>
                                    <td>{{number_format($gift->gift_amount, 2)}}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" wire:click="editGift({{$gift->debt}}, {{$gift->gift_amount}})"><i class="bi bi-pen"></i></button> /
                                        <button class="btn btn-sm btn-danger" wire:click="deleteGiftMessage({{$gift}})"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-3"><h6>المعاملات</h6></div>
                                <div class="col-3"><h6>رصيد الموظف
                                        : {{ number_format($currentEmployee['currentBalance'], 2) }}</div>
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
                                        <td>{{$debt->type == 'pay' ? number_format($debt->paid, 2) : number_format($debt->debt, 2)}}</td>
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
