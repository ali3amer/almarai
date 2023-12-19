<div>
        <x-title :$title :date="session('date')"  />
{{--    <livewire:Title :$title />--}}

    <div class="row mt-2">
        <div class="col-4">
            <div class="card bg-white mb-2">
                <div class="card-body">
                    <form action="" wire:submit="save({{ $id }})">
                        <label for="storeName" class="form-label">إسم المخزن</label>
                        <input autocomplete="off"  type="text" wire:model="storeName" autocomplete="off" autofocus class="form-control @error('storeName') is-invalid @enderror" placeholder="إسم المخزن ..." id="storeName">
                        <div>
                            @error('storeName') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="d-grid mt-2">
                            <button @disabled(!$create) class="btn btn btn-{{$id == 0 ? 'primary' : 'success'}}">{{$id == 0 ? 'حفـــــــــــــــــــظ' : 'تعـــــــــــديل'}}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <input autocomplete="off"  wire:model.live="search" class="form-control w-50" placeholder="بحث ......">
                </div>

                @if($read)
                <div class="card-body">
                    @if(count($stores) > 0)
                        <table class="table text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>إسم المخزن</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($stores as $store)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $store->storeName }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white"  @disabled(!$update) wire:click="edit({{$store}})"><i class="bi bi-pen"></i></button> /
                                        <button class="btn btn-sm btn-danger"  @disabled(!$delete)  wire:click="deleteMessage({{$store}})"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-danger text-center">لايوجد مخازن ....</div>
                    @endif

                </div>
                @endif
            </div>
        </div>
    </div>
</div>
