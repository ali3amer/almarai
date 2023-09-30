<div>

    <div wire:ignore.self class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="employeeModalLabel">العملاء</h1>
                </div>
                <div class="modal-body">
                    <div class="card bg-white">
                        <div class="card-body">
                            <form action="" wire:submit="save({{ $id }})">
                                <label for="employeeName" class="form-label">إسم الموظف</label>
                                <input type="text" wire:model="employeeName" class="form-control"
                                       placeholder="إسم الموظف ..." id="employeeName">
                                <div>
                                    @error('employeeName') <span
                                        class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                                <label for="employeeName" class="form-label">المرتب</label>
                                <input type="text" wire:model="salary" class="form-control" placeholder="المرتب"
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
            </div>
        </div>
    </div>

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
                                        <td>{{$details[0]->sale->total_amount}}</td>
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
            <div class="col-5">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-2">
                                    <button wire:click="resetData()"
                                            @disabled(!Auth::user()->hasPermission('employees-create')) data-bs-toggle="modal"
                                            data-bs-target="#employeeModal"
                                            class="btn btn-primary">
                                        <i class="bi bi-plus"></i></button>
                                </div>
                                <div class="col"><input wire:model.live="search" class="form-control"
                                                        placeholder="بحث ......"></div>
                            </div>
                        </div>
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
                                                <button data-bs-toggle="modal"
                                                        @disabled(!Auth::user()->hasPermission('employees-update')) data-bs-target="#employeeModal"
                                                        class="btn btn-sm btn-info text-white"
                                                        wire:click="edit({{$employee}})">
                                                    <i class="bi bi-pen"></i></button>
                                                /
                                                <button class="btn btn-sm btn-danger"
                                                        @disabled(!Auth::user()->hasPermission('employees-delete') || count($employee->sales) > 0 || count($employee->gifts) > 0) wire:click="delete({{$employee->id}})">
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
            <div class="col-7">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">
                                <input type="text" readonly wire:click="resetData()" style="cursor:pointer;"
                                       class="form-control text-center border-danger"
                                       wire:model.live="currentEmployee.employeeName">
                            </div>
                            <div class="col-3">
                                <input type="date" class="form-control text-center" wire:model.live="gift_date">
                            </div>
                            <div class="col-2">
                                <select id="payment" @disabled(!empty($debts)) class="form-select text-center"
                                        wire:model.live="payment">
                                    <option value="cash">كاش</option>
                                    <option value="bank">بنك</option>
                                </select>
                                <div>
                                    @error('payment') <span class="error text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-3">
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
                        </div>
                        <div class="row mt-2">
                            <div class="col-3">
                                <input type="text" class="form-control text-center"
                                       @disabled($payment == 'cash') placeholder="رقم الإيصال ...."
                                       wire:model.live="bank">
                            </div>

                            <div class="col-3">
                                <input type="text" class="form-control text-center"
                                       @disabled(!empty($debts)) placeholder="المبلغ ...."
                                       wire:model.live="gift_amount">
                            </div>

                            <div class="col-4">
                                <input type="text" class="form-control text-center" placeholder="ملاحظات ...."
                                       wire:model.live="note">
                            </div>
                            <div class="col-2">
                                @if($editGiftMode)
                                    <button class="btn btn-success w-100"
                                            wire:click="updateGift({{$currentGift['id']}})">تعديل
                                    </button>
                                @else
                                    <button class="btn btn-primary w-100" wire:click="payGift()">دفع</button>
                                @endif
                            </div>
                        </div>

                        @if(!empty($debts))
                            <div class="mt-1">
                                @foreach($debts as $key => $debt)
                                    <button type="button" class="btn btn-outline-danger"
                                            wire:click="deleteDebt({{$key}})">{{$debt}}</button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                       <div class="scroll">
                           <table class="table text-center">
                               <thead>
                               <tr>
                                   <th>#</th>
                                   <th>التاريخ</th>
                                   <th>المبلغ</th>
                                   <th>ملاحظات</th>
                                   <th>التحكم</th>
                               </tr>
                               </thead>
                               <tbody>
                               @if(!empty($gifts))
                                   @foreach($gifts as $gift)
                                       <tr>
                                           <td>{{$loop->index + 1}}</td>
                                           <td>{{$gift->gift_date}}</td>
                                           <td>{{number_format($gift->gift_amount, 2)}}</td>
                                           <td>{{$gift->note}}</td>
                                           <td>
                                               <button class="btn btn-sm btn-info text-white"
                                                       wire:click="editGift({{$gift}})"><i class="bi bi-pen"></i></button>
                                               /
                                               <button class="btn btn-sm btn-danger"
                                                       wire:click="deleteGift({{$gift->id}})"><i class="bi bi-trash"></i>
                                               </button>
                                           </td>
                                       </tr>
                                   @endforeach
                               @endif
                               </tbody>
                           </table>
                       </div>
                    </div>
                </div>
            </div>
            <div class="col-5">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-6">
                                    <h6>
                                        <span>مجموع المطالبات : </span><span>{{number_format($total_sum_paid, 2)}}</span>
                                    </h6>
                                </div>
                                <div class="col-6">
                                    <input type="text" placeholder="بحث ...."
                                           class="form-control text-center" wire:keydown="getSales()"
                                           wire:model="saleSearch">
                                </div>
                            </div>

                        </div>
                        <div class="scroll">
                            <table class="table text-center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>المتبقي</th>
                                    <th>التحكم</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($editGiftMode)
                                    @foreach($sales as $sale)
                                        @if(key_exists($sale->id, $oldDebts))
                                            <tr class="border-success">
                                                <td>{{$sale->id}}</td>
                                                <td>{{$sale->sale_date}}</td>
                                                <td>{{ $sale->saleDebts->last()->paid}}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-success" wire:click="addDebt({{$sale}})">
                                                        <i
                                                            class="bi bi-plus"></i></button>
                                                    /
                                                    <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal"
                                                            data-bs-target="#saleModal" wire:click="showSale({{$sale}})"><i
                                                            class="bi bi-eye"></i></button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                @foreach($sales as $sale)
                                    @if($sale->saleDebts->last()->remainder != 0)
                                        <tr>
                                            <td>{{$sale->id}}</td>
                                            <td>{{$sale->sale_date}}</td>
                                            <td>{{ $sale->saleDebts->last()->remainder}}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" wire:click="addDebt({{$sale}})"><i
                                                        class="bi bi-plus"></i></button>
                                                /
                                                <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal"
                                                        data-bs-target="#saleModal" wire:click="showSale({{$sale}})"><i
                                                        class="bi bi-eye"></i></button>
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
