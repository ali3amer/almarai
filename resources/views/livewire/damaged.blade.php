<div>
    <x-title :$title/>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-3"><h5>المنتجات</h5></div>
                            <div class="col"><input type="text" placeholder="إسم المنتج ..."
                                                    class="form-control text-center" wire:model.live="productsSearch">
                            </div>
                        </div>
                    </div>
                    <div class="scroll">
                        <table class="table text-center table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>إسم المنتج</th>
                                <th>الكميه</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $product)
                                @if($product->stock > 0)
                                    <tr style="cursor: pointer"  wire:click="chooseProduct({{$product}})">
                                        <td>{{$loop->index + 1}}</td>
                                        <td>{{$product->productName}}</td>
                                        <td>{{$product->stock}}</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"></div>
                    <label for="productName">إسم المنتج</label>
                    <input type="text" id="productName" disabled wire:model="currentProduct.productName" class="mb-2 form-control text-center"
                           placeholder="إسم المنتج">
                    <label for="quantity">الكميه التالفه</label>
                    <input type="text" id="quantity" wire:model.live="quantity" class="form-control text-center"
                           placeholder="الكمية التالفه">
                    <label for="quantity">التاريخ</label>
                    <input type="date" id="damaged_date" wire:model.live="damaged_date" class="form-control text-center">


                    <button @disabled(empty($currentProduct) || ($quantity == 0)) class="btn {{ $id == 0 ? 'btn-primary' : 'btn-success' }} w-100 mt-2" wire:click="save()">{{$id == 0 ? 'حفـــــــــــــــظ' : 'تعـــــــــــــديل'}}</button>
                </div>
            </div>
        </div>

        <div class="col-5">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><h6>المنتجات التالفه</h6></div>
                    <div class="scroll">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>إسم المنتج</th>
                                <th>الكمية التالفه</th>
                                <th>التاريخ</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($damageds as $damaged)
                                <tr>
                                    <td>{{$damaged->product->productName}}</td>
                                    <td>{{$damaged->quantity}}</td>
                                    <td>{{$damaged->damaged_date}}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" wire:click="edit({{$damaged}})"><i class="bi bi-pen"></i></button> /
                                        <button class="btn btn-sm btn-danger" wire:click="delete({{$damaged}})"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
