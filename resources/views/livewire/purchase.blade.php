<div wire:keydown.escape="resetData('currentSupplier')">
    <x-title :$title/>

    <!-- Show Purchase Model -->

    <div wire:ignore.self class="modal fade" id="showPurchaseModal" tabindex="-1"
         aria-labelledby="showPurchaseModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="showPurchaseModalLabel">فاتوره</h1>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-body">
                                <div class="card-title">
                                    <div class="row">
                                        <div class="col-4"><h5>الفاتوره {{$id != 0 ? '#'. $id : ''}}</h5></div>
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
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($cart as $item)
                                        <tr class="align-items-center">
                                            <td scope="row">{{$loop->index + 1}}</td>
                                            <td>{{$item['productName']}}</td>
                                            <td>{{number_format(floatval($item['purchase_price']), 2)}}</td>
                                            <td>{{number_format(floatval($item['quantity']), 2)}}</td>
                                            <td>{{number_format($item['amount'], 2)}}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>الجمله</td>
                                        <td>{{number_format($total_amount, 2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>المدفوع</td>
                                        <td>{{number_format(floatval($paid), 2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>المتبقي</td>
                                        <td>{{number_format($total_amount - floatval($paid), 2)}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Print Invoice Modal -->
    <div wire:ignore.self class="modal fade" id="printModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header d-print-none">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                        <button class="btn btn-primary" @click="window.print()"><i class="bi bi-printer"></i>
                        </button>
                    </h1>
                </div>
                <div class="modal-body">
                    <livewire:invoice />
                </div>
            </div>
        </div>
    </div>


    <div class="row mt-2 d-print-none">
        @if(!empty($currentSupplier))

            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editPurchase" wire:click="showPurchases"
                                style="cursor: pointer"><i class="bi bi-pen"></i></button>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#printModal"
                                style="cursor: pointer" wire:click="save()" {{empty($cart) ? 'disabled':''}} ><i
                                class="bi bi-bookmark-check"></i></button>
                        <button class="btn btn-danger"
                                wire:click="resetData('currentSupplier')" {{empty($currentSupplier) ? 'disabled':''}}><i
                                class="bi bi-x"></i></button>

                        {{$currentSupplier['supplierName']}}
                        <div class="card-title mt-2">
                            <div class="row">
                                <div class="col-4 align-self-center"><h5>المنتجات</h5></div>
                                <div class="col-8"><input type="text" placeholder="بحث ..."
                                                          wire:keydown.enter="chooseProduct({{$products[0]}})"
                                                          class="form-control"
                                                          wire:model.live="productSearch"></div>
                            </div>
                        </div>
                        <div class="scroll">
                            <table class="table text-center">
                                <thead>
                                <tr>
                                    <th scope="col" style="width: 10px">#</th>
                                    <th scope="col">إسم المنتج</th>
                                    <th scope="col">سعر الوحده</th>
                                    <th scope="col">الكميه</th>
                                    <th scope="col">التحكم</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $product)
                                    @if(!key_exists($product->id, $cart))
                                        <tr style="cursor: pointer">
                                            <td scope="row">{{$loop->index + 1}}</td>
                                            <td>{{$product->productName}}</td>
                                            <td>{{number_format($product->purchase_price, 2)}}</td>
                                            <td>{{number_format($product->stock, 2)}}</td>
                                            <td>
                                                <button
                                                    {{ $product->stock < 1 ? "disabled" : "" }} wire:click="chooseProduct({{$product}})"
                                                    class="btn btn-primary btn-sm">+
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @if(!$editMode)
                <div class="col-2">
                    <div class="card">
                        <form wire:submit="addToCart()">
                            <div class="card-body">
                                <label for="productName">إسم المنتج</label>
                                <input type="text" id="productName" class="form-control" disabled
                                       wire:model="currentProduct.productName">
                                <label for="purchase_price">سعر الوحده</label>
                                <input type="text" id="purchase_price" class="form-control"
                                       {{ empty($currentProduct) ? 'disabled' : '' }} wire:model.live="currentProduct.purchase_price">
                                <label for="quantity">الكميه</label>
                                <input type="text" id="quantity" class="form-control"
                                       {{ empty($currentProduct) ? 'disabled' : '' }} wire:model.live="currentProduct.quantity">
                                <label for="amount">الجمله</label>
                                <input type="text" class="form-control" disabled
                                       value="{{ !empty($currentProduct) ? number_format(floatval($currentProduct['purchase_price']) * floatval($currentProduct['quantity']), 2) : '' }}">

                                <button type="submit"
                                        class="btn btn-primary d-block {{ empty($currentProduct) ? 'disabled' : '' }} text-white mt-2 w-100">
                                    إضــــــــــافة
                                </button>

                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <div class="row">
                                    <div class="col-4"><h5>الفاتوره {{$id != 0 ? '#'. $id : ''}}</h5></div>
                                    <div class="col-4"><input type="date" wire:model.live="purchase_date"
                                                              class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <select wire:model.live="payment" class="form-select">
                                            <option value="cash">كاش</option>
                                            <option value="bank">بنك</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-1">
                                    <div class="col-4"><input type="text" placeholder="رقم الاشعار ....."
                                                              @disabled($payment == 'cash') wire:model.live="bank"
                                                              class="form-control"></div>

                                    <div class="col-4">
                                        <select @disabled($payment == 'cash') wire:model.live="bank_id" class="form-select">
                                            @foreach($banks as $bank)
                                                <option value="{{$bank->id}}">{{$bank->bankName}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="scroll">
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
                                            <td>{{number_format(floatval($item['purchase_price']), 2)}}</td>
                                            <td>{{number_format(floatval($item['quantity']), 2)}}</td>
                                            <td>{{number_format($item['amount'], 2)}}</td>
                                            <td>
                                                <button wire:loading.attr="disabled" wire:click="deleteFromCart({{$item['id']}})"
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
                                        <td>المدفوع</td>
                                        <td><input type="text" min="0" wire:keydown.debounce.150ms="calcRemainder()"
                                                   wire:model.live.debounce.150ms="paid"
                                                   class="form-control text-center">
                                        </td>
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
                </div>
            @else
                <div class="col-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <div class="row">
                                    <div class="col-3"><h5>الفواتير</h5></div>
                                    <div class="col-9">
                                        <input type="text" placeholder="بحث ..." class="form-control mb-2 text-center"
                                               wire:model.live="purchaseSearch">
                                    </div>
                                </div>
                            </div>
                            <div class="scroll">
                                <div class="accordion" id="accordionExample">
                                    @if(!empty($purchases))
                                        @foreach($purchases as $purchase)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapse.{{$purchase->id}}"
                                                            aria-expanded="false"
                                                            aria-controls="collapse.{{$purchase->id}}">
                                                        {{$purchase->id}}: {{$purchase->purchase_date}}
                                                    </button>
                                                </h2>
                                                <div id="collapse.{{$purchase->id}}" class="accordion-collapse collapse"
                                                     data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <table class="table table-responsive" data-bs-dismiss="modal"
                                                               aria-label="Close"
                                                               wire:click="choosePurchase({{$purchase}})">
                                                            <thead class="table-dark">
                                                            <tr>
                                                                <th>إسم المنتج</th>
                                                                <th>السعر</th>
                                                                <th>الكميه</th>
                                                                <th>الجمله</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($purchase->purchaseDetails as $detail)
                                                                <tr>
                                                                    <td>{{$detail->product->productName}}</td>
                                                                    <td>{{number_format($detail->price, 2)}}</td>
                                                                    <td>{{$detail->quantity}}</td>
                                                                    <td>{{number_format($detail->price*$detail->quantity, 2)}}</td>
                                                                </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td>الجمله:</td>
                                                                <td>{{number_format($purchase->total_amount, 2)}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>المدفوع:</td>
                                                                <td></td>
                                                            </tr>
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
            @endif
        @else
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-4 align-self-center"><h5>العملاء</h5></div>
                                <div class="col-8"><input type="text" placeholder="بحث ..." class="form-control"
                                                          wire:keydown.enter="chooseSupplier({{$suppliers[0]}})"
                                                          wire:model.live="supplierSearch"></div>
                            </div>
                        </div>
                        <div class="scroll">
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">إسم العميل</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($suppliers as $supplier)
                                    <tr style="cursor: pointer" wire:click="chooseSupplier({{$supplier}})"
                                        data-bs-dismiss="modal"
                                        aria-label="Close">
                                        <td scope="row">{{$loop->index + 1}}</td>
                                        <td>{{$supplier->supplierName}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
