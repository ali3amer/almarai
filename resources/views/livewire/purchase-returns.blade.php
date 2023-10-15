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
                                            <td>{{$supplier[$buyer.'Name']}}</td>
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
                                        <div class="col"><h6>{{$currentPurchase['purchase_date']}}</h6></div>
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
                                            <td>{{$currentPurchase['total_amount']}}</td>
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

    <div class="row my-2">
        <div class="col-4">
            <div class="card mb-2">
                <div class="card-body">
                    <input type="text" autocomplete="off" class="form-control text-center" style="cursor: pointer"
                           wire:model.live="currentSupplier.{{$buyer.'Name'}}" readonly placeholder="اسم المورد ...."
                           data-bs-toggle="modal"
                           data-bs-target="#suppliersModal">
                </div>
            </div>

            @if(!empty($currentSupplier))
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-3"><h6>فواتير {{$currentSupplier[$buyer.'Name']}}</h6></div>
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
                                        <td>{{number_format($purchase['total_amount'], 2)}}</td>
                                        <td>{{$purchase['purchase_date']}}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning text-white"
                                                    wire:click="getReturns({{$purchase}})"><i class="bi bi-eye"></i>
                                            </button>
                                            /
                                            <button data-bs-toggle="modal" data-bs-target="#purchaseModal"
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

                            <input type="text" autocomplete="off" id="amount" disabled wire:model="amount"
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
                            <label for="return_date">تاريخ الارجاع</label>
                            <input type="date" @disabled(empty($currentDetail)) wire:model="return_date"
                                   class="form-control text-center">
                        </div>

                        <div class="col d-flex align-items-end">
                            <button
                                @disabled(empty($currentDetail) || ($quantityReturn == 0) || ($quantityReturn == null) || ($quantityReturn > $quantity)) class="btn {{ $editMode ? 'btn-success' : 'btn-primary' }} "
                                wire:click="save()">حفـــــــــــــــظ</button>
                        </div>
                    </div>
                </div>
            </div>
            @if(!empty($returns) && !empty($currentPurchase))
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-4"><h6>المنتجات المرجعه بفاتورة رقم {{ $currentPurchase['id'] }}</h6></div>
                                <div class="col"><h6>{{$currentPurchase['purchase_date']}}</h6></div>
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
                                        <td>{{$return['return_date']}}</td>
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
