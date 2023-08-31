<div>


    <!-- Modal -->
    <x-title :$title></x-title>

    <div class="row mt-2">
        @if(empty($currentSupplier))
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <h4>إختر المورد</h4>
                    </div>
                    <div class="card-body">
                        <table class="table text-center table-bordered">
                            <tr>
                                <th colspan="2">
                                    <input wire:model.live="supplierSearch" class="form-control"
                                           placeholder="بحث ......">
                                </th>
                            </tr>
                            @foreach($suppliers as $supplier)
                                <tr>
                                    <td>{{ $supplier->supplierName }}</td>
                                    <td>
                                        <button wire:click="chooseSupplier({{$supplier}})"
                                                class="btn btn-sm btn-primary">+
                                        </button>
                                        /
                                        <button wire:click="edit({{$supplier->id}})" class="btn btn-sm btn-success">+
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            @if($editMode && !empty($purchases))
                <div class="col-6">
                    @foreach($purchases as $purchase)
                        <div class="card">
                            <div class="card-header" data-bs-toggle="collapse" data-bs-target="{{'#collapseExample'.$purchase->id}}"
                                 aria-expanded="false" aria-controls="{{'collapseExample'.$purchase->id}}"><h4>{{$purchase->id}}</h4></div>
                            <div class="card-body collapse" id="{{'collapseExample'.$purchase->id}}">
                                <table class="table" wire:click="choosePurchase({{$purchase}})">
                                    <tr>
                                        <td>إسم المنتج</td>
                                        <td>سعر الوحده</td>
                                        <td>الكميه</td>
                                        <td>الجمله</td>
                                    </tr>
                                    @foreach($purchase->purchaseDetails as $detail)
                                        <tr>
                                            <td>{{ $detail->product->productName }}</td>
                                            <td>{{ $detail->price }}</td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>{{ $detail->price * $detail->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="col-4">
                <div class="card bg-white">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">
                                <input wire:model.live="search" class="form-control" placeholder="بحث ......">
                            </div>
                            <div class="col-6">
                                <input readonly wire:click="chooseSupplier()" class="form-control"
                                       style="cursor: pointer" wire:model="currentSupplier.name"
                                       placeholder="المورد ....">
                            </div>
                        </div>
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
                                    <td>{{ $product->productName }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white"
                                                wire:click="chooseProduct({{$product}})">+
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if(!empty($currentProduct))
                <div class="col-3">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{$currentProduct['productName']}}</h4>
                        </div>
                        <div class="card-body">
                            <form action="" wire:submit="add({{ $currentProduct['id'] }})">
                                <label for="price" class="form-label">سعر الوحد</label>
                                <input type="text" wire:keydown="calcPrice()" wire:model.live="currentProduct.price"
                                       class="form-control text-center" placeholder="سعر الوحده ..." id="price">
                                <label for="quantity" class="form-label">الكميه</label>
                                <input type="text" wire:keydown="calcPrice()" wire:model.live="currentProduct.quantity"
                                       class="form-control text-center" placeholder="الكميه ..." id="quantity">
                                <div class="my-2">
                                    <label for="amount"
                                           class="form-label">الجمله</label> {{ number_format($currentProduct['amount'], 2) }}
                                </div>
                                <div class="d-grid mt-2">
                                    <button wire:loading.attr="disabled" class="btn btn- btn-primary">
                                        حفـــــــــــــــــــظ
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-5">
                @if(!empty($cart))
                    <div class="card bg-white">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-4 align-self-center">
                                    <span>الجمله</span>
                                    <span>{{ number_format($total_amount, 2) }}</span>
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
                                        <td>{{ $item['productName'] }}</td>
                                        <td>{{ number_format($item['price'], 2) }}</td>
                                        <td>{{ number_format($item['quantity'], 2) }}</td>
                                        <td>{{ number_format($item['quantity'] * $item['price'], 2) }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-danger"
                                                    wire:click="deleteList({{ $item['id'] }})"> -
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>

                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div
                                    class="col-3 text-center align-self-center">{{ number_format($total_amount, 2) }}</div>
                                <div class="col-3"><input wire:model="discount" class="form-control"
                                                          placeholder="التخفيض ...." wire:keydown="calcDiscount()">
                                </div>
                                <div class="col-3 text-center align-self-center">{{ number_format($paid, 2) }}</div>
                                <div class="col-3">
                                    <button class="btn btn-primary" wire:click="save()">save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        @endif

    </div>
</div>
