<div>

    <div wire:loading class="position-fixed top-0 opacity-25 bg-dark z-3" style="width: 100%; height: 100%;">
        <div class="d-flex justify-content-center" style="height: 100%">
            <i class="spinner-border text-primary m-auto"></i>
        </div>
    </div>
    <!-- Choose Client Modal -->
    <div wire:ignore.self class="modal fade" id="peopleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                        @if($reportType == "client")
                            العملاء
                        @elseif($reportType == "supplier")
                            الموردين
                        @elseif($reportType == "employee")
                            الموظفين
                        @else
                            العهد والأمانات
                        @endif
                    </h1>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <div class="row">
                                    <div class="col-4 align-self-center"><h5>
                                            @if($reportType == "client")
                                                العملاء
                                            @elseif($reportType == "supplier")
                                                الموردين
                                            @elseif($reportType == "employee")
                                                الموظفين
                                            @else
                                                العهد والأمانات
                                            @endif
                                        </h5></div>
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
                                            <tr style="cursor: pointer" wire:click="choosePeople({{$client}})"
                                                data-bs-dismiss="modal"
                                                aria-label="Close">
                                                <td scope="row">{{$loop->index + 1}}</td>
                                                <td>{{$client->name}}</td>
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

    <x-title :$title :$show/>
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

                    @if($reportType == "daily" || $reportType == "safe" || $reportType == "bank")
                        <label for="payment">وسيلة الدفع</label>
                        <select class="form-select" @disabled($reportType == "bank") wire:model.live="payment"
                                id="payment">
                            <option value="">--------------------</option>
                            <option value="cash">كاش</option>
                            <option value="bank">بنك</option>
                        </select>
                    @endif

                    @if($reportType == "safe" || $reportType == "bank")
                        <label for="payment">صادر أم وارد</label>
                        <select class="form-select" wire:model.live="showType"
                                id="payment">
                            <option>الكل</option>
                            <option value="expense">صادر</option>
                            <option value="income">وارد</option>
                        </select>
                    @endif

                    @if($reportType == 'client' || $reportType == 'supplier' || $reportType == 'deposit' || $reportType == 'employee')
                        <label for="client">الإسم</label>
                        <input data-bs-toggle="modal" wire:model="currentPeople.name" readonly
                               placeholder="الإسم ...." class="form-control" data-bs-target="#peopleModal">
                    @endif
                    @if($reportType =='inventory' || $reportType =='sales'|| $reportType =='percent' || $reportType =='purchases' || $reportType =='tracking')
                        <label for="store_id">المخزن</label>
                        <select class="form-select mt-2" wire:model.live="store_id" id="store_id">
                            <option value="0">-----------------</option>
                            @foreach($stores as $store)
                                <option value="{{$store->id}}">{{$store->storeName}}</option>
                            @endforeach
                        </select>

                        @if($store_id != 0 && ($reportType =='sales' || $reportType =='percent' || $reportType =='purchases' || $reportType =='tracking'))
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
                    <button wire:loading.attr="disabled"
                            @disabled(($reportType == "employee" || $reportType == "supplier" || $reportType == "deposit" || $reportType == "client") && empty($currentPeople)) @disabled($reportType == "tracking" && empty($currentProduct)) @disabled($reportDuration == 'day' && $day == '') @disabled($reportDuration == 'duration' && $from == '') @disabled($reportDuration == 'duration' && $to == '') class="btn btn-primary w-100 mt-2"
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

        <div class="card mt-2 visually-hidden">
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
                        @if(!empty($people))
                            @foreach($people as $client)
                                @if($client->type == "client")
                                    <tr>
                                        <td>{{ $client->name }}</td>
                                        <td>{{ number_format($client->salesBalance , 2) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif

                        <tr>
                            <th colspan="2" class="text-center">مبيعات الموردين</th>
                        </tr>
                        @if(!empty($people))
                            @foreach($people as $supplier)
                                @if($supplier->salesBalance != 0 && $supplier->type == "supplier")
                                    <tr>
                                        <td>{{ $supplier->name }}</td>
                                        <td>{{ number_format($supplier->salesBalance , 2) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif

                        <tr>
                            <th colspan="2" class="text-center">مبيعات الموظفين</th>
                        </tr>
                        @if(!empty($people))
                            @foreach($people as $employee)
                                @if($employee->salesBalance != 0 && $employee->type == "employee")
                                    <tr>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ number_format($employee->salesBalance , 2) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>الجمله</th>
                            <th>{{ number_format($totalClientsBalance , 2) }}</th>
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
                        <tr>
                            <th colspan="2" class="text-center">مشتريات الموردين</th>
                        </tr>
                        @if(!empty($people))
                            @foreach($people as $supplier)
                                @if($supplier->type == "supplier")
                                    <tr>
                                        <td>{{ $supplier->name }}</td>
                                        <td>{{ number_format($supplier->purchasesBalance , 2) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif

                        <tr>
                            <th colspan="2" class="text-center">مشتريات العملاء</th>
                        </tr>

                        @if(!empty($people))
                            @foreach($people as $client)
                                @if($client->purchasesBalance != 0 && $client->type == "client")
                                    <tr>
                                        <td>{{ $client->name }}</td>
                                        <td>{{ number_format($client->purchasesBalance , 2) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif

                        <tr>
                            <th colspan="2" class="text-center">مشتريات الموظفيين</th>
                        </tr>

                        @if(!empty($people))
                            @foreach($people as $employee)
                                @if($employee->purchasesBalance != 0 && $employee->type == "employee")
                                    <tr>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ number_format($employee->purchasesBalance , 2) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif

                        </tbody>
                        <tfoot>
                        <tr>
                            <th>الجمله</th>
                            <th>{{ number_format($totalSuppliersBalance , 2) }}</th>
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
                        @if(!empty($people))
                            @foreach($people as $deposit)
                                @if($deposit->type == "deposit")
                                    <tr>
                                        <td>{{ $deposit->name }}</td>
                                        <td>{{ number_format($deposit->depositsBalance , 2) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>الجمله</th>
                            <th>{{ number_format($totalDepositsBalance , 2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    @elseif($reportType == "safeDays" && !empty($days))
        <div class="card mt-2">
            <div class="card-body invoice">
                <div class="card-title" dir="rtl">
                    <h2>ملخص الخزنه</h2>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice" dir="rtl">
                        <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>الوارد</th>
                            <th>الصادر</th>
                            <th>رصيد اليوم</th>
                            <th>الرصيد</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>الرصيد الافتتاحي</td>
                            <td>{{ number_format($initialSafeBalance, 2) }}</td>
                            <td>0</td>
                            <td>{{ number_format($initialSafeBalance, 2) }}</td>
                            <td>{{ number_format($initialSafeBalance, 2) }}</td>
                        </tr>
                        @php $credit = $initialSafeBalance; @endphp
                        @php $withdraw = 0; @endphp
                        @php $debit = 0; @endphp
                        @php $balance = 0; @endphp
                        @foreach($days as $day)
                            @if($day['credit'] != 0 || $day['debit'] != 0)
                                <tr>
                                    @php $credit += $day['credit']; @endphp
                                    @php $debit += $day['debit']; @endphp
                                    @php $balance = $credit - $debit; @endphp
                                    <td>{{ $day['due_date'] }}</td>
                                    <td>{{ number_format($day['credit'] , 2) }}</td>
                                    <td>{{ number_format($day['debit'] , 2) }}</td>
                                    <td>{{ number_format($day['credit'] - $day['debit'] , 2) }}</td>
                                    <td>{{ number_format($balance, 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>الجمله</th>
                            <th>{{ number_format($credit , 2) }}</th>
                            <th>{{ number_format($debit , 2) }}</th>
                            <th></th>
                            <th>{{ number_format($balance , 2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body invoice">
                <div class="card-title" dir="rtl">
                    <h2>الصافي</h2>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice" dir="rtl">
                        <thead>
                        <tr>
                            <th>البيان</th>
                            <th>المبلغ</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>الوارد</td>
                            <td>{{ number_format($credit, 2) }}</td>
                        </tr>
                        <tr>
                            <td>الصادر</td>
                            <td>{{ number_format($debit, 2) }}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>الجمله</th>
                            <th>{{ number_format($credit - $debit, 2) }}</th>
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

    @elseif(($reportType == 'client' || $reportType == 'employee' || $reportType == 'supplier' || $reportType == 'deposit') && !empty($currentPeople))

        @if(!empty($saleDebts) || !empty($purchaseDebts) || !empty($employeeGifts) || !empty($depositDebts))
            <div class="card bg-white mt-2 shadow">
                <div class="card-body p-2 invoice" style="page-break-after: unset" dir="rtl">
                    <div class="row align-items-center">
                        @if($currentPeople['type'] == 'employee')
                            <div class="col-2">
                                <h6 class="m-0 px-2">السحوبات : {{ number_format($giftsBalance, 2) }}</h6>
                            </div>
                            <div class="col-2">
                                <h6 class="m-0 px-2">المبيعات : {{ number_format($salesBalance, 2) }}</h6>
                            </div>
                            <div class="col-2">
                                <h6 class="m-0 px-2">المشتريات : {{ number_format($purchasesBalance, 2) }}</h6>
                            </div>
                        @else
                            <div class="col-3">
                                <h6 class="m-0 px-2">المشتريات : {{ number_format($purchasesBalance, 2) }}</h6>
                            </div>
                            <div class="col-3">
                                <h6 class="m-0 px-2">المبيعات : {{ number_format($salesBalance, 2) }}</h6>
                            </div>
                        @endif
                        <div class="col-3">
                            <h6 class="m-0 px-2">العهد : {{ number_format($depositsBalance, 2) }}</h6>
                        </div>

                        <div class="col-3">
                            <h6 class="m-0 px-2">
                                الجمله
                                : @if($currentPeople['type'] == 'supplier')
                                    {{ number_format($purchasesBalance - ($salesBalance + $depositsBalance), 2) }}
                                @elseif($currentPeople['type'] == 'client' || $currentPeople['type'] == 'deposit')
                                    {{ number_format(($salesBalance + $depositsBalance) - $purchasesBalance, 2) }}
                                @else
                                    {{ number_format(($depositsBalance + $salesBalance + $giftsBalance) - $purchasesBalance, 2) }}
                                @endif
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(!empty($saleDebts))
            <div class="card mt-2">
                <div class="card-body invoice" dir="rtl">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-4"><h5>{{$currentPeople['name'] ?? ''}}</h5>
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
                                $currentBalance = $currentPeople['initialSalesBalance'];
                                $debit = $currentPeople['initialSalesBalance'];
                                $credit = 0;
                            @endphp
                            <tr>
                                <td></td>
                                <td>الرصيد السابق</td>
                                <td>{{ number_format($currentPeople['initialSalesBalance'], 2) }}</td>
                                <td>0</td>
                                <td>{{ number_format($currentBalance, 2) }}</td>
                            </tr>
                            @foreach($statements as $debt)
                                @php
                                    $debit += floatval($debt['debit']);
                                    $credit += floatval($debt['credit']);
                                    $currentBalance =  $debit - $credit;
                                @endphp
                                <tr>
                                    <td>{{$debt['due_date']}}</td>
                                    <td @if($debt['invoice_id'] != null) data-bs-toggle="modal"
                                        data-bs-target="#printModal"
                                        wire:click="getInvoice({{$debt['invoice_id']}}, '{{$debt['tableName']}}')"
                                        @endif>{{ $debt['note'] }}</td>
                                    <td>{{ number_format($debt['debit'], 2) }}</td>
                                    <td>{{ number_format($debt['credit'], 2) }}</td>
                                    <td>{{number_format($currentBalance, 2)}}</td>
                                </tr>
                            @endforeach

                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="2">الجــــــــــــــــمله</th>
                                <th>{{ number_format($debit, 2) }}</th>
                                <th>{{ number_format($credit, 2) }}</th>
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
                            <div class="col-4"><h5> {{$currentPeople['name'] ?? ''}} بالتفصيل</h5>
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
                            @foreach($statements as $debt)
                                <tr>
                                    <td>{{$debt['due_date']}}</td>
                                    <td colspan="3">{{ $debt['note'] }}</td>
                                    <td>{{ number_format($debt['debit'] != 0 ? $debt['debit'] : $debt['credit'], 2) }}</td>
                                </tr>
                                @if($debt['invoice_id'] != null)
                                    @if($debt['tableName'] == "sales")
                                        @php $invoice = \App\Models\Sale::find($debt['invoice_id'])->saleDetails; @endphp
                                    @elseif($debt['tableName'] == "purchases")
                                        @php $invoice = \App\Models\Purchase::find($debt['invoice_id'])->purchaseDetails; @endphp
                                    @endif
                                    @foreach($invoice as $product)
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

        @if(100 == 200)
            @if(!empty($purchaseDebts))
                <div class="card mt-2">
                    <div class="card-body invoice" dir="rtl">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-4"><h5>مشتريات
                                        : {{$currentPeople['name'] ?? ''}}</h5>
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
                                    $currentBalance = floatval($currentPeople['initialPurchasesBalance']);
                                    $credit = floatval($currentPeople['initialPurchasesBalance']);
                                    $debit = 0;
                                @endphp
                                <tr>
                                    <td></td>
                                    <td>الرصيد السابق</td>
                                    <td>0</td>
                                    <td>{{ number_format($currentPeople['initialPurchasesBalance'], 2) }}</td>
                                    <td>{{ number_format($currentBalance, 2) }}</td>
                                </tr>
                                @foreach($purchaseDebts as $debt)
                                    @php
                                        $debit += floatval($debt['debit']);
                                        $credit += floatval($debt['credit']);
                                        $currentBalance =  $credit - $debit;
                                    @endphp
                                    <tr>
                                        <td>{{$debt['due_date']}}</td>
                                        <td @if($debt['invoice_id'] != null) data-bs-toggle="modal"
                                            data-bs-target="#printModal"
                                            wire:click="getInvoice({{$debt['invoice_id']}}, '{{$debt['tableName']}}')"
                                            @endif>{{ $debt['note'] }}</td>
                                        <td>{{ number_format($debt['debit'], 2) }}</td>
                                        <td>{{ number_format($debt['credit'], 2) }}</td>
                                        <td>{{number_format($currentBalance, 2)}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="2">الجــــــــــــــــمله</th>
                                    <th>{{ number_format($debit, 2) }}</th>
                                    <th>{{ number_format($credit, 2) }}</th>
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
                                        : {{$currentPeople['name'] ?? ''}} بالتفصيل</h5>
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
                                        <td>{{ number_format($debt['debit'] != 0 ? $debt['debit'] : $debt['debit'], 2) }}</td>
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

            @if(!empty($employeeGifts))
                <div class="card mt-2">
                    <div class="card-body invoice" dir="rtl">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-4"><h5>مدفوعات الى
                                        : {{$currentPeople['name'] ?? ''}}</h5>
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
                                    @if($gift['type'] == "salary")
                                        <tr>
                                            @php
                                                $total += $gift['debit'];
                                            @endphp
                                            <td>{{$gift['due_date']}}</td>
                                            <td>{{$gift['note']}}</td>
                                            <td>{{number_format($gift['debit'], 2)}}</td>
                                        </tr>
                                    @endif
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

                <div class="card mt-2">
                    <div class="card-body invoice" dir="rtl">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-4"><h5>سحوبات
                                        : {{$currentPeople['name'] ?? ''}}</h5>
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
                                    $currentBalance = $currentPeople['initialGiftsBalance'];
                                    $debit = 0;
                                    $credit = $currentPeople['initialGiftsBalance'];
                                @endphp
                                <tr>
                                    <td></td>
                                    <td>الرصيد السابق</td>
                                    <td>{{ number_format($currentPeople['initialGiftsBalance'], 2) }}</td>
                                    <td>0</td>
                                    <td>{{ number_format($currentBalance, 2) }}</td>
                                </tr>
                                @foreach($employeeGifts as $debt)
                                    @if($debt['type'] != "salary")
                                        @php
                                            $debit += floatval($debt['debit']);
                                            $credit += floatval($debt['credit']);
                                            $currentBalance = $debit - $credit;
                                        @endphp
                                        <tr>
                                            <td>{{$debt['due_date']}}</td>
                                            <td @if($debt['invoice_id'] != null) data-bs-toggle="modal"
                                                data-bs-target="#printModal"
                                                wire:click="getInvoice({{$debt['invoice_id']}}, '{{$debt['tableName']}}')"
                                                @endif>{{ $debt['note'] }}</td>
                                            <td>{{ number_format($debt['debit'], 2) }}</td>
                                            <td>{{ number_format($debt['credit'], 2) }}</td>
                                            <td>{{number_format($currentBalance, 2)}}</td>
                                        </tr>
                                    @endif
                                @endforeach

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="2">الجــــــــــــــــمله</th>
                                    <th>{{ number_format($debit, 2) }}</th>
                                    <th>{{ number_format($credit, 2) }}</th>
                                    <th>{{ number_format($currentBalance, 2) }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if(!empty($depositDebts))
                <div class="card mt-2">
                    <div class="card-body invoice" dir="rtl">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-4"><h5>العهد والأمانات
                                        : {{$currentPeople['name'] ?? ''}}</h5>
                                </div>
                                <div class="col-4"><h5>الرصيد : {{number_format($depositsBalance, 2)}}</h5></div>

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
                                    $currentBalance = $currentPeople['initialDepositsBalance'];
                                    $incomes = 0;
                                    $expenses = $currentPeople['initialDepositsBalance'];
                                @endphp
                                <tr>
                                    <td></td>
                                    <td>الرصيد السابق</td>
                                    <td>0</td>
                                    <td>{{ number_format($currentPeople['initialDepositsBalance'], 2) }}</td>
                                    <td>{{ number_format($currentBalance, 2) }}</td>
                                </tr>
                                @foreach($depositDebts as $debt)
                                    @php
                                        $expenses += $debt['type'] == 'pay' ? floatval($debt['amount']) : 0;
                                        $incomes += $debt['type'] == 'debt' ? floatval($debt['amount']) : 0;
                                        $currentBalance += floatval($debt['type'] == "pay" ? floatval($debt['amount']) : 0) - floatval($debt['type'] == "debt" ? floatval($debt['amount']) : 0);
                                    @endphp
                                    <tr>
                                        <td>{{$debt['due_date']}}</td>
                                        <td>{{ $debt['note'] }}</td>
                                        <td>{{ $debt['type'] == "debt" ? number_format($debt['amount'], 2) : 0 }}</td>
                                        <td>{{ $debt['type'] == "pay" ? number_format($debt['amount'], 2) : 0 }}</td>
                                        <td>{{number_format($currentBalance, 2)}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="2">الجــــــــــــــــمله</th>
                                    <th>{{ number_format($incomes, 2) }}</th>
                                    <th>{{ number_format($expenses, 2) }}</th>
                                    <th>{{ number_format($currentBalance, 2) }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
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
    @elseif($reportType == 'percent')

        <div class="card mt-2">
            <div class="card-body invoice" dir="rtl">
                <div class="card-title">
                    <div class="row">
                        <div class="col-3">
                            <h5>نسبة الارباح من المبيعات</h5>
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control text-center" wire:model.live="percent">
                        </div>
                    </div>
                </div>
                <div class="scroll">
                    <table class="text-center printInvoice">
                        <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>إسم المنتج</th>
                            <th>الكميه المباعة</th>
                            <th>الكميه المطلوبة</th>
                            <th>سعر اليوم</th>
                            <th>الجمله</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $total = 0 @endphp
                        @foreach($sales as $index => $sale)
                            @php
                                $amount = $sale['quantity'] * floatval($percent) / 100;
                                $total += isset($prices[$index]) ? floatval($amount) * floatval($prices[$index]) : 0;
                            @endphp

                            <tr>
                                <td data-bs-toggle="modal" data-bs-target="#printModal"
                                    wire:click="getInvoice({{$sale['sale_id']}}, 'sales')">{{$sale['due_date']}}</td>
                                <td>{{ $sale['productName'] }}</td>
                                <td>{{number_format($sale['quantity'], 2)}}</td>
                                <td>{{$amount}}</td>
                                <td><input wire:model.live="prices.{{$index}}" class="form-control text-center"/></td>
                                <td>
                                    @if(isset($prices[$index]))
                                        {{ number_format(floatval($prices[$index]) * $amount, 2) }}
                                    @else
                                        0
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        @if(!empty($currentProduct))
                            <tr>
                                <th colspan="4">الجــــــــــــــــــــملة</th>
                                <th>{{number_format($quantity, 2)}}</th>
                                <th>{{number_format($total, 2)}}</th>
                            </tr>
                        @else
                            <tr>
                                <td colspan="5">الجــــــــــــــــــــملة</td>
                                <td>{{number_format($total, 2)}}</td>
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
                                <th>رقم الفاتوره</th>
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
                                    <td></td>
                                    <td style="cursor:pointer;">الكمية السابقه</td>
                                    <td>{{ number_format($currentProduct['initialStock'], 2) }}</td>
                                    <td>0</td>
                                    <td>{{ number_format($currentProduct['initialStock'], 2) }}</td>
                                </tr>
                            @endif
                            @foreach($trackingProducts as $item)
                                <tr @if($item['invoice_id'] != null) data-bs-toggle="modal" data-bs-target="#printModal"
                                    wire:click="getInvoice({{$item['invoice_id']}}, '{{$item['tableName']}}')" @endif>
                                    @php
                                        $currentStock += floatval($item['income']) - floatval($item['expense']);
                                        $income += floatval($item['income']);
                                        $expense += floatval($item['expense']);
                                    @endphp
                                    <td>{{ $item['due_date'] }}</td>
                                    <td>{{ $item['invoice_id'] }}</td>
                                    <td>{{ $item['note'] }}</td>
                                    <td>{{ number_format($item['income'], 2) }}</td>
                                    <td>{{ number_format($item['expense'], 2) }}</td>
                                    <td>{{ number_format($currentStock, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="3">الجمـــــــــــــــلة</th>
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
                            <th>وسيلة الدفع</th>
                            <th>الوارد</th>
                            <th>الصادر</th>
                            <th>آجل وارد</th>
                            <th>آجل صادر</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $debit = $futureDebit = $futureCredit = 0;
                            $credit = (($payment == "" || $payment == "cash") ? $initialSafeBalance : 0) + (($payment == "" || $payment == "bank") ? $initialBankBalance : 0);
                        @endphp
                        @if($payment == "cash" || $payment == "")
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>رصيد الخزنه السابق</td>
                                <td>كاش</td>
                                <td>{{ number_format($initialSafeBalance, 2) }}</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                        @endif
                        @if($payment == "bank" || $payment == "")
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>رصيد البنك السابق</td>
                                <td>بنك</td>
                                <td>{{ number_format($initialBankBalance, 2) }}</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                        @endif
                        @foreach($statements as $statement)
                            @if($payment != "")
                                @if($payment != $statement['payment'])
                                    @continue
                                @endif
                            @endif
                            @php
                                $debit += $statement['real'] ? floatval($statement['debit']) : 0;
                                $credit += $statement['real'] ? floatval($statement['credit']) : 0;
                                $futureDebit += $statement['real'] ? 0 : floatval($statement['debit']);
                                $futureCredit += $statement['real'] ? 0 : floatval($statement['credit']);
                            @endphp

                            <tr>
                                <td>{{ $statement['due_date'] }}</td>
                                <td>{{ $accounts[$statement['tableName']] }}</td>
                                <td>{{ $statement['ownerName'] }}</td>
                                <td @if($statement['invoice_id'] != null) data-bs-toggle="modal"
                                    data-bs-target="#printModal"
                                    wire:click="getInvoice({{$statement['invoice_id']}}, '{{$statement['tableName']}}')"
                                    style="cursor:pointer;" @endif >{{ $statement['note'] }}</td>
                                <td>{{ $statement['payment'] == "cash" ? "كاش" : ($statement['payment']== "bank" ? "بنك" : "") }}</td>
                                <td>{{ $statement['real'] ? number_format($statement['credit'], 2) : 0 }}</td>
                                <td>{{ $statement['real'] ? number_format($statement['debit'], 2) : 0 }}</td>
                                <td>{{ $statement['real'] ? 0 : number_format($statement['credit'], 2) }}</td>
                                <td>{{ $statement['real'] ? 0 : number_format($statement['debit'], 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="5">الجمـــــــــــــــلة</th>
                            <th>{{ number_format($credit, 2) }}</th>
                            <th>{{ number_format($debit, 2) }}</th>
                            <th>{{ number_format($futureCredit, 2) }}</th>
                            <th>{{ number_format($futureDebit, 2) }}</th>
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
                            <th>وسيلة الدفع</th>
                            @if($showType == "income" || $showType == null)
                                <th>الوارد</th>
                            @endif
                            @if($showType == "expense" || $showType == null)
                                <th>الصادر</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>

                        @php
                            $credit = (($payment == "" || $payment == "cash") ? $initialSafeBalance : 0) + (($payment == "" || $payment == "bank") ? $initialBankBalance : 0);
                            $debit = 0;
                        @endphp
                        @if($payment == "cash" || $payment == "")
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>رصيد الخزنه السابق</td>
                                <td>كاش</td>
                                @if($showType == "income" || $showType == null)
                                    <td>{{ number_format($initialSafeBalance, 2) }}</td>
                                @endif
                                @if($showType == "expense" || $showType == null)
                                    <td>0</td>
                                @endif
                            </tr>
                        @endif
                        @if($payment == "bank" || $payment == "")
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>رصيد البنك السابق</td>
                                <td>بنك</td>
                                @if($showType == "income" || $showType == null)
                                    <td>{{ number_format($initialBankBalance, 2) }}</td>
                                @endif
                                @if($showType == "expense" || $showType == null)
                                    <td>0</td>
                                @endif
                            </tr>
                        @endif
                        @foreach($statements as $statement)
                            @if(floatval($statement['real']))
                                @php
                                    $debit += floatval($statement['debit']);
                                    $credit += floatval($statement['credit']);
                                @endphp
                                <tr>
                                    <td>{{ $statement['due_date'] }}</td>
                                    <td>{{ $accounts[$statement['tableName']] }}</td>
                                    <td>{{ $statement['ownerName'] }}</td>
                                    <td @if($statement['invoice_id'] != null) data-bs-toggle="modal"
                                        data-bs-target="#printModal"
                                        wire:click="getInvoice({{$statement['invoice_id']}}, '{{$statement['tableName']}}')"
                                        style="cursor:pointer;" @endif >{{ $statement['note'] }}</td>
                                    <td>{{ $statement['payment'] == "cash" ? "كاش" : ($statement['payment']== "bank" ? "بنك" : "") }}</td>
                                    @if($showType == "income" || $showType == null)
                                        <td>{{ $statement['real'] ? number_format($statement['credit'], 2) : 0 }}</td>
                                    @endif
                                    @if($showType == "expense" || $showType == null)
                                        <td>{{ $statement['real'] ? number_format($statement['debit'], 2) : 0 }}</td>
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="5">الجمـــــــــــــــلة</th>
                            @if($showType == "income" || $showType == null)
                                <th>{{ number_format($credit, 2) }}</th>
                            @endif
                            @if($showType == "expense" || $showType == null)
                                <th>{{ number_format($debit, 2) }}</th>
                            @endif
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @elseif($reportType == 'bank' && !empty($statements))
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
                            <th>رقم الفاتوره</th>
                            <th>الجهة</th>
                            <th>رقم الإشعار</th>
                            @if($showType == "income" || $showType == null)
                                <th>الوارد</th>
                            @endif
                            @if($showType == "expense" || $showType == null)
                                <th>الصادر</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>

                        @php
                            $credit = (($payment == "" || $payment == "cash") ? $initialSafeBalance : 0) + (($payment == "" || $payment == "bank") ? $initialBankBalance : 0);
                            $debit = 0;
                        @endphp
                        @if($payment == "cash" || $payment == "")
                            <tr>
                                <td></td>
                                <td></td>
                                <td>رصيد الخزنه السابق</td>
                                <td></td>
                                @if($showType == "income" || $showType == null)
                                    <td>{{ number_format($initialSafeBalance, 2) }}</td>
                                @endif
                                @if($showType == "income" || $showType == null)
                                    <td>0</td>
                                @endif
                            </tr>
                        @endif
                        @if($payment == "bank" || $payment == "")
                            <tr>
                                <td></td>
                                <td></td>
                                <td>رصيد البنك السابق</td>
                                <td></td>
                                @if($showType == "income" || $showType == null)
                                    <td>{{ number_format($initialBankBalance, 2) }}</td>
                                @endif
                                @if($showType == "expense" || $showType == null)
                                    <td>0</td>
                                @endif
                            </tr>
                        @endif
                        @foreach($statements as $statement)
                            @if(floatval($statement['real']))
                                @php
                                    $debit += floatval($statement['debit']);
                                    $credit += floatval($statement['credit']);
                                @endphp
                                <tr>
                                    <td>{{ $statement['due_date'] }}</td>
                                    <td>{{ $statement['invoice_id'] }}</td>
                                    <td @if($statement['invoice_id'] != null) data-bs-toggle="modal"
                                        data-bs-target="#printModal"
                                        wire:click="getInvoice({{$statement['invoice_id']}}, '{{$statement['tableName']}}')"
                                        style="cursor:pointer;" @endif >{{ $statement['note'] }} @if($statement['ownerName'] != null)
                                            {{ " | " . $statement['ownerName'] }}
                                        @endif</td>
                                    <td>{{ $statement['bank'] }}</td>
                                    @if($showType == "income" || $showType == null)
                                        <td>{{ $statement['real'] ? number_format($statement['credit'], 2) : 0 }}</td>
                                    @endif
                                    @if($showType == "expense" || $showType == null)
                                        <td>{{ $statement['real'] ? number_format($statement['debit'], 2) : 0 }}</td>
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="4">الجمـــــــــــــــلة</th>
                            @if($showType == "income" || $showType == null)
                                <th>{{ number_format($credit, 2) }}</th>
                            @endif
                            @if($showType == "expense" || $showType == null)
                                <th>{{ number_format($debit, 2) }}</th>
                            @endif
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

