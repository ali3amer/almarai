<div>
    <x-title :$title ></x-title>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card bg-white mb-2">
                <div class="card-body">
                    <form action="" wire:submit="save({{ $id }})">
                        <label for="storeName" class="form-label">إسم المخزن</label>
                        <input type="text" wire:model="storeName" autocomplete="off" autofocus class="form-control" placeholder="إسم المخزن ..." id="storeName">
                        <div>
                            @error('name') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="d-grid mt-2">
                            <button class="btn btn- btn-primary">حفـــــــــــــــــــظ</button>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card bg-white">
                <div class="card-header">
                    <input wire:model.live="search" class="form-control w-50" placeholder="بحث ......">
                </div>

                <div class="card-body">
                    @if(count($stores) > 0)
                        <table class="table table-bordered text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>إسم المخزن</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody class="text-white">
                            @foreach($stores as $store)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $store->storeName }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white" wire:click="edit({{$store}})"><i class="bi bi-pen"></i></button> /
                                        <button class="btn btn-sm btn-danger" wire:loading.attr="disabled" wire:click="delete({{$store->id}})"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-danger text-center">لايوجد مخازن ....</div>
                    @endif

                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-header">a</div>
                <div class="card-body">a</div>
            </div>
        </div>
    </div>
</div>
