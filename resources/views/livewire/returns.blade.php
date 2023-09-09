<div>

    <div wire:ignore.self class="modal fade" id="clientsModal" tabindex="-1" aria-labelledby="clientsModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="bankModalLabel">العملاء</h1>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <input type="text" placeholder="إسم العميل ...." wire:model.live="clientSearch" class="form-control text-center">
                            <table class="table table-responsive text-center">
                                <thead>
                                <tr>
                                    <th>إسم العميل</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($clients as $client)
                                    <tr style="cursor: pointer" wire:click="chooseClient({{$client}})"
                                        data-bs-dismiss="modal">
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

    <div class="row my-2">
        <div class="col-4">
            <div class="card mb-2">
                <div class="card-body">
                    <input type="text" class="form-control text-center" style="cursor: pointer"
                           wire:model="currentClient.clientName" readonly placeholder="اسم العميل ...."
                           data-bs-toggle="modal"
                           data-bs-target="#clientsModal">
                </div>
            </div>

            @if(!empty($currentClient))
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-3"><h6>فواتير {{$currentClient['clientName']}}</h6></div>
                                <div class="col-9"><input type="text" placeholder="رقم الفاتوره ...." class="form-control text-center" wire:model.live="saleSearch"></div>
                            </div>
                        </div>
                        <table class="table text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>المبلغ</th>
                                <th>التاريخ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sales as $sale)
                                <tr wire:click="chooseSale({{$sale}})">
                                    <td>{{$sale['id']}}</td>
                                    <td>{{number_format($sale['total_amount'], 2)}}</td>
                                    <td>{{$sale['sale_date']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        @if(!empty($saleDetails))
            <div class="col-8">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="card-title"></div>
                        <div class="row">
                            <div class="col">
                                <input type="text" disabled wire:model="productName" class="form-control text-center"
                                       placeholder="إسم المنتج">
                            </div>
                            <div class="col">
                                <input type="text" disabled wire:model="price" class="form-control text-center"
                                       placeholder="سعر الوحده">
                            </div>
                            <div class="col">
                                <input type="text" disabled wire:model="quantity" class="form-control text-center"
                                       placeholder="الكمية">
                            </div>
                            <div class="col">
                                <input type="text" disabled wire:model="amount" class="form-control text-center"
                                       placeholder="الجمله">
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col">
                                <input type="text" wire:model="quantityReturn" wire:change="calcQuantity()"
                                       class="form-control text-center"
                                       placeholder="سعر الوحده">
                            </div>

                            <div class="col">
                                <button @disabled(empty($currentProduct) || ($quantityReturn == 0)) class="btn btn-primary" wire:click="save()">حــــــــــــــفظ</button>
                            </div>
                        </div>
                    </div>
                </div>
                @if(!empty($currentSale))
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <div class="row">
                                    <div class="col-2"><h6>فاتوره رقم {{ $id }}</h6></div>
                                    <div class="col"><h6>{{$currentSale['sale_date']}}</h6></div>
                                </div>
                            </div>
                            <table class="table text-center">
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
                                @foreach($saleDetails as $detail)
                                    <tr wire:click="chooseProduct({{$detail}}, {{$detail['product']}})">
                                        <td>{{$loop->index + 1}}</td>
                                        <td>{{$detail['product']['productName']}}</td>
                                        <td>{{number_format($detail['price'], 2)}}</td>
                                        <td>{{number_format($detail['quantity'], 2)}}</td>
                                        <td>{{number_format($detail['quantity'] * $detail['price'], 2)}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>الجمله</td>
                                    <td>{{$currentSale['total_amount']}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
