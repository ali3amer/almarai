<div>
    <x-title :$title>{{ $currentClient['clientName'] ?? '' }}</x-title>
    <!-- Edit Purchase Modal -->
    <div wire:ignore.self class="modal fade" id="editPurchase" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="exampleModalLabel">الفواتير</h1>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <input type="text" placeholder="بحث ..." class="form-control"
                                   wire:model.live="saleSearch">
                            <div class="accordion" id="accordionExample">
                                @if(!empty($sales))
                                    @foreach($sales as $sale)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse.{{$sale->id}}" aria-expanded="false" aria-controls="collapse.{{$sale->id}}">
                                                    {{$sale->id}}: {{$sale->sale_date}}
                                                </button>
                                            </h2>
                                            <div id="collapse.{{$sale->id}}" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <table class="table table-responsive" data-bs-dismiss="modal" aria-label="Close" wire:click="chooseSale({{$sale}})">
                                                        <thead>
                                                            <tr>
                                                                <th>إسم المنتج</th>
                                                                <th>السعر</th>
                                                                <th>الكميه</th>
                                                                <th>الجمله</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($sale->saleDetails as $detail)
                                                            <tr>
                                                                <td>{{$detail->product->productName}}</td>
                                                                <td>{{$detail->price}}</td>
                                                                <td>{{$detail->quantity}}</td>
                                                                <td>{{$detail->price*$detail->quantity}}</td>
                                                            </tr>
                                                        @endforeach
                                                        <tr><td>الجمله: </td><td>{{$sale->total_amount}}</td></tr>
                                                        <tr><td>التخفيض: </td><td>{{$sale->discount}}</td></tr>
                                                        <tr><td>المدفوع: </td><td>{{$sale->saleDebts[0]['paid']}}</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Choose Client Modal -->
    <div wire:ignore.self class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="exampleModalLabel">العملاء</h1>
                </div>
                <div class="modal-body">
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
                                    <tr style="cursor: pointer" wire:click="chooseClient({{$client}})"
                                        data-bs-dismiss="modal"
                                        aria-label="Close">
                                        <td scope="row">{{$loop->index + 1}}</td>
                                        <td>{{$client->clientName}}</td>
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

    <!-- Print Invoice Modal -->
    <div wire:ignore.self class="modal fade" id="printModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-print-none">
                    <button type="button" wire:click="printInvoice(false)" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="exampleModalLabel"><button class="btn btn-primary" wire:click="printInvoice(true)"><i class="bi bi-printer"></i></button></h1>
                </div>
                <div class="modal-body">
                    <div class="card d-print-table">
                        <div class="card-body">
                            <div class="card-title">
                                <h5>الفاتوره {{$id != 0 ? '#'. $id : ''}}</h5>
                            </div>
                            <table class="table text-center table-responsive table-responsive table-responsive">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">إسم المنتج</th>
                                    <th scope="col">سعر الوحده</th>
                                    <th scope="col">الكميه</th>
                                    <th scope="col">الجمله</th>
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
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>الجمله</td>
                                    <td>{{number_format($total_amount, 2)}}</td>
                                </tr>
                                <tr>
                                    <td>التخفيض</td>
                                    <td>{{number_format($discount, 2)}}</td>
                                </tr>
                                <tr>
                                    <td>المدفوع</td>
                                    <td>{{number_format($paid, 2)}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row mt-2 d-print-none">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"
                            style="cursor: pointer"><i class="bi bi-plus-square"></i></button>
                    <button class="btn btn-warning" {{empty($currentClient) ? 'disabled':''}} data-bs-toggle="modal" data-bs-target="#editPurchase"
                            style="cursor: pointer"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#printModal"
                            style="cursor: pointer"  wire:click="save()"  {{empty($cart) ? 'disabled':''}} ><i class="bi bi-bookmark-check"></i></button>
                    <button class="btn btn-danger"  wire:click="resetData()" {{empty($currentClient) ? 'disabled':''}}><i class="bi bi-x"></i></button>
                    {{ $currentClient['clientName'] ?? '' }}
                    <div class="card-title mt-2">
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
                                <tr style="cursor: pointer">
                                    <td scope="row">{{$loop->index + 1}}</td>
                                    <td>{{$product->productName}}</td>
                                    <td>{{number_format($product->sale_price, 2)}}</td>
                                    <td>{{number_format($product->stock, 2)}}</td>
                                    <td>
                                        <button {{ $product->stock < 1 ? "disabled" : "" }} wire:click="chooseProduct({{$product}})" class="btn btn-primary btn-sm">+</button>
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
            <div class="col-2">
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

            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-4"><h5>الفاتوره {{$id != 0 ? '#'. $id : ''}}</h5></div>
                                <div class="col-4"><input type="date" wire:model.live="sale_date" class="form-control"></div>
                                <div class="col-4">
                                    <select wire:model.live="payment" class="form-select">
                                        <option value="cash">كاش</option>
                                        <option value="bank">بنك</option>
                                    </select>
                                </div>
                                <div class="col-4 mt-1"><input type="text" placeholder="رقم الاشعار ....." @disabled($payment == 'cash') wire:model.live="bank" class="form-control"></div>
                            </div>

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
                                <td>{{number_format($total_amount, 2)}}</td>
                            </tr>
                            <tr>
                                <td>التخفيض</td>
                                <td><input type="number" min="0" wire:keydown.debounce.150ms="calcDiscount()"
                                           wire:model.live.debounce.150ms="discount" class="form-control text-center">
                                </td>
                            </tr>
                            <tr>
                                <td>المدفوع</td>
                                <td><input type="number" min="0" wire:keydown.debounce.150ms="calcRemainder()"
                                           wire:model.live.debounce.150ms="paid" class="form-control text-center"></td>
                            </tr>
                            <tr>
                                <td>المتبقي</td>
                                <td>{{number_format($remainder, 2)}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
