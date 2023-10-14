<div wire:keydown.escape.window="resetData()">
    <x-title :$title/>

    <!-- Print Invoice Modal -->
    <div wire:ignore.self class="modal fade" id="printModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-print-none">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                        @if(isset($invoice['id']))
                            <button class="btn btn-danger" wire:click="deleteMessage({{$invoice['id']}})"><i class="bi bi-trash"></i>
                            </button>
                        @endif
                        <button class="btn btn-primary" id="print"><i class="bi bi-printer"></i>
                        </button>
                    </h1>
                </div>
                <div class="modal-body">
                    <livewire:invoice/>
                </div>
            </div>
        </div>
    </div>


    <div class="row mt-2 d-print-none">
        @if(!empty($currentSupplier))

            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-warning" wire:click="showPurchases()"
                                style="cursor: pointer"><i class="bi bi-pen"></i></button>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#printModal"
                                style="cursor: pointer" wire:click="save()" {{empty($cart) ? 'disabled':''}} ><i
                                class="bi bi-bookmark-check"></i></button>
                        <button class="btn btn-danger"
                                wire:click="resetData('currentSupplier')" {{empty($currentSupplier) ? 'disabled':''}}><i
                                class="bi bi-x"></i></button>

                        {{$currentSupplier[$buyer.'Name']}}
                        <div class="card-title mt-2">
                            <div class="row">
                                <div class="col-4 align-self-center"><h5>المنتجات</h5></div>
                                <div class="col-8"><input autocomplete="off" type="text" id="productSearch"
                                                          placeholder="بحث ..."
                                                          wire:keydown.enter="chooseProduct({{$products[0]}})"
                                                          class="form-control"
                                                          wire:model.live="productSearch" autofocus></div>
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
                @if($currentSupplier['blocked'] != true)
                    <div class="col-2">
                        <div class="card">
                            <div class="card-body">
                                <label for="productName">إسم المنتج</label>
                                <input type="text" id="productName" class="form-control" disabled
                                       wire:model="currentProduct.productName">
                                <label for="price">سعر الوحده</label>
                                <input autocomplete="off" type="text" id="price" class="form-control"
                                       {{ empty($currentProduct) ? 'disabled' : '' }} wire:model.live="currentProduct.price">
                                <label for="quantity">الكميه</label>

                                <input autocomplete="off" type="text" id="quantity" wire:keydown.enter="addToCart()"
                                       class="form-control"
                                       {{ empty($currentProduct) ? 'disabled' : '' }} wire:model.live="currentProduct.quantity">
                                <label for="amount">الجمله</label>
                                <input type="text" class="form-control" disabled
                                       value="{{ !empty($currentProduct) ? number_format(floatval($currentProduct['price']) * floatval($currentProduct['quantity']), 2) : '' }}">

                                <button wire:click="addToCart()"
                                        class="btn btn-primary d-block {{ empty($currentProduct) ? 'disabled' : '' }} text-white mt-2 w-100">
                                    إضــــــــــافة
                                </button>

                            </div>
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
                                        <div class="col-4"><input autocomplete="off" type="text"
                                                                  placeholder="رقم الاشعار ....."
                                                                  @disabled($payment == 'cash') wire:model.live="bank"
                                                                  class="form-control"></div>

                                        <div class="col-4">
                                            <select wire:model.live="bank_id"
                                                    @disabled($payment == 'cash') class="form-select">
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
                                                <td>{{number_format(floatval($item['price']), 2)}}</td>
                                                <td>{{number_format(floatval($item['quantity']), 2)}}</td>
                                                <td>{{number_format($item['amount'], 2)}}</td>
                                                <td>
                                                    <button wire:loading.attr="disabled"
                                                            wire:click="deleteFromCart({{$item['id']}})"
                                                            class="btn btn-primary btn-sm btn-danger"><i
                                                            class="bi bi-trash-fill"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td>الجمله</td>
                                            <td>{{number_format($total_amount, 2)}}</td>
                                            <td>الرصيد الحالي</td>
                                            <td>{{number_format($currentBalance, 2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>المدفوع</td>
                                            <td><input autocomplete="off" type="text" min="0"
                                                       wire:keydown.debounce.150ms="calcRemainder()"
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
                @elseif($currentSupplier['blocked'] == true)
                    <div class="col-8">
                        <div class="alert alert-danger text-center
                        "><h3>هذا المورد موقوف بسبب {{ $currentSupplier['note'] }}</h3>
                        </div>

                    </div>
                @endif
            @else
                <div class="col-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title mt-2">
                                <div class="row">
                                    <div class="col-4 align-self-center"><h5>الفواتير</h5></div>
                                    <div class="col-8"><input autocomplete="off" type="text" id="purchaseSearch"
                                                              placeholder="بحث ..."
                                                              wire:keydown.enter="chooseProduct({{$products[0]}})"
                                                              class="form-control"
                                                              wire:model.live="purchaseSearch" autofocus></div>
                                </div>
                            </div>
                            <div class="scroll">
                                <table class="table text-center">
                                    <thead>
                                    <tr>
                                        <th scope="col" style="width: 10px">#</th>
                                        <th scope="col">التاريخ</th>
                                        <th scope="col">المبلغ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($purchases as $purchase)
                                        <tr style="cursor: pointer"
                                            wire:click="getPurchase({{$purchase}})" data-bs-toggle="modal" data-bs-target="#printModal">
                                            <td>{{$purchase->id}}</td>
                                            <td>{{$purchase->purchase_date}}</td>
                                            <td>{{number_format($purchase->total_amount, 2)}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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
                            @if(count($suppliers) > 0)
                                <input autocomplete="off" type="text" placeholder="بحث ..." class="form-control"
                                       wire:keydown.enter="chooseSupplier({{$suppliers[0]}})"
                                       wire:model.live="supplierSearch">
                            @endif
                        </div>
                    <div class="scroll">
                        <table class="table table-responsive">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">إسم المورد</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($suppliers))
                                @foreach($suppliers as $supplier)
                                    <tr style="cursor: pointer" wire:click="chooseSupplier({{$supplier}})"
                                        data-bs-dismiss="modal"
                                        aria-label="Close">
                                        <td scope="row">{{$loop->index + 1}}</td>
                                        <td>{{$supplier[$buyer.'Name']}}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
    @endif

</div>


<script>
    document.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {

            if (event.target.id === "productSearch") {
                document.getElementById("price").removeAttribute('disabled');
                document.getElementById("price").focus();
            } else if (event.target.id === "price") {
                document.getElementById("quantity").focus();
            } else if (event.target.id === "quantity") {
                document.getElementById("productSearch").focus();
            }
        }
    });

</script>
</div>
