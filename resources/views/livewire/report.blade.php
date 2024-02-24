<div>

    <div wire:loading class="position-fixed top-0 opacity-25 bg-dark z-3" style="width: 100%; height: 100%;">
        <div class="d-flex justify-content-center" style="height: 100%">
            <i class="spinner-border text-primary m-auto"></i>
        </div>
    </div>
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
                            <div class="scroll">
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
    </div>

    <!-- Choose Employee Modal -->
    <div wire:ignore.self class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="employeeModalLabel">الموظفين</h1>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <div class="row">
                                    <div class="col-4 align-self-center"><h5>الموظفين</h5></div>
                                    <div class="col-6"><input type="text" placeholder="بحث ..." class="form-control"
                                                              wire:model.live="employeeSearch"></div>
                                </div>
                            </div>
                            <div class="scroll">
                                <table class="table table-responsive">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">إسم الموظف</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($employees))
                                        @foreach($employees as $employee)
                                            <tr style="cursor: pointer" wire:click="chooseEmployee({{$employee}})"
                                                data-bs-dismiss="modal"
                                                aria-label="Close">
                                                <td scope="row">{{$loop->index + 1}}</td>
                                                <td>{{$employee->employeeName}}</td>
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
                            <div class="scroll">
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
    </div>

    <!-- Print Invoice Modal -->
    <div wire:ignore.self class="modal fade" id="printModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-print-none">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                        <button class="btn btn-primary" id="printInvoice"><i class="bi bi-printer"></i>
                        </button>
                    </h1>
                </div>
                <div class="modal-body">
                    <livewire:invoice/>
                </div>
            </div>
        </div>
    </div>

    <x-title :$title/>
    {{--    <livewire:Title :$title/>--}}

    <button class="d-print-none btn btn-primary position-fixed z-2" style="bottom: 10px; border-radius: 50%"
            type="button"
            data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling"><i
            class="bi bi-gear"></i></button>

    <button class="d-print-none btn btn-secondary position-fixed z-2" id="printReport"
            style="bottom: 10px; right: 60px; border-radius: 50%"
            type="button"><i
            class="bi bi-printer"></i></button>

    <button class="d-print-none btn btn-danger position-fixed z-2"
            style="bottom: 10px; right: 110px; border-radius: 50%"
            type="button" wire:click="dbBackup()"><i
            class="bi bi-recycle"></i></button>

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
                        <label for="reportDuration">فترة التقرير</label>
                        <select @disabled($reportType == 0) class="form-select" wire:model.live="reportDuration"
                                id="reportDuration">
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
                    @elseif($reportType == 'employee')
                        <label for="client">الموظف</label>
                        <input data-bs-toggle="modal" wire:model="currentEmployee.employeeName" readonly
                               placeholder="إسم الموظف ...." class="form-control" data-bs-target="#employeeModal">
                    @endif
                    @if($reportType =='inventory' || $reportType =='sales' || $reportType =='purchases' || $reportType =='tracking')
                        <label for="store_id">المخزن</label>
                        <select class="form-select mt-2" wire:model.live="store_id" id="store_id">
                            <option value="0">-----------------</option>
                            @foreach($stores as $store)
                                <option value="{{$store->id}}">{{$store->storeName}}</option>
                            @endforeach
                        </select>

                        @if($store_id != 0 && ($reportType =='sales' || $reportType =='purchases' || $reportType =='tracking'))
                            <label for="product">المنتج</label>
                            <input id="product" data-bs-toggle="modal" wire:model="currentProduct.productName" readonly
                                   placeholder="إسم المنتج ...." class="form-control" data-bs-target="#productModal">
                        @endif
                    @endif
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-body">
                    @if($reportType != 'inventory')
                        @if($reportDuration == 'day')
                            <label for="day">التاريخ</label>
                            <input type="date" class="form-control" wire:model.live="day" id="day">
                        @elseif($reportDuration == 'duration')
                            <label for="from">من</label>
                            <input type="date" class="form-control" wire:model.live="from" id="from">
                            <label for="to">الى</label>
                            <input type="date" class="form-control" wire:model.live="to" id="to">
                        @endif
                    @endif
                    <button
                        @disabled($reportType == "employee" && empty($currentEmployee)) @disabled($reportType == "tracking" && empty($currentProduct)) @disabled($reportType == 'supplier' && empty($currentSupplier))  @disabled($reportType == 'client' && empty($currentClient)) @disabled($reportDuration == 'day' && $day == '') @disabled($reportDuration == 'duration' && $from == '') @disabled($reportDuration == 'duration' && $to == '') class="btn btn-primary w-100 mt-2"
                        wire:click="chooseReport()">جلب التقرير
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($reportType == 'general')

        <div class="card mt-2">
            <div class="card-body invoice">
                <table class="text-center printInvoice" dir="rtl">
                    <thead>
                    <tr>
                        <th colspan="2">الأصول</th>
                        <th colspan="2">الخصوم</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>قيمة البضاعه الموجودة</td>
                        <td>{{number_format($stock, 2)}}</td>
                        <td>الدائنون (الموردين)</td>
                        <td>{{number_format($creditors, 2)}}</td>
                    </tr>
                    <tr>
                        <td>رصيد البنك</td>
                        <td>{{number_format($bankBalance, 2)}}</td>
                        <td>أمانات طرفنا</td>
                        <td>{{ number_format($deposits, 2) }}</td>
                    </tr>
                    <tr>
                        <td>النقديه بالخزنه</td>
                        <td>{{number_format($balance, 2)}}</td>
                        <td rowspan="3">رأس المال</td>
                        <td rowspan="3">{{number_format($capital, 2)}}</td>
                    </tr>
                    <tr>
                        <td>المدينون</td>
                        <td>{{number_format($owe, 2)}}</td>
                    </tr>
                    <tr>
                        <td>مصروفات مدفوعه مقدماً</td>
                        <td>{{number_format($totalExpenses, 2)}}</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>الجمله</th>
                        <th>{{ number_format($assets, 2) }}</th>
                        <th>الجمله</th>
                        <th>{{ number_format($adversaries, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4">صافي الارباح = الأصول - الخصوم</th>
                    </tr>
                    <tr>
                        <th colspan="4">{{ number_format($assets - $adversaries, 2) }}</th>
                    </tr>

                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body invoice">
                <table class="text-center printInvoice" dir="rtl">
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
                        <td>مدفوعات المبيعات</td>
                        <td>{{number_format($salesPaidSum, 2)}}</td>
                    </tr>
                    <tr>
                        <td>المشتريات</td>
                        <td>{{number_format($purchasesSum, 2)}}</td>
                    </tr>
                    <tr>
                        <td>مدفوعات المشتريات</td>
                        <td>{{number_format($purchasesPaidSum, 2)}}</td>
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
                    <tr>
                        <th>الخزنة</th>
                        <th>{{number_format($safeBalance, 2)}}</th>
                    </tr>
                    <tr>
                        <td>ديون مبيعات</td>
                        <td>{{number_format($salesDebts, 2)}}</td>
                    </tr>
                    <tr>
                        <td>ديون مشتريات</td>
                        <td>{{number_format($purchasesDebts, 2)}}</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>الجمله</th>
                        <th>{{ number_format($total, 2) }}</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card mt-2">
            @php $clientsBalance = 0 @endphp
            @php $clientBalance = 0 @endphp
            <div class="card-body invoice">
                <div class="card-title" dir="rtl">
                    <h2>العملاء</h2>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice" dir="rtl">
                        <thead>
                        <tr>
                            <th>إسم العميل</th>
                            <th>الرصيد</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($clients))
                            @foreach($clients as $client)
                                @php $clientBalance = $client->debts->sum("debt") - $client->debts->sum("paid") + $client->initialBalance  @endphp
                                @php $clientsBalance += $clientBalance  @endphp
                                <tr>
                                    <td>{{ $client->clientName }}</td>
                                    <td>{{ number_format($clientBalance , 2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>الجمله</th>
                            <th>{{ number_format($clientsBalance , 2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body invoice">
                <div class="card-title" dir="rtl">
                    <h2>الموردين</h2>
                </div>
                <div class="scroll">
                    @php $suppliersBalance = 0; @endphp
                    @php $supplierBalance = 0; @endphp
                    <table class="text-center printInvoice" dir="rtl">
                        <thead>
                        <tr>
                            <th>إسم المورد</th>
                            <th>الرصيد</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($suppliers))

                            @foreach($suppliers as $supplier)
                                @php $supplierBalance = $supplier->purchaseDebts->sum("debt") - $supplier->purchaseDebts->sum("paid") - $supplier->saleDebts->sum("debt") - $supplier->saleDebts->sum("paid") + $supplier->initialBalance - $supplier->initialSalesBalance; @endphp
                                @php $suppliersBalance += $supplierBalance; @endphp
                                <tr>
                                    <td>{{ $supplier->supplierName }}</td>
                                    <td>{{ number_format($supplierBalance , 2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>الجمله</th>
                            <th>{{ number_format($suppliersBalance , 2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    @elseif($reportType == 'inventory' && !empty($products))
        <div class="card mt-2">
            <div class="card-body invoice">
                <div class="scroll">
                    <table class="text-center printInvoice">
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
                            <th colspan="4">الجمــــــــــــــــــله</th>
                            <th>{{number_format($sum, 2)}}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @elseif($reportType == 'client' && !empty($currentClient))

        @if(!empty($saleDebts))
            <div class="card mt-2">
                <div class="card-body invoice" dir="rtl">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-4"><h5>مبيعات
                                    : {{$currentClient['clientName'] ?? ''}}</h5>
                            </div>
                            <div class="col-4">
                                <h5>الرصيد الافتتاحي : {{number_format($currentClient['initialBalance'] ?? 0, 2)}}</h5>
                            </div>
                            <div class="col-4"><h5>الرصيد : {{number_format($salesBalance, 2)}}</h5></div>
                        </div>
                    </div>
                    <div class="scroll">
                        <table class="text-center printInvoice">
                            <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>البيان</th>
                                <th>عليه</th>
                                <th>له</th>
                                <th>الرصيد</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $currentBalance = 0;
                            @endphp
                            @foreach($saleDebts as $debt)
                                <tr>
                                    @php $currentBalance += $debt->debt - $debt->paid @endphp
                                    <td>{{$debt->due_date}}</td>
                                    <td @if($debt->sale_id != null || $debt->purchase_id != null) data-bs-toggle="modal"
                                        data-bs-target="#printModal"
                                        wire:click="getInvoice({{$debt}})" @endif >{{ $debt->note }}</td>
                                    <td>{{number_format($debt->debt, 2)}}</td>
                                    <td>{{number_format($debt->paid, 2)}}</td>
                                    <td>{{number_format($currentBalance, 2)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-body invoice" dir="rtl">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-4"><h5>مبيعات
                                    : {{$currentClient['clientName'] ?? ''}}</h5>
                            </div>
                            <div class="col-4">
                                <h5>الرصيد الافتتاحي : {{number_format($currentClient['initialBalance'] ?? 0, 2)}}</h5>
                            </div>
                            <div class="col-4"><h5>الرصيد : {{number_format($salesBalance, 2)}}</h5></div>
                        </div>
                    </div>
                    <div class="scroll">
                        <table class="text-center printInvoice">
                            <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>البيان</th>
                                <th>سعر الوحده</th>
                                <th>الكمية</th>
                                <th>الجمله</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $currentBalance = 0;
                            @endphp
                            @foreach($saleDebts as $debt)
                                <tr>
                                    @php $currentBalance += $debt->debt - $debt->paid @endphp
                                    <td>{{$debt->due_date}}</td>
                                    <td colspan="3"
                                        @if($debt->sale_id != null || $debt->purchase_id != null) data-bs-toggle="modal"
                                        data-bs-target="#printModal"
                                        wire:click="getInvoice({{$debt}})" @endif>{{ $debt->note }}</td>
                                    <td>{{$debt->type == "debt" ? number_format($debt->debt, 2) : number_format($debt->paid, 2)}}</td>
                                </tr>
                                @if($debt->type == 'debt' && $debt->sale_id != null)
                                    @foreach($debt->sale->saleDetails as $product)
                                        <tr>
                                            <td>{{ $debt->sale->sale_date }}</td>
                                            <td>{{ $product->product->productName }}</td>
                                            <td>{{ number_format($product->price,2) }}</td>
                                            <td>{{ number_format($product->quantity,2) }}</td>
                                            <td>{{ number_format($product->quantity * $product->price,2) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    @elseif($reportType == 'supplier' && !empty($purchaseDebts) && !empty($currentSupplier))
        <div class="card mt-2">
            <div class="card-body invoice" dir="rtl">
                <div class="card-title">
                    <div class="row">
                        <div class="col-3">
                            <h5>{{$currentSupplier['supplierName'] ?? ''}}</h5>
                        </div>

                        <div class="col-5">
                            <div class="row">
                                <div class="col-6">
                                    <h5>الرصيد الافتتاحي : {{number_format($currentSupplier['initialBalance'], 2)}}</h5>
                                </div>
                                <div class="col-6">
                                    <h5>افتتاحي المبيعات
                                        : {{number_format($currentSupplier['initialSalesBalance'], 2)}}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-4">
                            <h5>الرصيد : {{number_format($purchasesBalance - $salesBalance, 2)}}</h5>
                        </div>
                    </div>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice">
                        <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>البيان</th>
                            <th>عليه</th>
                            <th>له</th>
                            <th>الرصيد</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $currentBalance = 0;
                        @endphp
                        @if(!empty($merged))
                            @foreach($merged as $debt)
                                <tr>
                                    @php $currentBalance += $debt->debt - $debt->paid @endphp
                                    <td>{{$debt->due_date}}</td>
                                    <td @if($debt->sale_id != null || $debt->purchase_id != null) data-bs-toggle="modal"
                                        data-bs-target="#printModal"
                                        wire:click="getInvoice({{$debt}})" @endif>{{ $debt->note }}</td>
                                    <td>{{number_format($debt->debt, 2)}}</td>
                                    <td>{{number_format($debt->paid, 2)}}</td>
                                    <td>{{number_format($currentBalance, 2)}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body invoice" dir="rtl">
                <div class="card-title">
                    <div class="row">
                        <div class="col-3">
                            <h5>{{$currentSupplier['supplierName'] ?? ''}}</h5>
                        </div>

                        <div class="col-5">
                            <div class="row">
                                <div class="col-6">
                                    <h5>الرصيد الافتتاحي : {{number_format($currentSupplier['initialBalance'], 2)}}</h5>
                                </div>
                                <div class="col-6">
                                    <h5>افتتاحي المبيعات
                                        : {{number_format($currentSupplier['initialSalesBalance'], 2)}}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-4">
                            <h5>الرصيد : {{number_format($purchasesBalance - $salesBalance, 2)}}</h5>
                        </div>
                    </div>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice">
                        <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>البيان</th>
                            <th>عليه</th>
                            <th>له</th>
                            <th>الرصيد</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $currentBalance = 0;
                        @endphp
                        @if(!empty($merged))
                            @foreach($merged as $debt)
                                <tr>
                                    @php $currentBalance += $debt->debt - $debt->paid @endphp
                                    <td>{{$debt->due_date}}</td>
                                    <td colspan="3"
                                        @if($debt->sale_id != null || $debt->purchase_id != null) data-bs-toggle="modal"
                                        data-bs-target="#printModal"
                                        wire:click="getInvoice({{$debt}})" @endif>{{ $debt->note }}</td>
                                    <td>{{ $debt->type == "debt" ? number_format($debt->debt, 2) : number_format($debt->paid, 2)}}</td>
                                </tr>
                                @if($debt->type == 'debt' && $debt->sale_id != null)
                                    @foreach($debt->sale->saleDetails as $product)
                                        <tr>
                                            <td>{{ $debt->sale->sale_date }}</td>
                                            <td>{{ $product->product->productName }}</td>
                                            <td>{{ number_format($product->price,2) }}</td>
                                            <td>{{ number_format($product->quantity,2) }}</td>
                                            <td>{{ number_format($product->quantity * $product->price,2) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if($debt->type == 'debt' && $debt->purchase_id != null)
                                    @foreach($debt->purchase->purchaseDetails as $product)
                                        <tr>
                                            <td>{{ $debt->purchase->purchase_date }}</td>
                                            <td>{{ $product->product->productName }}</td>
                                            <td>{{ number_format($product->price,2) }}</td>
                                            <td>{{ number_format($product->quantity,2) }}</td>
                                            <td>{{ number_format($product->quantity * $product->price,2) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @elseif($reportType == 'employee' && !empty($saleDebts) && !empty($currentEmployee))
        <div class="card mt-2">
            <div class="card-body invoice" dir="rtl">
                <div class="card-title">
                    <div class="row">
                        <div class="col-4">
                            <h5>{{$currentEmployee['employeeName'] ?? ''}}</h5>
                        </div>
                        <div class="col-4">
                            <h5>الرصيد الافتتاحي : {{number_format($currentEmployee['initialBalance'] ?? 0, 2)}}</h5>
                        </div>
                        <div class="col-4">
                            <h5>الرصيد : {{number_format($salesBalance, 2)}}</h5>
                        </div>
                    </div>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice">
                        <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>البيان</th>
                            <th>عليه</th>
                            <th>له</th>
                            <th>الرصيد</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $currentBalance = 0;
                        @endphp
                        @if(!empty($merged))
                            @foreach($merged as $debt)
                                <tr>
                                    @php $currentBalance += $debt->debt - $debt->paid @endphp
                                    <td>{{$debt->due_date ?? $debt->gift_date}}</td>
                                    <td @if($debt->sale_id != null) data-bs-toggle="modal" data-bs-target="#printModal"
                                        wire:click="getInvoice({{$debt}})" @endif>{{ $debt->note != "" ? $debt->note : "تم دفع مبلغ" }}</td>
                                    @if(isset($debt->gift_amount))
                                        <td>0</td>
                                        <td>{{number_format($debt->gift_amount, 2)}}</td>
                                    @else
                                        <td>{{number_format($debt->debt, 2)}}</td>
                                        <td>{{number_format($debt->paid, 2)}}</td>
                                    @endif
                                    <td>{{number_format($currentBalance, 2)}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body invoice" dir="rtl">
                <div class="card-title">
                    <div class="row">
                        <div class="col-4">
                            <h5>{{$currentEmployee['employeeName'] ?? ''}}</h5>
                        </div>
                        <div class="col-4">
                            <h5>الرصيد الافتتاحي : {{number_format($currentEmployee['initialBalance'] ?? 0, 2)}}</h5>
                        </div>
                        <div class="col-4">
                            <h5>الرصيد : {{number_format($salesBalance, 2)}}</h5>
                        </div>
                    </div>
                </div>

                <div class="scroll">
                    <table class="text-center printInvoice">
                        <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>البيان</th>
                            <th>عليه</th>
                            <th>له</th>
                            <th>الرصيد</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $currentBalance = 0;
                        @endphp
                        @if(!empty($merged))
                            @foreach($merged as $debt)
                                <tr>
                                    @php $currentBalance += $debt->debt - $debt->paid @endphp
                                    <td>{{$debt->due_date ?? $debt->gift_date}}</td>
                                    <td colspan="3"
                                        @if($debt->sale_id != null || $debt->purchase_id != null) data-bs-toggle="modal"
                                        data-bs-target="#printModal"
                                        wire:click="getInvoice({{$debt}})" @endif>{{ $debt->note }}</td>
                                    <td>{{ $debt->type == "debt" ? number_format($debt->debt, 2) : number_format($debt->paid, 2)}}</td>
                                </tr>
                                @if($debt->type == 'debt' && $debt->sale_id != null)
                                    @foreach($debt->sale->saleDetails as $product)
                                        <tr>
                                            <td>{{ $debt->sale->sale_date }}</td>
                                            <td>{{ $product->product->productName }}</td>
                                            <td>{{ number_format($product->price,2) }}</td>
                                            <td>{{ number_format($product->quantity,2) }}</td>
                                            <td>{{ number_format($product->quantity * $product->price,2) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    @elseif($reportType == 'sales' && !empty($sales))
        <div class="card mt-2">
            <div class="card-body invoice" dir="rtl">
                <div class="card-title">
                    <div class="row">
                        <div class="col-3">
                            <h5>المبيعات</h5>
                        </div>
                        <div class="col-3">
                            <input type="text" wire:model.live="percent" placeholder="نسبة الربح"
                                   class="form-control text-center">
                        </div>
                    </div>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice">
                        <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>رقم الفاتوره</th>
                            <th>العميل</th>
                            <th>إسم المنتج</th>
                            <th>سعر البيع</th>
                            <th>الكميه</th>
                            <th>الجمله</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sales as $sale)
                            <tr>
                                <td>{{$sale->sale_date}}</td>
                                <td>{{$sale->sale_id}}</td>
                                @if(!empty($sale->sale->client))
                                    <td>{{$sale->sale->client->clientName}}</td>
                                @elseif(!empty($sale->sale->employee))
                                    <td>الموظف : {{$sale->sale->employee->employeeName}}</td>
                                @elseif(!empty($sale->sale->supplier))
                                    <td>المورد : {{$sale->sale->supplier->supplierName}}</td>
                                @endif

                                <td>{{ $sale->product->productName ?? "" }}</td>
                                <td>{{number_format($sale->price, 2)}}</td>
                                <td>{{number_format($sale->quantity, 2)}}</td>
                                <td>{{number_format($sale->quantity * $sale->price, 2)}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        @if(!empty($currentProduct))
                            <tr>
                                <td colspan="5">الجــــــــــــــــــــملة</td>
                                <td>{{number_format($quantity, 2)}}</td>
                                <td>{{number_format($sum, 2)}}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="6">الجــــــــــــــــــــملة</td>
                                <td>{{number_format($sum, 2)}}</td>
                            </tr>
                        @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @elseif($reportType == 'purchases' && !empty($purchases))
        <div class="card mt-2">
            <div class="card-body invoice" dir="rtl">
                <div class="card-title">
                    <div class="row">
                        <div class="col-3">
                            <h5>المشتريات</h5>
                        </div>
                    </div>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice">
                        <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>رقم الفاتوره</th>
                            <th>المورد</th>
                            <th>إسم المنتج</th>
                            <th>سعر البيع</th>
                            <th>الكميه</th>
                            <th>الجمله</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($purchases as $purchase)
                            <tr>
                                <td>{{$purchase->purchase_date}}</td>
                                <td>{{$purchase->purchase_id}}</td>
                                <td>{{$purchase->purchase->supplier->supplierName}}</td>
                                <td>{{ $purchase->product->productName ?? "" }}</td>
                                <td>{{number_format($purchase->price, 2)}}</td>
                                <td>{{number_format($purchase->quantity, 2)}}</td>
                                <td>{{number_format($purchase->quantity * $purchase->price, 2)}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        @if(!empty($currentProduct))
                            <tr>
                                <td colspan="5">الجــــــــــــــــــــملة</td>
                                <td>{{number_format($quantity, 2)}}</td>
                                <td>{{number_format($sum, 2)}}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="6">الجــــــــــــــــــــملة</td>
                                <td>{{number_format($sum, 2)}}</td>
                            </tr>
                        @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @elseif($reportType == 'tracking' && !empty($currentProduct))
        <div class="card mt-2">
            <div class="card-body invoice">
                <div class="card-title" dir="rtl">
                    <div class="row">
                        <div class="col-4">
                            <h3>{{ $currentProduct['productName'] }}</h3>
                        </div>
                        <div class="col-4">
                            <h3>الكمية الافتتاحيه : {{number_format($currentProduct['initialStock'], 2)}}</h3>
                        </div>
                        <div class="col-4">
                            <h3>الكمية الحالية : {{number_format($currentProduct['stock'], 2)}}</h3>

                        </div>
                    </div>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice">
                        <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>البيان</th>
                            <th>الوارد</th>
                            <th>الصادر</th>
                            <th>الرصيد</th>
                        </tr>
                        </thead>
                        <tbody>

                        @php $currentStock = $currentProduct['initialStock']; @endphp
                        @foreach($array as $item)
                            <tr>
                                @php $currentStock += $item['purchase'] - $item['sale'] @endphp
                                <td>{{ $item['date'] }}</td>
                                <td data-bs-toggle="modal" data-bs-target="#printModal"
                                    wire:click="getInvoice({{$item['invoice']}})"
                                    style="cursor:pointer;" >{{ $item['note'] }}</td>
                                <td>{{ number_format($item['purchase'], 2) }}</td>
                                <td>{{ number_format($item['sale'], 2) }}</td>
                                <td>{{ number_format($currentStock, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="2">الجمـــــــــــــــلة</th>
                            <th>{{ number_format($purchase, 2) }}</th>
                            <th>{{ number_format($sale, 2) }}</th>
                            <th>{{ number_format($currentStock, 2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @elseif($reportType == 'daily' && !empty($array))
        <div class="card mt-2">
            <div class="card-body invoice">
                <div class="card-title" dir="rtl">
                    <div class="row">
                        <div class="col-4">
                             <h3>الخزنة : {{number_format($safeBalance, 2)}}</h3>
                        </div>
                        <div class="col-4">
                            <h3>البنك : {{number_format($bankBalance, 2)}}</h3>
                        </div>
                        <div class="col-4">
                            <h3>الجمله : {{number_format($safeBalance + $bankBalance, 2)}}</h3>

                        </div>
                    </div>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice">
                        <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>إسم الحساب</th>
                            <th>الجهة</th>
                            <th>البيان</th>
                            <th>الوارد</th>
                            <th>الصادر</th>
                            <th>آجل مبيعات</th>
                            <th>آجل مشتريات</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($array as $item)
                            <tr>
                                <td>{{ $item['date'] }}</td>
                                <td>{{ $item['account'] }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td @if(isset($item['invoice'])) data-bs-toggle="modal" data-bs-target="#printModal"
                                    wire:click="getInvoice({{$item['invoice']}})"
                                    style="cursor:pointer;" @endif >{{ $item['note'] }}</td>
                                <td>{{ number_format($item['paid'], 2) }}</td>
                                <td>{{ number_format($item['debt'], 2) }}</td>
                                <td>{{ number_format($item['saleFuture'], 2) }}</td>
                                <td>{{ number_format($item['purchaseFuture'], 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="4">الجمـــــــــــــــلة</th>
                            <th>{{ number_format($paid, 2) }}</th>
                            <th>{{ number_format($debt, 2) }}</th>
                            <th>{{ number_format($saleFuture, 2) }}</th>
                            <th>{{ number_format($purchaseFuture, 2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @elseif($reportType == 'safe' && !empty($array))
        <div class="card mt-2">
            <div class="card-body invoice">
                <div class="card-title" dir="rtl">
                    <div class="row">
                        <div class="col-4">
                            <h3>الخزنة : {{number_format($safeBalance, 2)}}</h3>
                        </div>
                        <div class="col-4">
                            <h3>البنك : {{number_format($bankBalance, 2)}}</h3>
                        </div>
                        <div class="col-4">
                            <h3>الجمله : {{number_format($safeBalance + $bankBalance, 2)}}</h3>

                        </div>
                    </div>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice">
                        <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>إسم الحساب</th>
                            <th>الجهة</th>
                            <th>البيان</th>
                            <th>نوع المعامله</th>
                            <th>الوارد</th>
                            <th>الصادر</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($array as $item)
                            @if($item['saleFuture'] == 0 && $item['purchaseFuture'] == 0)
                                <tr>
                                    <td>{{ $item['date'] }}</td>
                                    <td>{{ $item['account'] }}</td>
                                    <td>{{ $item['name'] }}</td>
                                    <td @if(isset($item['invoice'])) data-bs-toggle="modal" data-bs-target="#printModal"
                                        wire:click="getInvoice({{$item['invoice']}})"
                                        style="cursor:pointer;" @endif >{{ $item['note'] }}</td>
                                    <td>{{ $item['payment'] == "cash" ? "كاش" : ($item['payment'] == "bank" ? "بنك" : "") }}</td>
                                    <td>{{ number_format($item['paid'], 2) }}</td>
                                    <td>{{ number_format($item['debt'], 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="5">الجمـــــــــــــــلة</th>
                            <th>{{ number_format($paid, 2) }}</th>
                            <th>{{ number_format($debt, 2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

