<div>

    <!-- Choose Client Modal -->
    <div wire:ignore.self class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="exampleModalLabel"
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
                                @if(!empty($clients))
                                    @foreach($clients as $client)
                                        <tr style="cursor: pointer" wire:click="chooseClient({{$client}})"
                                            data-bs-dismiss="modal"
                                            aria-label="Close">
                                            <td scope="row">{{$loop->index + 1}}</td>
                                            <td>{{$client->clientName}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Choose Supplier Modal -->
    <div wire:ignore.self class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="supplierModalLabel">الموردين</h1>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <div class="row">
                                    <div class="col-4 align-self-center"><h5>الموردين</h5></div>
                                    <div class="col-6"><input type="text" placeholder="بحث ..." class="form-control"
                                                              wire:model.live="supplierSearch"></div>
                                </div>
                            </div>
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
                                            <td>{{$supplier->supplierName}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Choose Product Modal -->
    <div wire:ignore.self class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="productModalLabel">الموردين</h1>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <div class="row">
                                    <div class="col-4 align-self-center"><h5>المنتجات</h5></div>
                                    <div class="col-6"><input type="text" placeholder="بحث ..." class="form-control"
                                                              wire:model.live="productSearch"></div>
                                </div>
                            </div>
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">إسم المنتج</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($products))
                                    @foreach($products as $product)
                                        <tr style="cursor: pointer" wire:click="chooseProduct({{$product}})"
                                            data-bs-dismiss="modal"
                                            aria-label="Close">
                                            <td scope="row">{{$loop->index + 1}}</td>
                                            <td>{{$product->productName}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <x-title :$title/>
    <button class="d-print-none btn btn-primary position-fixed z-3" style="bottom: 10px; border-radius: 50%"
            type="button"
            data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling"><i
            class="bi bi-gear"></i></button>

    <button class="d-print-none btn btn-secondary position-fixed z-3" id="print"
            style="bottom: 10px; right: 60px; border-radius: 50%"
            type="button"><i
            class="bi bi-printer"></i></button>

    <div wire:ignore.self class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="false"
         tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">التقارير</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><h6>نوع وفترة التقرير</h6></div>
                    <label for="reportType">نوع التقرير</label>
                    <select class="form-select" wire:model.live="reportType" id="reportType">
                        @foreach($reportTypes as $key => $type)
                            <option value="{{$key}}">{{$type}}</option>
                        @endforeach
                    </select>

                    @if($reportType != 'inventory')
                        <label for="reportType">فترة التقرير</label>
                        <select @disabled($reportType == 0) class="form-select" wire:model.live="reportDuration"
                                id="reportType">
                            @foreach($reportDurations as $key => $duration)
                                <option value="{{$key}}">{{$duration}}</option>
                            @endforeach
                        </select>
                    @endif

                    @if($reportType == 'client')
                        <label for="client">العميل</label>
                        <input data-bs-toggle="modal" wire:model="currentClient.clientName" readonly
                               placeholder="إسم العميل ...." class="form-control" data-bs-target="#clientModal">
                    @elseif($reportType == 'supplier')
                        <label for="client">المورد</label>
                        <input data-bs-toggle="modal" wire:model="currentSupplier.supplierName" readonly
                               placeholder="إسم المورد ...." class="form-control" data-bs-target="#supplierModal">
                    @endif
                    @if($reportType =='inventory' || $reportType =='sales' || $reportType =='purchases')
                        <label for="store_id">المخزن</label>
                        <select class="form-select mt-2" wire:model.live="store_id" id="store_id">
                            <option value="0">-----------------</option>
                            @foreach($stores as $store)
                                <option value="{{$store->id}}">{{$store->storeName}}</option>
                            @endforeach
                        </select>

                        @if($store_id != 0)
                            <label for="product">المورد</label>
                            <input id="product" data-bs-toggle="modal" wire:model="currentProduct.productName" readonly
                                   placeholder="إسم ألمنتج ...." class="form-control" data-bs-target="#productModal">
                        @endif
                    @endif
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-body">
                    @if($reportDuration == 'day')
                        <label for="day">من</label>
                        <input type="date" class="form-control" wire:model.live="day" id="day">
                    @elseif($reportDuration == 'duration')
                        <label for="from">من</label>
                        <input type="date" class="form-control" wire:model.live="from" id="from">
                        <label for="to">الى</label>
                        <input type="date" class="form-control" wire:model.live="to" id="to">
                    @endif
                    <button class="btn btn-primary w-100 mt-2" wire:click="chooseReport()">جلب التقرير
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($reportType == 'general')
        <div class="card mt-2">
            <div class="card-body">
                <table class="table text-center">
                    <thead>
                    <tr>
                        <th>البيان</th>
                        <th>الجمله</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>المبيعات</td>
                        <td>{{number_format($salesSum, 2)}}</td>
                    </tr>
                    <tr>
                        <td>المشتريات</td>
                        <td>{{number_format($purchasesSum, 2)}}</td>
                    </tr>
                    <tr>
                        <td>المصروفات</td>
                        <td>{{number_format($expensesSum, 2)}}</td>
                    </tr>
                    <tr>
                        <td>مصروفات الموظفين</td>
                        <td>{{number_format($employeesSum, 2)}}</td>
                    </tr>
                    <tr>
                        <td>التالف</td>
                        <td>{{number_format($damagedsSum, 2)}}</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>الجمله</th>
                        <th>{{ number_format($salesSum - $purchasesSum - $expensesSum - $employeesSum - $damagedsSum, 2) }}</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @elseif($reportType == 'inventory' && !empty($products))
        <div class="card mt-2">
            <div class="card-body">
                <table class="table text-center d-print-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>إسم المنتج</th>
                        <th>الكميه</th>
                        <th>سعر الجرد</th>
                        <th>الجمله</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{$loop->index + 1}}</td>
                            <td>{{$product->productName}}</td>
                            <td>{{number_format($product->stock, 2)}}</td>
                            <td>{{number_format($product->purchase_price, 2)}}</td>
                            <td>{{ number_format($product->stock * $product->purchase_price, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="4">الجمــــــــــــــــــله</td>
                        <td>{{number_format($sum, 2)}}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @elseif($reportType == 'client' && !empty($saleDebts))
        <div class="card mt-2">
            <div class="card-body">
                <div class="card-title"><h5>{{$currentClient['clientName']}}</h5></div>
                <table class="table text-center">
                    <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>رقم الفاتوره</th>
                        <th>البيان</th>
                        <th>عليه</th>
                        <th>له</th>
                        <th>الرصيد</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($saleDebts as $debt)
                        <tr>
                            <td>{{$debt->due_date}}</td>
                            <td>{{$debt->sale_id}}</td>
                            <td>{{ $debt->paid == 0 ? 'تم الشراء بالآجل' : 'تم لإستلام مبلغ' }}</td>
                            <td>{{number_format($debt->remainder, 2)}}</td>
                            <td>{{number_format($debt->paid, 2)}}</td>
                            <td>{{number_format($debt->client_balance, 2)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($reportType == 'supplier'  && !empty($purchaseDebts))
        <div class="card mt-2">
            <div class="card-body">
                <div class="card-title"><h5>{{$currentSupplier['supplierName']}}</h5></div>
                <table class="table text-center">
                    <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>رقم الفاتوره</th>
                        <th>البيان</th>
                        <th>عليه</th>
                        <th>له</th>
                        <th>الرصيد</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($purchaseDebts as $debt)
                        <tr>
                            <td>{{$debt->due_date}}</td>
                            <td>{{$debt->sale_id}}</td>
                            <td>{{ $debt->paid == 0 ? 'تم الشراء بالآجل' : 'تم لإستلام مبلغ' }}</td>
                            <td>{{number_format($debt->remainder, 2)}}</td>
                            <td>{{number_format(floatval($debt->paid), 2)}}</td>
                            <td>{{number_format($debt->supplier_balance, 2)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($reportType == 'safe')

    @elseif($reportType == 'sales' && !empty($sales))
        <div class="card mt-2">
            <div class="card-body">
                <div class="card-title">
                   <div class="row">
                       <div class="col-3">
                           <h5>المبيعات</h5>
                       </div>
                       <div class="col-3">
                           <input type="text" wire:model.live="percent" placeholder="نسبة الربح" class="form-control text-center">
                       </div>
                   </div>
                </div>
                <table class="table text-center demo">
                    <thead>
                    <tr>
                        <th>رقم الفاتوره</th>
                        <th>العميل</th>
                        <th>إسم المنتج</th>
                        <th>سعر البيع</th>
                        <th>الكميه</th>
                        <th>الجمله</th>
                        <th>التاريخ</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sales as $sale)
                        <tr>
                            <td>{{$sale->sale_id}}</td>
                            <td>{{$sale->sale->client->clientName}}</td>
                            <td>{{ $sale->product->productName }}</td>
                            <td>{{number_format($sale->price, 2)}}</td>
                            <td>{{$sale->quantity}}</td>
                            <td>{{number_format($sale->quantity * $sale->price, 2)}}</td>
                            <td>{{$sale->sale_date}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5">الجــــــــــــــــــــملة</td>
                        <td>{{number_format($sum, 2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="5">الأرباح</td>
                        <td>{{ number_format($sum * $percent / 100, 2) }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @elseif($reportType == 'purchases' && !empty($purchases))
        <div class="card mt-2">
            <div class="card-body">
                <div class="card-title"><h5>المشتريات</h5></div>
                <table class="table text-center printThis">
                    <thead>
                    <tr>
                        <th>رقم الفاتوره</th>
                        <th>المورد</th>
                        <th>إسم المنتج</th>
                        <th>سعر الشراء</th>
                        <th>الكميه</th>
                        <th>الجمله</th>
                        <th>التاريخ</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($purchases as $purchase)
                        <tr>
                            <td>{{$purchase->purchase_id}}</td>
                            <td>{{$purchase->purchase->supplier->supplierName}}</td>
                            <td>{{ $purchase->product->productName }}</td>
                            <td>{{number_format($purchase->price, 2)}}</td>
                            <td>{{$purchase->quantity}}</td>
                            <td>{{number_format($purchase->quantity * $purchase->price, 2)}}</td>
                            <td>{{$purchase->purchase_date}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5">الجــــــــــــــــــــملة</td>
                        <td>{{number_format($sum, 2)}}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

</div>
