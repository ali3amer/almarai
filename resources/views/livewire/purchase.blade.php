<div>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
        Launch static backdrop modal
    </button>

@if(!empty($chosenSupplier))
        <x-modal :$title :$chosenSupplier ></x-modal>
@endif

    <!-- Modal -->
    <x-title :$title></x-title>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card bg-white">
                <div class="card-header">
                    <input wire:model.live="search" class="form-control w-50" placeholder="بحث ......">
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>إسم المنتج</th>
                            <th>التحكم</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $product->name }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info text-white" wire:click="add({{$product}})">+</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-8">
            @if(!empty($chosenSupplier))
                <div class="card bg-white">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-2 align-middle">
                                {{ $chosenSupplier['name'] }}
                            </div>
                            <div class="col-8">
                                <input wire:model.live="search" class="form-control w-50" placeholder="بحث ......">
                            </div>
                        </div>

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
                                            <button class="btn btn-sm btn-info text-white" wire:click="edit({{$purchase}})">
                                                Edit
                                            </button>
                                            /
                                            <button class="btn btn-sm btn-danger" wire:click="delete({{$purchase->id}})">
                                                delete
                                            </button>
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
            @endif

        </div>
    </div>
</div>
