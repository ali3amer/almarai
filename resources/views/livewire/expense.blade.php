<div>
    <x-title :$title></x-title>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card bg-white">
                <div class="card-body">
                    <form action="" wire:submit="save({{ $id }})">
                        <label for="description" class="form-label">البيان</label>
                        <input type="text" wire:model="description" class="form-control" placeholder="البيان ..."
                               name="description" id="description">
                        <div>
                            @error('description') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <label for="payment" class="form-label">طريقة الدفع</label>
                        <select id="payment" class="form-select" wire:model="payment">
                            <option value="cash">كاش</option>
                            <option value="bank">بنك</option>
                        </select>
                        <div>
                            @error('payment') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>

                        <label for="bank_id" class="form-label">البنك</label>
                        <select id="bank_id" class="form-select" wire:model="bank_id">
                            @foreach($banks as $bank)
                                <option value="{{$bank->id}}">{{$bank->bankName}}</option>
                            @endforeach
                        </select>
                        <div>
                            @error('bank_id') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>

                        <label for="bank" class="form-label">رقم الإيصال</label>
                        <input type="text" wire:model="bank" class="form-control" placeholder="رقم الإيصال ..."
                               id="bank">
                        <div>
                            @error('description') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>

                        <label for="amount" class="form-label">المبلغ</label>
                        <input type="text" wire:model="amount" class="form-control" placeholder="المبلغ ....."
                               name="amount" id="amount">
                        <div>
                            @error('amount') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <label for="expense_date" class="form-label">التاريخ</label>
                        <input type="date" wire:model="expense_date" class="form-control" placeholder="التاريخ ....."
                               name="expense_date" id="expense_date">
                        <div>
                            @error('expense_date') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="d-grid mt-2">
                            <button
                                @disabled(!Auth::user()->hasPermission('expenses-create')) class="btn btn- btn-primary">
                                حفـــــــــــــــــــظ
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <input wire:model.live="search" class="form-control w-50" placeholder="بحث ......">
                </div>

                <div class="card-body">
                    @if(count($expenses) > 0 && Auth::user()->hasPermission('expenses-read'))
                        <table class="table text-center">
                            <thead>
                            <tr>
                                <th>#</th>
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
                                    <td>{{ $expense->description }}</td>
                                    <td>{{ number_format($expense->amount, 2) }}</td>
                                    <td>{{ $expense->expense_date }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white"
                                                @disabled(!Auth::user()->hasPermission('expenses-update')) wire:click="edit({{$expense}})">
                                            <i class="bi bi-pen"></i></button>
                                        /
                                        <button class="btn btn-sm btn-danger"
                                                @disabled(!Auth::user()->hasPermission('expenses-delete')) wire:click="delete({{$expense->id}})">
                                            <i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-danger text-center">لاتوجد مصروفات ....</div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
