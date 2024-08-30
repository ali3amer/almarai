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
                                        <input type="text" autocomplete="off"  placeholder="إسم العميل ...." wire:model.live="clientSearch" class="form-control text-center">
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
                                        <div class="col-4"><h6>فاتوره رقم {{ $currentSale['id'] }}</h6></div>
                                        <div class="col"><h6>{{$currentSale['due_date']}}</h6></div>
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
                                            <tr style="cursor:pointer;" wire:click="chooseDetail({{$detail}}, {{$detail['product']}})" data-bs-dismiss="modal">
                                                <td>{{$loop->index + 1}}</td>
                                                <td>{{$detail['product']['productName']}}</td>
                                                <td>{{number_format($detail['price'], 2)}}</td>
                                                <td>{{number_format($detail['quantity'], 2)}}</td>
                                                <td>{{number_format($detail['quantity'] * $detail['price'], 2)}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td>الجمله</td>
                                            <td>{{number_format($currentSale['amount'], 2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>المدفوع</td>
                                            <td>{{number_format($currentSale['paid'], 2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>المتبقي</td>
                                            <td>{{number_format($currentSale['remainder'], 2)}}</td>
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
                    <input type="text" autocomplete="off"  class="form-control text-center" style="cursor: pointer"
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
                                <div class="col-3"><h6>فواتير {{$currentClient[$buyer.'Name'] ?? ''}}</h6></div>
                                <div class="col-9"><input type="text" autocomplete="off"  placeholder="رقم الفاتوره ...." class="form-control text-center" wire:model.live="saleSearch"></div>
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
                                        <td>{{number_format($sale['amount'], 2)}}</td>
                                        <td>{{$sale['due_date']}}</td>
                                        <td>
                                            <button class="btn btn-sm text-white btn-warning" wire:click="getReturns({{$sale}})"><i class="bi bi-eye"></i></button> /
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

                                <input type="text" autocomplete="off"  id="price" disabled wire:model="price" class="form-control text-center"
                                       placeholder="سعر الوحده">
                            </div>
                            <div class="col">
                                <label for="quantity">الكمية</label>

                                <input type="text" autocomplete="off"  id="quantity" disabled wire:model="quantity" class="form-control text-center"
                                       placeholder="الكمية">
                            </div>
                            <div class="col">
                                <label for="amount">الجمله</label>

                                <input type="text" autocomplete="off"  id="amount" disabled value="{{ $quantity * $price }}" class="form-control text-center"
                                       placeholder="الجمله">
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col">
                                <label for="quantityReturn">الكمية المرجعه</label>

                                <input type="text" autocomplete="off" @disabled(empty($currentDetail)) id="quantityReturn" wire:model="quantityReturn" wire:keydown="calcQuantity()"
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

                                <input type="text" autocomplete="off" @disabled(empty($currentDetail) || (!empty($currentClient) && $currentClient['cash'])) @disabled(empty($currentSale) || $currentSale['paid'] == 0) id="paid" wire:model.live="amount"
                                       class="form-control text-center"
                                       placeholder="المبلغ المدفوع">
                            </div>

                            <div class="col">
                                <label for="due_date">تاريخ الارجاع</label>
                                <input type="date" disabled @disabled(empty($currentDetail)) wire:model="due_date" class="form-control text-center">
                            </div>

                            @if(!session("closed"))
                                <div class="col d-flex align-items-end">
                                    <button @disabled(empty($currentDetail) || ($quantityReturn == 0) || ($quantityReturn == null) || ($quantityReturn > $quantity)) class="btn {{ $editMode ? 'btn-success' : 'btn-primary' }} " wire:click="save()">{{ $editMode ? 'تعـــــــــــــــديل' : 'حــــــــــــــفظ' }}</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if(!empty($returns) && !empty($currentSale))
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <div class="row">
                                    <div class="col-4"><h6>المنتجات المرجعه بفاتورة رقم {{ $currentSale['id'] }}</h6></div>
                                    <div class="col"><h6>{{"التاريخ الفاتورة : " . $currentSale['due_date']}}</h6></div>
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
