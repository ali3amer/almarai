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
                            <tr class="{{ array_key_exists($product['id'], $cart) ? 'visually-hidden' : '' }}">
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
            @if(!empty($cart))
                <div class="card bg-white">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-2 align-middle">

                            </div>
                            <div class="col-8">
                                <input wire:model.live="search" class="form-control w-50" placeholder="بحث ......">
                            </div>
                        </div>

                    </div>

                    <div class="card-body">
                        <table class="table table-bordered text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>إسم المنتج</th>
                                <th>سعر الوحده</th>
                                <th>الكميه</th>
                                <th>السعر</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($cart as $item)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $item['name'] }}</td>
                                    <td><input type="text" style="width: 100px" wire:model="cart.{{$item['id']}}.price" wire:change="calcPrice({{$item['id']}})"></td>
                                    <td><input type="number" style="width: 100px" wire:model="cart.{{$item['id']}}.quantity" wire:change="calcPrice({{$item['id']}})"></td>
                                    <td>{{ $this->cart[$item['id']]['amount'] ?? '' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" wire:click="deleteList({{ $item['id'] }})">
                                            -
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
