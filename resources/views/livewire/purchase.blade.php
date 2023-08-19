<div>
    <x-title :$title ></x-title>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card bg-white">
                <div class="card-body">
                    <form action="" wire:submit="save({{ $id }})">
                        <label for="name" class="form-label">إسم العميل</label>
                        <input type="text" wire:model="name" class="form-control" placeholder="إسم العميل ..." id="name">
                        <div>
                            @error('name') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <label for="phone" class="form-label">الهاتف</label>
                        <input type="text" wire:model="phone" class="form-control" placeholder="الهاتف ..." id="phone">
                        <div>
                            @error('phone') <span class="error text-danger">{{ $message }}</span> @enderror
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
                    @if(count($purchases) > 0)
                        <table class="table table-bordered text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>إسم العميل</th>
                                <th>الهاتف</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($purchases as $purchase)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $purchase->name }}</td>
                                    <td>{{ $purchase->phone }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white" wire:click="edit({{$purchase}})">Edit</button> /
                                        <button class="btn btn-sm btn-danger" wire:click="delete({{$purchase->id}})">delete</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-danger text-center">لايوجد عملاء ....</div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
