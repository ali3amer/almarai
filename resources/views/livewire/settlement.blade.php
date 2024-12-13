<div>
    <x-title :$title :$show/>
    {{--    <livewire:Title :$title />--}}

    <div class="row mt-2">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-3"><h5>المنتجات</h5></div>
                            <div class="col"><input type="text" placeholder="إسم المنتج ..."
                                                    class="form-control text-center" autocomplete="off" wire:model.live="productsSearch">
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
        <div class="col-2">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"></div>
                    <label for="productName">إسم المنتج</label>
                    <input type="text" id="productName" disabled wire:model="currentProduct.productName" class="mb-2 form-control text-center"
                           placeholder="إسم المنتج">
                    <label for="type">نوع العملية</label>
                    <select @disabled(empty($currentProduct)) wire:model.live="type"
                            class="form-select">
                        <option>إختر نوع العملية</option>
                        <option value="increment">إضافة</option>
                        <option value="decrement">تقليل</option>
                    </select>
                    <label for="quantity">الكميه</label>
                    <input type="text" @disabled(empty($currentProduct)) id="quantity" wire:model.live="quantity" autocomplete="off" class="form-control text-center"
                           placeholder="الكمية">
                    <label for="note">ملاحظه</label>
                    <input type="text" id="note" @disabled(empty($currentProduct)) wire:model.live="note" autocomplete="off" class="form-control text-center"
                           placeholder="ملاحظه">

                    <button @disabled($type == null) @disabled($note == null || $note == '') @disabled(empty($currentProduct) || (floatval($quantity) == 0)) class="btn {{ $id == 0 ? 'btn-primary' : 'btn-success' }} w-100 mt-2" wire:click="save()">{{$id == 0 ? 'حفـــــــــــــــظ' : 'تعـــــــــــــديل'}}</button>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><h6>التسويات</h6></div>
                    <div class="scroll">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>إسم المنتج</th>
                                <th>نوع العملية</th>
                                <th>الكمية التالفه</th>
                                <th>التاريخ</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($settlements as $settlement)
                                <tr>
                                    <td>{{$settlement->product->productName}}</td>
                                    <td>{{$settlement->type == 'increment' ? 'إضافة' : 'تقليل'}}</td>
                                    <td>{{$settlement->quantity}}</td>
                                    <td>{{$settlement->due_date}}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" wire:click="edit({{$settlement}})"><i class="bi bi-pen"></i></button> /
                                        <button class="btn btn-sm btn-danger" wire:click="deleteMessage({{$settlement}})"><i class="bi bi-trash"></i></button>
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
