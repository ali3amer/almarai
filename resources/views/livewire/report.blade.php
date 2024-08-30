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
                    <select class="form-select" wire:change="clearArray()" wire:model.live="reportType" id="reportType">
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

                    @if($reportType == "daily" || $reportType == "safe")
                        <label for="payment">وسيلة الدفع</label>
                        <select class="form-select" wire:model.live="payment"
                                id="payment">
                            <option value="">--------------------</option>
                            <option value="cash">كاش</option>
                            <option value="bank">بنك</option>
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
                        <td>{{number_format($totalProductsStock, 2)}}</td>
                        <td>الدائنون (الموردين)</td>
                        <td>{{number_format($totalSuppliersBalance, 2)}}</td>
                    </tr>
                    <tr>
                        <td>رصيد البنك</td>
                        <td>{{number_format($totalBanksBalance, 2)}}</td>
                        <td>أمانات طرفنا</td>
                        <td>{{ number_format($totalDepositsBalance, 2) }}</td>
                    </tr>
                    <tr>
                        <td>النقديه بالخزنه</td>
                        <td>{{number_format($totalSafeBalance, 2)}}</td>
                        <td rowspan="3">رأس المال</td>
                        <td rowspan="3">{{number_format($capital, 2)}}</td>
                    </tr>
                    <tr>
                        <td>المدينون</td>
                        <td>{{number_format($totalClientsBalance, 2)}}</td>
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
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>مدفوعات المبيعات</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>المشتريات</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>مدفوعات المشتريات</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>المصروفات</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>مصروفات الموظفين</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>التالف</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <th>الخزنة</th>
                        <th>0</th>
                    </tr>
                    <tr>
                        <td>ديون مبيعات</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>ديون مشتريات</td>
                        <td>0</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>الجمله</th>
                        <th>0</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card mt-2">
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
                            @php $total = 0; @endphp
                            @foreach($clients as $client)
                                @php $total += $client->currentBalance; @endphp
                                <tr>
                                    <td>{{ $client->clientName }}</td>
                                    <td>{{ number_format($client->currentBalance , 2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>الجمله</th>
                            <th>{{ number_format($total , 2) }}</th>
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
                    <table class="text-center printInvoice" dir="rtl">
                        <thead>
                        <tr>
                            <th>إسم المورد</th>
                            <th>الرصيد</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($suppliers))
                            @php $total = 0; @endphp
                            @foreach($suppliers as $supplier)
                                @php $total += $supplier->currentBalance; @endphp
                                <tr>
                                    <td>{{ $supplier->supplierName }}</td>
                                    <td>{{ number_format($supplier->currentBalance , 2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>الجمله</th>
                            <th>{{ number_format($total , 2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body invoice">
                <div class="card-title" dir="rtl">
                    <h2>الأمانات طرفنا</h2>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice" dir="rtl">
                        <thead>
                        <tr>
                            <th>إسم الشخص</th>
                            <th>الرصيد</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($deposits))
                            @php $total = 0; @endphp
                            @foreach($deposits as $deposit)
                                @php $total += $deposit->currentBalance; @endphp
                                <tr>
                                    <td>{{ $deposit->name }}</td>
                                    <td>{{ number_format($deposit->currentBalance , 2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>الجمله</th>
                            <th>{{ number_format($total , 2) }}</th>
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
                                $currentBalance = $currentClient['initialBalance'];
                                $incomes = 0;
                                $expenses = $currentClient['initialBalance'];
                            @endphp
                            <tr>
                                <td></td>
                                <td>الرصيد السابق</td>
                                <td>{{ number_format($currentClient['initialBalance'], 2) }}</td>
                                <td>0</td>
                                <td>{{ number_format($currentBalance, 2) }}</td>
                            </tr>
                            @foreach($saleDebts as $debt)
                                @php
                                    $incomes += floatval($debt['income']) + floatval($debt['futureExpense']);
                                    $expenses += floatval($debt['expense']) + floatval($debt['futureIncome']);
                                    $currentBalance += floatval($debt['expense']) + floatval($debt['futureIncome']) - floatval($debt['income']) - floatval($debt['futureExpense']);
                                @endphp
                                <tr>
                                    <td>{{$debt['due_date']}}</td>
                                    <td @if($debt['invoice_id'] != null) data-bs-toggle="modal"
                                        data-bs-target="#printModal"
                                        wire:click="getInvoice({{$debt['invoice_id']}}, '{{$debt['tableName']}}')"
                                        @endif>{{ $debt['note'] }}</td>
                                    <td>{{ floatval($debt['expense']) != 0 ? floatval($debt['expense']) : floatval($debt['futureIncome']) }}</td>
                                    <td>{{ floatval($debt['income']) != 0 ? floatval($debt['income']) : floatval($debt['futureExpense']) }}</td>
                                    <td>{{number_format($currentBalance, 2)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="2">الجــــــــــــــــمله</th>
                                <th>{{ number_format($expenses, 2) }}</th>
                                <th>{{ number_format($incomes, 2) }}</th>
                                <th>{{ number_format($currentBalance, 2) }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-body invoice" dir="rtl">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-4"><h5>مبيعات
                                    : {{$currentClient['clientName'] ?? ''}} بالتفصيل</h5>
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
                            @foreach($saleDebts as $debt)
                                <tr>
                                    <td>{{$debt['due_date']}}</td>
                                    <td colspan="3">{{ $debt['note'] }}</td>
                                    <td>{{ $debt['income'] != 0 ? number_format($debt['income'], 2) : ($debt['expense'] != 0 ? number_format($debt['expense'], 2) : ($debt['futureExpense'] != 0 ? number_format($debt['futureExpense'], 2) : number_format($debt['futureIncome'], 2))) }}</td>
                                </tr>
                                @if($debt['invoice_id'] != null)
                                    @foreach(\App\Models\Sale::find($debt['invoice_id'])->saleDetails as $product)
                                        <tr>
                                            <td>{{ $debt['due_date'] }}</td>
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

    @elseif($reportType == 'supplier' && !empty($currentSupplier))

        @if(!empty($purchaseDebts))
            <div class="card mt-2">
                <div class="card-body invoice" dir="rtl">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-4">
                                <h5>المشتريات : {{ number_format($purchasesBalance, 2) }}</h5>
                            </div>
                            <div class="col-4">
                                <h5>المبيعات : {{ number_format($salesBalance, 2) }}</h5>
                            </div>
                            <div class="col-4">
                                <h5>الجمله
                                    : {{ number_format($purchasesBalance - $salesBalance, 2) }}</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-4"><h5>مشتريات
                                    : {{$currentSupplier['supplierName'] ?? ''}}</h5>
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
                                $currentBalance = $currentSupplier['initialBalance'];
                                $incomes = 0;
                                $expenses = $currentSupplier['initialBalance'];
                            @endphp
                            <tr>
                                <td></td>
                                <td>الرصيد السابق</td>
                                <td>{{ number_format($currentSupplier['initialBalance'], 2) }}</td>
                                <td>0</td>
                                <td>{{ number_format($currentBalance, 2) }}</td>
                            </tr>
                            @foreach($purchaseDebts as $debt)
                                <tr>
                                    <td>{{$debt['due_date']}}</td>
                                    <td @if($debt['invoice_id'] != null) data-bs-toggle="modal"
                                        data-bs-target="#printModal"
                                        wire:click="getInvoice({{$debt['invoice_id']}}, '{{$debt['tableName']}}')"
                                        @endif>{{ $debt['note'] }}</td>
                                    <td>{{ floatval($debt['expense']) != 0 ? floatval($debt['expense']) : floatval($debt['futureIncome']) }}</td>
                                    <td>{{ floatval($debt['income']) != 0 ? floatval($debt['income']) : floatval($debt['futureExpense']) }}</td>
                                    <td>{{number_format($currentBalance, 2)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="2">الجــــــــــــــــمله</th>
                                <th>{{ number_format($expenses, 2) }}</th>
                                <th>{{ number_format($incomes, 2) }}</th>
                                <th>{{ number_format($currentBalance, 2) }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-body invoice" dir="rtl">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-4"><h5>مشتريات
                                    : {{$currentSupplier['supplierName'] ?? ''}} بالتفصيل</h5>
                            </div>

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
                            @foreach($purchaseDebts as $debt)
                                <tr>
                                    <td>{{$debt['due_date']}}</td>
                                    <td colspan="3">{{ $debt['note'] }}</td>
                                    <td>{{ $debt['income'] != 0 ? number_format($debt['income'], 2) : ($debt['expense'] != 0 ? number_format($debt['expense'], 2) : ($debt['futureExpense'] != 0 ? number_format($debt['futureExpense'], 2) : number_format($debt['futureIncome'], 2))) }}</td>
                                </tr>
                                @if($debt['invoice_id'] != null)
                                    @foreach(\App\Models\Purchase::find($debt['invoice_id'])->purchaseDetails as $product)
                                        <tr>
                                            <td>{{ $debt['due_date'] }}</td>
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

        @if(!empty($saleDebts))
            <div class="card mt-2">
                <div class="card-body invoice" dir="rtl">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-4"><h5>مبيعات
                                    : {{$currentSupplier['supplierName'] ?? ''}}</h5>
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
                                $currentBalance = $currentSupplier['initialSalesBalance'];
                                $incomes = 0;
                                $expenses = $currentSupplier['initialSalesBalance'];
                            @endphp
                            <tr>
                                <td></td>
                                <td>الرصيد السابق</td>
                                <td>{{ number_format($currentSupplier['initialBalance'], 2) }}</td>
                                <td>0</td>
                                <td>{{ number_format($currentBalance, 2) }}</td>
                            </tr>
                            @foreach($saleDebts as $debt)
                                @php
                                    $incomes += floatval($debt['income']) + floatval($debt['futureExpense']);
                                    $expenses += floatval($debt['expense']) + floatval($debt['futureIncome']);
                                    $currentBalance += floatval($debt['expense']) + floatval($debt['futureIncome']) - floatval($debt['income']) - floatval($debt['futureExpense']);
                                @endphp
                                <tr>
                                    <td>{{$debt['due_date']}}</td>
                                    <td @if($debt['invoice_id'] != null) data-bs-toggle="modal"
                                        data-bs-target="#printModal"
                                        wire:click="getInvoice({{$debt['invoice_id']}}, '{{$debt['tableName']}}')"
                                        @endif>{{ $debt['note'] }}</td>
                                    <td>{{ floatval($debt['expense']) != 0 ? floatval($debt['expense']) : floatval($debt['futureIncome']) }}</td>
                                    <td>{{ floatval($debt['income']) != 0 ? floatval($debt['income']) : floatval($debt['futureExpense']) }}</td>
                                    <td>{{number_format($currentBalance, 2)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="2">الجــــــــــــــــمله</th>
                                <th>{{ number_format($expenses, 2) }}</th>
                                <th>{{ number_format($incomes, 2) }}</th>
                                <th>{{ number_format($currentBalance, 2) }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-body invoice" dir="rtl">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-4"><h5>مبيعات
                                    : {{$currentClient['clientName'] ?? ''}} بالتفصيل</h5>
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
                            @foreach($saleDebts as $debt)
                                <tr>
                                    <td>{{$debt['due_date']}}</td>
                                    <td colspan="3">{{ $debt['note'] }}</td>
                                    <td>{{ $debt['income'] != 0 ? number_format($debt['income'], 2) : ($debt['expense'] != 0 ? number_format($debt['expense'], 2) : ($debt['futureExpense'] != 0 ? number_format($debt['futureExpense'], 2) : number_format($debt['futureIncome'], 2))) }}</td>
                                </tr>
                                @if($debt['invoice_id'] != null)
                                    @foreach(\App\Models\Sale::find($debt['invoice_id'])->saleDetails as $product)
                                        <tr>
                                            <td>{{ $debt['due_date'] }}</td>
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

    @elseif($reportType == 'employee' && !empty($currentEmployee))

        @if(!empty($saleDebts))
            <div class="card mt-2">
                <div class="card-body invoice" dir="rtl">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-4"><h5>مبيعات
                                    : {{$currentEmployee['employeeName'] ?? ''}}</h5>
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
                                $currentBalance = $currentEmployee['initialBalance'];
                                $incomes = 0;
                                $expenses = $currentEmployee['initialBalance'];
                            @endphp
                            <tr>
                                <td></td>
                                <td>الرصيد السابق</td>
                                <td>{{ number_format($currentEmployee['initialBalance'], 2) }}</td>
                                <td>0</td>
                                <td>{{ number_format($currentBalance, 2) }}</td>
                            </tr>
                            @foreach($saleDebts as $debt)
                                @php
                                    $incomes += floatval($debt['income']) + floatval($debt['futureExpense']);
                                    $expenses += floatval($debt['expense']) + floatval($debt['futureIncome']);
                                    $currentBalance += floatval($debt['expense']) + floatval($debt['futureIncome']) - floatval($debt['income']) - floatval($debt['futureExpense']);
                                @endphp
                                <tr>
                                    <td>{{$debt['due_date']}}</td>
                                    <td @if($debt['invoice_id'] != null) data-bs-toggle="modal"
                                        data-bs-target="#printModal"
                                        wire:click="getInvoice({{$debt['invoice_id']}}, '{{$debt['tableName']}}')"
                                        @endif>{{ $debt['note'] }}</td>
                                    <td>{{ floatval($debt['expense']) != 0 ? floatval($debt['expense']) : floatval($debt['futureIncome']) }}</td>
                                    <td>{{ floatval($debt['income']) != 0 ? floatval($debt['income']) : floatval($debt['futureExpense']) }}</td>
                                    <td>{{number_format($currentBalance, 2)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="2">الجــــــــــــــــمله</th>
                                <th>{{ number_format($expenses, 2) }}</th>
                                <th>{{ number_format($incomes, 2) }}</th>
                                <th>{{ number_format($currentBalance, 2) }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-2">
                <div class="card-body invoice" dir="rtl">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-4"><h5>مبيعات
                                    : {{$currentClient['clientName'] ?? ''}} بالتفصيل</h5>
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
                            @foreach($saleDebts as $debt)
                                <tr>
                                    <td>{{$debt['due_date']}}</td>
                                    <td colspan="3">{{ $debt['note'] }}</td>
                                    <td>{{ $debt['income'] != 0 ? number_format($debt['income'], 2) : ($debt['expense'] != 0 ? number_format($debt['expense'], 2) : ($debt['futureExpense'] != 0 ? number_format($debt['futureExpense'], 2) : number_format($debt['futureIncome'], 2))) }}</td>
                                </tr>
                                @if($debt['invoice_id'] != null)
                                    @foreach(\App\Models\Sale::find($debt['invoice_id'])->saleDetails as $product)
                                        <tr>
                                            <td>{{ $debt['due_date'] }}</td>
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

        @if(!empty($employeeGifts))
            <div class="card mt-2">
                <div class="card-body invoice" dir="rtl">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-4"><h5>مدفوعات الى
                                    : {{$currentEmployee['employeeName'] ?? ''}}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="scroll">
                        <table class="text-center printInvoice">
                            <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>البيان</th>
                                <th>المبلغ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $total = 0;
                            @endphp

                            @foreach($employeeGifts as $gift)
                                <tr>
                                    @php
                                        $total += $gift->amount;
                                    @endphp
                                    <td>{{$gift->due_date}}</td>
                                    <td>{{$gift->note}}</td>
                                    <td>{{number_format($gift->amount, 2)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="2">الجــــــــــــــــمله</th>
                                <th>{{ number_format($total, 2) }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    @elseif($reportType == 'sales' && !empty($sales))
        <div class="card mt-2">
            <div class="card-body invoice" dir="rtl">
                <div class="card-title">
                    <div class="row">
                        <div class="col-3">
                            <h5>المبيعات</h5>
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
                            <tr data-bs-toggle="modal" data-bs-target="#printModal"
                                wire:click="getInvoice({{$sale['sale_id']}}, 'sales')">
                                <td>{{$sale['due_date']}}</td>
                                <td>{{$sale['sale_id']}}</td>
                                <td>{{$sale['ownerName']}}</td>

                                <td>{{ $sale['productName'] }}</td>
                                <td>{{number_format($sale['price'], 2)}}</td>
                                <td>{{number_format($sale['quantity'], 2)}}</td>
                                <td>{{number_format($sale['quantity'] * $sale['price'], 2)}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        @if(!empty($currentProduct))
                            <tr>
                                <th colspan="5">الجــــــــــــــــــــملة</th>
                                <th>{{number_format($quantity, 2)}}</th>
                                <th>{{number_format($sum, 2)}}</th>
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
                            <tr data-bs-toggle="modal" data-bs-target="#printModal"
                                wire:click="getInvoice({{$purchase['purchase_id']}}, 'purchases')">
                                <td>{{$purchase['due_date']}}</td>
                                <td>{{$purchase['purchase_id']}}</td>
                                <td>{{$purchase['ownerName']}}</td>
                                <td>{{ $purchase['productName'] }}</td>
                                <td>{{number_format($purchase['price'], 2)}}</td>
                                <td>{{number_format($purchase['quantity'], 2)}}</td>
                                <td>{{number_format($purchase['quantity'] * $purchase['price'], 2)}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        @if(!empty($currentProduct))
                            <tr>
                                <th colspan="5">الجــــــــــــــــــــملة</th>
                                <th>{{number_format($quantity, 2)}}</th>
                                <th>{{number_format($sum, 2)}}</th>
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

        @if(!empty($trackingProducts))
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

                            @php
                                $currentStock = $currentProduct['initialStock'];
                                $income = $currentProduct['initialStock'];
                                $expense = 0;
                            @endphp
                            @if($currentProduct['initialStock'] != 0)
                                <tr>
                                    <td></td>
                                    <td style="cursor:pointer;">الكمية السابقه</td>
                                    <td>{{ number_format($currentProduct['initialStock'], 2) }}</td>
                                    <td>0</td>
                                    <td>{{ number_format($currentProduct['initialStock'], 2) }}</td>
                                </tr>
                            @endif
                            @foreach($trackingProducts as $item)
                                <tr data-bs-toggle="modal" data-bs-target="#printModal"
                                    wire:click="getInvoice({{$item['invoice_id']}}, '{{$item['tableName']}}')">
                                    @php
                                        $currentStock += floatval($item['income']) - floatval($item['expense']);
                                        $income += floatval($item['income']);
                                        $expense += floatval($item['expense']);
                                    @endphp
                                    <td>{{ $item['due_date'] }}</td>
                                    <td>{{ $item['note'] }}</td>
                                    <td>{{ number_format($item['income'], 2) }}</td>
                                    <td>{{ number_format($item['expense'], 2) }}</td>
                                    <td>{{ number_format($currentStock, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="2">الجمـــــــــــــــلة</th>
                                <th>{{ number_format($income, 2) }}</th>
                                <th>{{ number_format($expense, 2) }}</th>
                                <th>{{ number_format($currentStock, 2) }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @elseif($reportType == 'expenses' && !empty($expenses))

        <div class="card mt-2">
            <div class="card-body invoice">
                <h4>البنود</h4>
                <div class="scroll">
                    <table class="text-center printInvoice">
                        <thead>
                        <tr>
                            <th>البند</th>
                            <th>المبلغ</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sumOptions = 0 @endphp
                        @foreach($expensesByOptions as $option)
                            <tr>
                                <td>{{ $option['option_name']}}</td>
                                <td>{{ number_format($option['total_amount'], 2) }}</td>
                            </tr>
                            @php $sumOptions += $option['total_amount'] @endphp
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>الجمـــــــــــــــلة</th>
                            <th>{{ number_format($sumOptions, 2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body invoice">
                <div class="card-title" dir="rtl">
                    <h4>المصروفات</h4>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice">
                        <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>البند</th>
                            <th>البيان</th>
                            <th>المبلغ</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($expenses as $expense)
                            <tr>
                                <td>{{ $expense->due_date }}</td>
                                <td>{{ $expense->option_id != null ? $expense->option->optionName : "" }}</td>
                                <td>{{ $expense->description }}</td>
                                <td>{{ number_format($expense->amount, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="3">الجمـــــــــــــــلة</th>
                            <th>{{ !empty($expenses) ? number_format($expenses->sum("amount"), 2) : 0 }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @elseif($reportType == 'daily' && !empty($statements))
        <div class="card mt-2">
            <div class="card-body invoice">
                <div class="card-title" dir="rtl">
                    <div class="row">
                        @if($payment == "" || $payment == "cash")
                            <div class="col-4">
                                <h3>الخزنة : {{number_format($safeBalance, 2)}}</h3>
                            </div>
                        @endif

                        @if($payment == "" || $payment == "bank")
                            <div class="col-4">
                                <h3>البنك : {{number_format($bankBalance, 2)}}</h3>
                            </div>
                        @endif

                        @if($payment == "")
                            <div class="col-4">
                                <h3>الجمله : {{number_format($safeBalance + $bankBalance, 2)}}</h3>
                            </div>
                        @endif
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
                            <th>آجل وارد</th>
                            <th>آجل صادر</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $incomes = $expenses = $futureIncomes = $futureExpenses = 0 @endphp
                        @foreach($statements as $statement)
                            @php
                                $incomes += floatval($statement['income']);
                                $expenses += floatval($statement['expense']);
                                $futureIncomes += floatval($statement['futureIncome']);
                                $futureExpenses += floatval($statement['futureExpense']);
                            @endphp

                            <tr>
                                <td>{{ $statement['due_date'] }}</td>
                                <td>{{ $accounts[$statement['tableName']] }}</td>
                                <td>{{ $statement['ownerName'] }}</td>
                                <td @if($statement['invoice_id'] != null) data-bs-toggle="modal"
                                    data-bs-target="#printModal"
                                    wire:click="getInvoice({{$statement['invoice_id']}}, '{{$statement['tableName']}}')"
                                    style="cursor:pointer;" @endif >{{ $statement['note'] }}</td>
                                <td>{{ number_format($statement['income'], 2) }}</td>
                                <td>{{ number_format($statement['expense'], 2) }}</td>
                                <td>{{ number_format($statement['futureIncome'], 2) }}</td>
                                <td>{{ number_format($statement['futureExpense'], 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="4">الجمـــــــــــــــلة</th>
                            <th>{{ number_format($incomes, 2) }}</th>
                            <th>{{ number_format($expenses, 2) }}</th>
                            <th>{{ number_format($futureIncomes, 2) }}</th>
                            <th>{{ number_format($futureExpenses, 2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @elseif($reportType == 'safe' && !empty($statements))
        <div class="card mt-2">
            <div class="card-body invoice">
                <div class="card-title" dir="rtl">
                    <div class="row">
                        @if($payment == "" || $payment == "cash")
                            <div class="col-4">
                                <h3>الخزنة : {{number_format($safeBalance, 2)}}</h3>
                            </div>
                        @endif

                        @if($payment == "" || $payment == "bank")
                            <div class="col-4">
                                <h3>البنك : {{number_format($bankBalance, 2)}}</h3>
                            </div>
                        @endif

                        @if($payment == "")
                            <div class="col-4">
                                <h3>الجمله : {{number_format($safeBalance + $bankBalance, 2)}}</h3>
                            </div>
                        @endif
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
                        </tr>
                        </thead>
                        <tbody>
                        @php $incomes = $expenses = 0 @endphp
                        @foreach($statements as $statement)
                            @php
                                $incomes += floatval($statement['income']);
                                $expenses += floatval($statement['expense']);
                            @endphp
                            @if(floatval($statement['futureExpense']) == 0 && floatval($statement['futureIncome']) == 0)
                                <tr>
                                    <td>{{ $statement['due_date'] }}</td>
                                    <td>{{ $accounts[$statement['tableName']] }}</td>
                                    <td>{{ $statement['ownerName'] }}</td>
                                    <td @if($statement['invoice_id'] != null) data-bs-toggle="modal"
                                        data-bs-target="#printModal"
                                        wire:click="getInvoice({{$statement['invoice_id']}}, '{{$statement['tableName']}}')"
                                        style="cursor:pointer;" @endif >{{ $statement['note'] }}</td>
                                    <td>{{ number_format($statement['income'], 2) }}</td>
                                    <td>{{ number_format($statement['expense'], 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="4">الجمـــــــــــــــلة</th>
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

