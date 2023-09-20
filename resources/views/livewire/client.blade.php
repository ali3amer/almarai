<div>
    <x-title :$title ></x-title>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card bg-white">
                <div class="card-body">
                    <form action="" wire:submit="save({{ $id }})">
                        <label for="clientName" class="form-label">إسم العميل</label>
                        <input type="text" wire:model="clientName" class="form-control" placeholder="إسم العميل ..." id="clientName">
                        <div>
                            @error('clientName') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <label for="phone" class="form-label">الهاتف</label>
                        <input type="text" wire:model="phone" class="form-control" placeholder="الهاتف ..." id="phone">
                        <div>
                            @error('phone') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <label for="initialBalance" class="form-label">الرصيد الافتتاحي</label>
                        <input type="text" wire:model="initialBalance" class="form-control" placeholder="الرصيد الافتتاحي ..." id="initialBalance">
                        <div>
                            @error('initialBalance') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-grid mt-2">
                            <button @disabled(!Auth::user()->hasPermission('clients-create')) class="btn btn- btn-{{$id == 0 ? 'primary' : 'success'}}">{{$id == 0 ? 'حفـــــــــــــــــــظ' : 'تعـــــــــــــــــديل'}}</button>
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
                    @if(count($clients) > 0 && Auth::user()->hasPermission('clients-read'))
                        <table class="table text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>إسم العميل</th>
                                <th>الهاتف</th>
                                <th>الرصيد الافتتاحي</th>
                                <th>الرصيد الحالي</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clients as $client)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $client->clientName }}</td>
                                    <td>{{ $client->phone }}</td>
                                    <td>{{ number_format($client->initialBalance, 2) }}</td>
                                    <td>{{ number_format($client->currentBalance, 2) }}</td>
                                    <td>
                                        <button @disabled(!Auth::user()->hasPermission('clients-update')) class="btn btn-sm btn-info text-white" wire:click="edit({{$client}})"><i class="bi bi-pen"></i></button> /
                                        <button @disabled(!Auth::user()->hasPermission('clients-delete')) class="btn btn-sm btn-danger" wire:click="delete({{$client->id}})"><i class="bi bi-trash"></i></button>
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
