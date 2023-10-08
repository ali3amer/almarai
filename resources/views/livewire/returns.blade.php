<div>

    <!-- Client Modal -->
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
                            <div class="card-title">
                                <div class="row">
                                    <div class="col-3">
                                        <select class="form-select" wire:model.live="buyer">
                                            <option value="client">عملاء</option>
                                            <option value="employee">موظفين</option>
                                            <option value="supplier">موردين</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <input type="text" placeholder="إسم العميل ...." wire:model.live="clientSearch" class="form-control text-center">
                                    </div>
                                </div>
                            </div>
                            <div class="scroll">
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
                                            <td>{{$client[$buyer.'Name']}}</td>
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


    <!-- Sale Modal -->
    <div wire:ignore.self class="modal fade" id="saleModal" tabindex="-1" aria-labelledby="saleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="saleModalLabel"></h1>
                </div>
                <div class="modal-body">
                    @if(!empty($currentSale))
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <div class="row">
                                        <div class="col-4"><h6>فاتوره رقم {{ $id }}</h6></div>
                                        <div class="col"><h6>{{$currentSale['sale_date']}}</h6></div>
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
                                        @foreach($saleDetails as $detail)
                                            <tr wire:click="chooseDetail({{$detail}}, {{$detail['product']}})" data-bs-dismiss="modal">
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
                    <input type="text" class="form-control text-center" style="cursor: pointer"
                           wire:model.live="currentClient.{{$buyer.'Name'}}" readonly placeholder="اسم العميل ...."
                           data-bs-toggle="modal"
                           data-bs-target="#clientsModal">
                </div>
            </div>

            @if(!empty($currentClient))
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-3"><h6>فواتير {{$currentClient[$buyer.'Name']}}</h6></div>
                                <div class="col-9"><input type="text" placeholder="رقم الفاتوره ...." class="form-control text-center" wire:model.live="saleSearch"></div>
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
                                @foreach($sales as $sale)
                                    <tr>
                                        <td>{{$sale['id']}}</td>
                                        <td>{{number_format($sale['total_amount'], 2)}}</td>
                                        <td>{{$sale['sale_date']}}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" wire:click="getReturns({{$sale}})"><i class="bi bi-pen"></i></button> /
                                            <button  data-bs-toggle="modal" data-bs-target="#saleModal" wire:click="chooseSale({{$sale}}, false)" class="btn btn-sm btn-danger"><i class="bi bi-arrow-return-left"></i></button>
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
                                <input id="productName" type="text" disabled wire:model="productName" class="form-control text-center"
                                       placeholder="إسم المنتج">
                            </div>
                            <div class="col">
                                <label for="price">سعر الوحده</label>

                                <input type="text" id="price" disabled wire:model="price" class="form-control text-center"
                                       placeholder="سعر الوحده">
                            </div>
                            <div class="col">
                                <label for="quantity">الكمية</label>

                                <input type="text" id="quantity" disabled wire:model="quantity" class="form-control text-center"
                                       placeholder="الكمية">
                            </div>
                            <div class="col">
                                <label for="amount">الجمله</label>

                                <input type="text" id="amount" disabled wire:model="amount" class="form-control text-center"
                                       placeholder="الجمله">
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col">
                                <label for="quantityReturn">الكمية المرجعه</label>

                                <input type="text" id="quantityReturn" wire:model="quantityReturn" wire:keydown="calcQuantity()"
                                       class="form-control text-center"
                                       placeholder="الكمية المرجعه">
                            </div>

                            <div class="col">
                                <label for="return_date">تاريخ الارجاع</label>
                                <input type="date" wire:model="return_date" class="form-control text-center">
                            </div>

                            <div class="col d-flex align-items-end">
                                <button @disabled(empty($currentDetail) || ($quantityReturn == 0)) class="btn {{ $editMode ? 'btn-success' : 'btn-primary' }} " wire:click="save()">{{ $editMode ? 'تعـــــــــــــــديل' : 'حــــــــــــــفظ' }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                @if(!empty($returns) && !empty($currentSale))
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <div class="row">
                                    <div class="col-4"><h6>المنتجات المرجعه بفاتورة رقم {{ $currentSale['id'] }}</h6></div>
                                    <div class="col"><h6>{{$currentSale['sale_date']}}</h6></div>
                                </div>
                            </div>
                            <div class="scroll">
                                <table class="table table-hover text-center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>إسم المنتج</th>
                                        <th>سعر الوحده</th>
                                        <th> الكمية</th>
                                        <th>الجمله</th>
                                        <th>التاريخ</th>
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
                                            <td>{{$return['return_date']}}</td>
                                            <td></td>
                                            <td>
                                                <button  wire:click="chooseDetail({{$return}}, {{$return['product']}})" class="btn btn-sm btn-primary"><i class="bi bi-pen"></i></button>
                                                <button  wire:click="delete({{$return}})" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
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
