<div>
    <x-title :$title ></x-title>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card bg-white">
                <div class="card-body">
                    <form action="" wire:submit="save({{ $id }})">
                        <label for="description" class="form-label">البيان</label>
                        <input type="text" wire:model="description" class="form-control" placeholder="البيان ..." name="description" id="description">
                        <div>
                            @error('description') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <label for="amount" class="form-label">المبلغ</label>
                        <input type="text" wire:model="amount" class="form-control" placeholder="المبلغ ....." name="amount" id="amount">
                        <div>
                            @error('amount') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <label for="expense_date" class="form-label">التاريخ</label>
                        <input type="date" wire:model="expense_date" class="form-control" placeholder="التاريخ ....." name="expense_date" id="expense_date">
                        <div>
                            @error('expense_date') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="d-grid mt-2">
                            <button class="btn btn- btn-primary">حفـــــــــــــــــــظ</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card bg-white">
                <div class="card-header">
                    <input wire:model.live="search" class="form-control w-50" placeholder="بحث ......">
                </div>

                <div class="card-body">
                    @if(count($expenses) > 0)
                        <table class="table table-bordered text-center">
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
                                        <button class="btn btn-sm btn-info text-white" wire:click="edit({{$expense}})">Edit</button> /
                                        <button class="btn btn-sm btn-danger" wire:click="delete({{$expense->id}})">delete</button>
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
