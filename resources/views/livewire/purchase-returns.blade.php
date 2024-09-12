<div>

    <!-- Supplier Modal -->
    <div wire:ignore.self class="modal fade" id="suppliersModal" tabindex="-1" aria-labelledby="suppliersModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="bankModalLabel">الموردين</h1>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <input type="text" autocomplete="off" placeholder="إسم المورد ...."
                                       wire:model.live="supplierSearch" class="form-control text-center">
                            </div>
                            <div class="scroll">
                                <table class="table table-responsive text-center">
                                    <thead>
                                    <tr>
                                        <th>إسم المورد</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($suppliers as $supplier)
                                        <tr style="cursor: pointer" wire:click="chooseSupplier({{$supplier}})"
                                            data-bs-dismiss="modal">
                                            <td>{{$supplier['name']}}</td>
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
    </div>


    <!-- Purchase Modal -->
    <div wire:ignore.self class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="purchaseModalLabel"></h1>
                </div>
                <div class="modal-body">
                    @if(!empty($currentPurchase))
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <div class="row">
                                        <div class="col-4"><h6>فاتوره رقم {{ $id }}</h6></div>
                                        <div class="col"><h6>{{$currentPurchase['due_date']}}</h6></div>
                                    </div>
                                </div>
                                <div class="scroll">
                                    <table class="table text-center table-hover">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>إسم المنتج</th>
                                            <th>سعر الوحده</th>
                                            <th>الكميه</th>
                                            <th>الجمله</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($purchaseDetails as $detail)
                                            <tr wire:click="chooseDetail({{$detail}}, {{$detail['product']}})"
                                                data-bs-dismiss="modal">
                                                <td>{{$loop->index + 1}}</td>
                                                <td>{{$detail['product']['productName']}}</td>
                                                <td>{{number_format($detail['price'], 2)}}</td>
                                                <td>{{number_format($detail['quantity'], 2)}}</td>
                                                <td>{{number_format($detail['quantity'] * $detail['price'], 2)}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td>الجمله</td>
                                            <td>{{$currentPurchase['amount']}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

        <x-title :$title/>
{{--    <livewire:Title :$title />--}}

    <div class="row my-2">
        <div class="col-4">
            <div class="card mb-2">
                <div class="card-body">
                    <input type="text" autocomplete="off" class="form-control text-center" style="cursor: pointer"
                           wire:model.live="currentSupplier.{{'name'}}" readonly placeholder="اسم المورد ...."
                           data-bs-toggle="modal"
                           data-bs-target="#suppliersModal">
                </div>
            </div>

            @if(!empty($currentSupplier))
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-3"><h6>فواتير {{$currentSupplier['name']}}</h6></div>
                                <div class="col-9"><input type="text" autocomplete="off" placeholder="رقم الفاتوره ...."
                                                          class="form-control text-center"
                                                          wire:model.live="purchaseSearch"></div>
                            </div>
                        </div>
                        <div class="scroll">
                            <table class="table text-center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المبلغ</th>
                                    <th>التاريخ</th>
                                    <th>التحكم</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($purchases as $purchase)
                                    <tr>
                                        <td>{{$purchase['id']}}</td>
                                        <td>{{number_format($purchase['amount'], 2)}}</td>
                                        <td>{{$purchase['due_date']}}</td>
                                        <td>
                                            <button @disabled(!$read) class="btn btn-sm btn-warning text-white"
                                                    wire:click="getReturns({{$purchase}})"><i class="bi bi-eye"></i>
                                            </button>
                                            /
                                            <button @disabled(!$read) data-bs-toggle="modal" data-bs-target="#purchaseModal"
                                                    wire:click="choosePurchase({{$purchase}}, false)"
                                                    class="btn btn-sm btn-danger"><i
                                                    class="bi bi-arrow-return-left"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-8">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="card-title"></div>
                    <div class="row">
                        <div class="col">
                            <label for="productName">إسم المنتج</label>
                            <input id="productName" type="text" disabled wire:model="productName"
                                   class="form-control text-center"
                                   placeholder="إسم المنتج">
                        </div>
                        <div class="col">
                            <label for="price">سعر الوحده</label>

                            <input type="text" autocomplete="off" id="price" disabled wire:model="price"
                                   class="form-control text-center"
                                   placeholder="سعر الوحده">
                        </div>
                        <div class="col">
                            <label for="quantity">الكمية</label>

                            <input type="text" autocomplete="off" id="quantity" disabled wire:model="quantity"
                                   class="form-control text-center"
                                   placeholder="الكمية">
                        </div>

                        <div class="col">
                            <label for="amount">الجمله</label>

                            <input type="text" autocomplete="off" id="amount" disabled value="{{ $price * $quantity }}"
                                   class="form-control text-center"
                                   placeholder="الجمله">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col">
                            <label for="quantityReturn">الكمية المرجعه</label>

                            <input type="text" autocomplete="off" @disabled(empty($currentDetail)) id="quantityReturn"
                                   wire:model="quantityReturn" wire:keydown="calcQuantity()"
                                   class="form-control text-center"
                                   placeholder="الكمية المرجعه">
                        </div>

                        <div class="col">
                            <label for="priceReturn">قيمة المرتجعات</label>

                            <input type="text" autocomplete="off" disabled id="priceReturn" wire:model="priceReturn"
                                   class="form-control text-center"
                                   placeholder="قيمة المرتجعات">
                        </div>

                        <div class="col">
                            <label for="amount">المبلغ المدفوع</label>

                            <input type="text" autocomplete="off" @disabled(empty($currentDetail) || (!empty($currentSupplier) && $currentSupplier['cash'])) @disabled(empty($currentPurchase) || $currentPurchase['amount'] == 0) id="amount" wire:model="amount"
                                   class="form-control text-center"
                                   placeholder="المبلغ المدفوع">
                        </div>

                        <div class="col">
                            <label for="due_date">تاريخ الارجاع</label>
                            <input type="date" disabled @disabled(empty($currentDetail)) wire:model="due_date"
                                   class="form-control text-center">
                        </div>

                        @if(!session("closed") && $update)
                            <div class="col d-flex align-items-end">
                                <button
                                    @disabled(empty($currentDetail) || ($quantityReturn == 0) || ($quantityReturn == null) || ($quantityReturn > $quantity)) class="btn {{ $editMode ? 'btn-success' : 'btn-primary' }} "
                                    wire:click="save()">حفـــــــــــــــظ</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @if(!empty($returns) && !empty($currentPurchase))
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-4"><h6>المنتجات المرجعه بفاتورة رقم {{ $currentPurchase['id'] }}</h6></div>
                                <div class="col"><h6>{{$currentPurchase['due_date']}}</h6></div>
                            </div>
                        </div>
                        <div class="scroll">
                            <table class="table table-hover text-center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>إسم المنتج</th>
                                    <th>سعر الوحده</th>
                                    <th>الكمية</th>
                                    <th>الجمله</th>
                                    <th>التاريخ</th>
                                    <th>التحكم</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($returns as $return)
                                    <tr>
                                        <td>{{$loop->index + 1}}</td>
                                        <td>{{$return['product']['productName']}}</td>
                                        <td>{{number_format($return['price'], 2)}}</td>
                                        <td>{{number_format($return['quantity'], 2)}}</td>
                                        <td>{{number_format($return['quantity'] * $return['price'], 2)}}</td>
                                        <td>{{$return['due_date']}}</td>
                                        <td>
                                            @if($return['due_date'] == session("date") && !session("closed"))
                                                <button @disabled(!$update) class="btn btn-sm btn-info"
                                                        wire:click="edit({{ $return['id'] }})"><i
                                                        class="bi bi-pen"></i></button>
                                                <button @disabled(!$delete) class="btn btn-sm btn-danger"
                                                        wire:click="deleteMessage({{ $return['id'] }})">
                                                    <i
                                                        class="bi bi-trash"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
