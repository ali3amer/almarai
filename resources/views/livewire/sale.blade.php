<div>
    <x-title :$title>{{ $currentClient['clientName'] ?? '' }}</x-title>

    <x-modal title="العملاء">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <div class="row">
                        <div class="col-4 align-self-center"><h5>العملاء</h5></div>
                        <div class="col-6"><input type="text" placeholder="بحث ..." class="form-control"
                                                  wire:model.live="clientSearch"></div>
                    </div>
                </div>
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">إسم العميل</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($clients as $client)
                        <tr style="cursor: pointer" wire:click="chooseClient({{$client}})" data-bs-dismiss="modal"
                            aria-label="Close">
                            <td scope="row">{{$loop->index + 1}}</td>
                            <td>{{$client->clientName}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-modal>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-4 align-self-center"><h5>المنتجات</h5></div>
                            <div class="col-8"><input type="text" placeholder="بحث ..." class="form-control"
                                                      wire:model.live="productSearch"></div>
                        </div>
                    </div>
                    <table class="table table-responsive overflow-scroll" style="max-height: 200px">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">إسم المنتج</th>
                            <th scope="col">سعر الوحده</th>
                            <th scope="col">الكميه</th>
                            <th scope="col">التحكم</th>
                        </tr>
                        </thead>
                        <tbody style="max-height: 300px">
                        @foreach($products as $product)
                            @if(!key_exists($product->id, $cart))
                                <tr wire:click="chooseProduct({{$product}})" style="cursor: pointer">
                                    <td scope="row">{{$loop->index + 1}}</td>
                                    <td>{{$product->productName}}</td>
                                    <td>{{number_format($product->sale_price, 2)}}</td>
                                    <td>{{number_format($product->stock, 2)}}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm">+</button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if(!empty($currentClient))
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <label for="productName">إسم المنتج</label>
                        <input type="text" id="productName" class="form-control" disabled
                               wire:model="currentProduct.productName">
                        <label for="sale_price">سعر الوحده</label>
                        <input type="text" id="sale_price" class="form-control"
                               {{ empty($currentProduct) ? 'disabled' : '' }} wire:model.live="currentProduct.sale_price">
                        <label for="quantity">الكميه</label>
                        <input type="text" id="quantity" class="form-control"
                               {{ empty($currentProduct) ? 'disabled' : '' }} wire:model.live="currentProduct.quantity">
                        <label for="amount">الجمله</label>
                        <input type="text" class="form-control" disabled
                               value="{{ !empty($currentProduct) ? number_format($currentProduct['sale_price'] * $currentProduct['quantity'], 2) : '' }}">

                        <div wire:click="addToCart()"
                             class="btn btn-primary d-block {{ empty($currentProduct) ? 'disabled' : '' }} text-white mt-2">
                            حـــــــــــــــــفظ
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-5">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <h5>الفاتوره</h5>
                        </div>
                        <table class="table text-center table-responsive table-responsive table-responsive">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">إسم المنتج</th>
                                <th scope="col">سعر الوحده</th>
                                <th scope="col">الكميه</th>
                                <th scope="col">الجمله</th>
                                <th scope="col">التحكم</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($cart as $item)
                                <tr style="cursor: pointer" class="align-items-center">
                                    <td scope="row">{{$loop->index + 1}}</td>
                                    <td>{{$item['productName']}}</td>
                                    <td>{{number_format($item['sale_price'], 2)}}</td>
                                    <td>{{number_format($item['quantity'], 2)}}</td>
                                    <td>{{number_format($item['amount'], 2)}}</td>
                                    <td>
                                        <button wire:click="deleteFromCart({{$item['id']}})"
                                                class="btn btn-primary btn-sm btn-danger"><i
                                                class="bi bi-trash-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>الجمله</td>
                                <td>{{$total_amount}}</td>
                            </tr>
                            <tr>
                                <td>التخفيض</td>
                                <td><input type="number" min="0" wire:keydown.debounce.150ms="calcDiscount()"
                                           wire:model.live.debounce.150ms="discount" class="form-control text-center">
                                </td>
                            </tr>
                            <tr>
                                <td>المدفوع</td>
                                <td>{{$paid}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
