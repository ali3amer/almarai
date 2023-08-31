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
                    @if(count($suppliers) > 0)
                        <table class="table table-bordered text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>إسم المورد</th>
                                <th>الهاتف</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody class="text-white">
                            @foreach($suppliers as $supplier)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $supplier->supplierName }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white" wire:click="edit({{$supplier}})">Edit</button> /
                                        <button class="btn btn-sm btn-danger" wire:click="delete({{$supplier->id}})">delete</button>
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
