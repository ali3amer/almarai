<div>
    <x-title :$title></x-title>
    {{--<livewire:Title :$title />--}}
    <div class="row mt-2">
        <div class="col-4">
            <div class="card bg-white">
                <div class="card-body">
                    @if(!$optionsMode)
                        <form action="" wire:submit="save({{ $id }})">
                            <label for="option_id" class="form-label">البند</label>
                            <select id="option_id" @disabled(count($options) == 0) class="form-select"
                                    wire:model.live="option_id">
                                <option value="0">---------------------------</option>
                                @foreach($options as $option)
                                    <option value="{{$option->id}}">{{ $option->optionName }}</option>
                                @endforeach
                            </select>
                            <div>
                                @error('option_id') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                            <label for="description" class="form-label">البيان</label>
                            <input type="text" autocomplete="off" wire:model="description" class="form-control"
                                   placeholder="البيان ..."
                                   name="description" id="description">
                            <div>
                                @error('description') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                            <label for="payment" class="form-label">طريقة الدفع</label>
                            <select id="payment" @disabled($banks->count() == 0) class="form-select"
                                    wire:model.live="payment">
                                <option value="cash">كاش</option>
                                <option value="bank">بنك</option>
                            </select>
                            <div>
                                @error('payment') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>

                            <label for="bank_id" class="form-label">البنك</label>
                            <select @disabled($payment == "cash") @disabled($banks->count() == 0) id="bank_id"
                                    class="form-select" wire:model="bank_id">
                                @foreach($banks as $bank)
                                    <option value="{{$bank->id}}">{{$bank->bankName}}</option>
                                @endforeach
                            </select>
                            <div>
                                @error('bank_id') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>

                            <label for="bank" class="form-label">رقم الإيصال</label>
                            <input @disabled($payment == "cash") @disabled($banks->count() == 0) type="text"
                                   autocomplete="off" wire:model="bank" class="form-control" placeholder="رقم الإيصال ..."
                                   id="bank">
                            <div>
                                @error('bank') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>

                            <label for="amount" class="form-label">المبلغ</label>
                            <input type="text" autocomplete="off" wire:model.live="amount" class="form-control"
                                   placeholder="المبلغ ....."
                                   name="amount" id="amount">
                            <div>
                                @error('amount') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                            <label for="expense_date" class="form-label">التاريخ</label>
                            <input type="date" disabled autocomplete="off" wire:model="expense_date" class="form-control text-center"
                                   placeholder="التاريخ ....."
                                   name="expense_date" id="expense_date">
                            <div>
                                @error('expense_date') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="d-grid mt-2">
                                <button
                                    @disabled(!$create) @disabled($amount <= 0) class="btn btn- btn-{{$id==0?'primary':'success'}}">
                                    {{$id == 0 ? 'حفـــــــــــــــــــظ' : 'تعـــــــــــــــديل'}}
                                </button>
                            </div>

                        </form>
                    @else
                        <form action="" wire:submit="saveOption()">
                            <label for="description" class="form-label">البيان</label>
                            <input type="text" autocomplete="off" wire:model="optionName" class="form-control"
                                   placeholder="البيان ..."
                                   name="optionName" id="optionName">
                            <div>
                                @error('optionName') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="d-grid mt-2">
                                <button
                                    @disabled(!$create) class="btn btn- btn-{{$optionId==0?'primary':'success'}}">
                                    {{$optionId == 0 ? 'حفـــــــــــــــــــظ' : 'تعـــــــــــــــديل'}}
                                </button>
                            </div>

                        </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <input autocomplete="off" wire:model.live="search" class="form-control"
                                   placeholder="بحث ......">
                        </div>
                        <div class="col-6 align-items-end">
                            <button wire:click="changeMode" class="btn btn-{{ $optionsMode ? "danger" : "primary" }}"><i
                                    class="bi bi-{{ $optionsMode ? "x" : "list" }}"></i></button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(!$optionsMode)
                        @if(count($expenses) > 0 && $read)
                            <div class="scroll">
                                <table class="table text-center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>البند</th>
                                        <th>البيان</th>
                                        <th>المبلغ</th>
                                        <th>التاريخ</th>
                                        <th>التحكم</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-white">
                                    @foreach($expenses as $expense)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $expense->option->optionName ?? "" }}</td>
                                            <td>{{ $expense->description }}</td>
                                            <td>{{ number_format($expense->amount, 2) }}</td>
                                            <td>{{ $expense->expense_date }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info text-white"
                                                        @disabled(!$update) wire:click="edit({{$expense}})">
                                                    <i class="bi bi-pen"></i></button>
                                                /
                                                <button class="btn btn-sm btn-danger"
                                                        @disabled(!$delete) wire:click="deleteMessage({{$expense}})">
                                                    <i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-danger text-center">لاتوجد مصروفات ....</div>
                        @endif
                    @else
                        @if($read && count($options) > 0)
                            <div class="scroll">
                                <table class="table text-center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>البيان</th>
                                        <th>التحكم</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-white">
                                    @foreach($options as $option)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $option->optionName }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info text-white"
                                                        @disabled(!$update) wire:click="editOption({{$option}})">
                                                    <i class="bi bi-pen"></i></button>
                                                /
                                                <button class="btn btn-sm btn-danger"
                                                        @disabled(!$delete) wire:click="deleteOptionMessage({{$option}})">
                                                    <i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-danger text-center">لاتوجد بنود مصروفات ....</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
