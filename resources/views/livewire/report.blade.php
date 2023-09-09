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


    <x-title :$title/>
    <button class="d-print-none btn btn-primary position-fixed " style="bottom: 10px; border-radius: 50%" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling"><i
            class="bi bi-gear"></i></button>

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

                        <label for="client">العميل</label>
                        <input data-bs-toggle="modal" wire:model="currentClient.clientName" readonly
                               placeholder="إسم العميل ...." class="form-control" data-bs-target="#clientModal">
                    @else
                        <select class="form-select mt-2" wire:model.live="store_id" id="store_id">
                            <option value="0">-----------------</option>
                            @foreach($stores as $store)
                                <option value="{{$store->id}}">{{$store->storeName}}</option>
                            @endforeach
                        </select>
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

    <div class="card mt-2">
        <div class="card-body">
            @if($reportType == 'inventory' && !empty($products))
                <table class="table text-center">
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
            @elseif($reportType == 'client' && !empty($currentClient) && !empty($sales))
                <caption style="direction: rtl ">{{$currentClient['clientName']}}</caption>
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
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @elseif($reportType == 'supplier') @elseif($reportType == 'safe') @elseif($reportType == 'sales') @elseif($reportType == 'purchases')@endif
        </div>
    </div>
</div>
