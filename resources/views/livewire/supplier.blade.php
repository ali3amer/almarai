<div>
    <x-title :$title ></x-title>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card bg-white">
                <div class="card-body">
                    <form action="" wire:submit="save({{ $id }})">
                        <label for="supplierName" class="form-label">إسم المورد</label>
                        <input type="text" wire:model="supplierName" class="form-control" placeholder="إسم المورد ..." id="supplierName">
                        <div>
                            @error('supplierName') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <label for="phone" class="form-label">الهاتف</label>
                        <input type="text" wire:model="phone" class="form-control" placeholder="الهاتف ..." id="phone">
                        <div>
                            @error('phone') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>

                        <label for="address" class="form-label">العنوان</label>
                        <input type="text" wire:model="address" class="form-control" placeholder="العنوان ..." id="address">
                        <div>
                            @error('address') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>

                        <label for="initialBalance" class="form-label">الرصيد الافتتاحي</label>
                        <input type="text" wire:model="initialBalance" class="form-control" placeholder="الرصيد الافتتاحي ..." id="initialBalance">
                        <div>
                            @error('initialBalance') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-grid mt-2">
                            <button @disabled(!Auth::user()->hasPermission('suppliers-create')) class="btn btn btn-{{$id == 0 ? 'primary' : 'success'}}">{{$id == 0 ? 'حفـــــــــــــــــــظ' : 'تعــــــــــــــديل'}}</button>
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
                    @if(count($suppliers) > 0 && Auth::user()->hasPermission('suppliers-read'))
                        <table class="table text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>إسم المورد</th>
                                <th>الهاتف</th>
                                <th>الرصيد الافتتاحي</th>
                                <th>الرصيد الحالي</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody class="text-white">
                            @foreach($suppliers as $supplier)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $supplier->supplierName }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>{{ number_format($supplier->initialBalance, 2) }}</td>
                                    <td>{{ number_format($supplier->currentBalance, 2) }}</td>
                                    <td>
                                        <button @disabled(!Auth::user()->hasPermission('suppliers-update')) class="btn btn-sm btn-info text-white" wire:click="edit({{$supplier}})"><i class="bi bi-pen"></i></button> /
                                        <button @disabled(!Auth::user()->hasPermission('suppliers-delete')) class="btn btn-sm btn-danger" wire:click="delete({{$supplier->id}})"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-danger text-center">لايوجد موردين ....</div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
