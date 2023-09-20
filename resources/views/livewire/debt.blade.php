<div>
    <!-- Choose Client Modal -->
    <div wire:ignore.self class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="clientModalLabel">العملاء</h1>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <div class="row">
                                    <div class="col-4 align-self-center"><h5>العملاء</h5></div>
                                    <div class="col-8"><input type="text" placeholder="بحث ..." class="form-control"
                                                              wire:model.live="clientSearch"></div>
                                </div>
                            </div>
                            <table class="table table-responsive text-center">
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

    <!-- Show Sale Model -->

    <div wire:ignore.self class="modal fade" id="showSaleModal" tabindex="-1" aria-labelledby="showSaleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h1 class="modal-title fs-5" id="showSaleModalLabel">فاتوره</h1>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-body">
                                <div class="card-title">
                                    <div class="row">
                                        <div class="col-4"><h5>الفاتوره {{$id != 0 ? '#'. $id : ''}}</h5></div>
                                    </div>

                                </div>
                                <table class="table text-center table-responsive table-responsive table-responsive">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">إسم المنتج</th>
                                        <th scope="col">سعر الوحده</th>
                                        <th scope="col">الكميه</th>
                                        <th scope="col">الجمله</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($details as $detail)
                                        <tr class="align-items-center">
                                            <td scope="row">{{$loop->index + 1}}</td>
                                            <td>{{$detail['product']['productName']}}</td>
                                            <td>{{number_format($detail['price'], 2)}}</td>
                                            <td>{{number_format($detail['quantity'], 2)}}</td>
                                            <td>{{number_format($detail['quantity']*$detail['price'], 2)}}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>الجمله</td>
                                        <td>{{number_format($total_amount, 2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>المدفوع</td>
                                        <td>{{number_format($paid, 2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>المتبقي</td>
                                        <td>{{number_format($total_amount - $paid, 2)}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <x-title :$title/>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#clientModal"><i
                            class="bi bi-person"></i></button>
                    <div class="card-title mt-2">
                        <div class="row">
                            <div class="col-3 align-self-center"><h5>الفواتير</h5></div>
                            <div class="col"><input type="text" wire:model.live="saleSearch"
                                                    class="form-control text-center" placeholder="بحث ....."></div>
                        </div>
                    </div>
                    <table class="table table-responsive text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>التاريخ</th>
                            <th>المتبقي</th>
                            <th>التحكم</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($saleDebts))
                            @foreach($saleDebts as $sale)
                                <tr>
                                    <td>{{$sale->id}}</td>
                                    <td>{{$sale->sale_date}}</td>
                                    <td>{{$sale->total_amount - $sale->sale_debts_sum_paid}}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                                                data-bs-target="#showSaleModal" wire:click="showSale({{$sale}})"><i
                                                class="bi bi-eye"></i>
                                        </button>
                                        /
                                        <button class="btn btn-sm btn-success" wire:click="getDebts({{$sale}})"><i
                                                class="bi bi-currency-dollar"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-8">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="card-title">
                        <h6>سداد</h6>
                        <div class="row">
                            <div class="col-3">
                                <label for="debtPaid">المبلغ المدفوع</label>
                                <input type="text" wire:model.live="debtPaid" id="debtPaid"
                                       class="form-control text-center"
                                       placeholder="المدفوع ....">
                            </div>

                            <div class="col-3">
                                <label for="debtRemainder">المتبقي</label>
                                <input type="text" wire:model.live="debtRemainder" disabled id="debtRemainder"
                                       class="form-control text-center" placeholder="المتبقي ....">
                            </div>

                            <div class="col-3">
                                <label for="due_date">التاريخ</label>
                                <input type="date" wire:model.live="due_date" id="due_date"
                                       class="form-control text-center">
                            </div>

                            <div class="col-3">
                                <label for="payment">طريقة الدفع</label>
                                <select class="form-select text-center" wire:model.live="payment">
                                    <option value="cash">كاش</option>
                                    <option value="bank">بنك</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">

                            <div class="col-3">
                                <label for="payment">البنك</label>
                                <select class="form-select text-center" wire:model.live="bank_id">
                                    @foreach($banks as $bank)
                                        <option value="{{$bank->id}}">{{$bank->bankName}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-3">
                                <label for="bank">رقم الايصال</label>
                                <input type="text" wire:model="bank" id="bank" class="form-control text-center"
                                       placeholder="رقم الايصال ....">
                            </div>

                            <div class="col-2 d-flex align-items-end">
                                <button class="btn btn-{{$debtId == 0 ? 'primary' : 'success'}} w-100"
                                        wire:click="payDebt()">{{$debtId == 0 ? 'دفــــع' : 'تعــــديل'}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h5>المدفوعات لفاتورة رقم {{$id}}</h5>
                    </div>
                    <table class="table table-responsive text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>التاريخ</th>
                            <th>المبلغ</th>
                            <th>المتبقي</th>
                            <th>الدفع</th>
                            <th>الإيصال</th>
                            <th>التحكم</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($debts as $debt)
                            <tr>
                                <td>{{$loop->index + 1}}</td>
                                <td>{{$debt['due_date']}}</td>
                                <td>{{number_format($debt['paid'], 2)}}</td>
                                <td>{{number_format($debt['remainder'], 2)}}</td>
                                <td>{{$debt['payment']}}</td>
                                <td>{{$debt['bank']}}</td>
                                <td>
                                    <button class="btn btn-sm btn-info text-white"
                                            wire:click="chooseDebt({{$debt['id']}})"><i class="bi bi-pen"></i></button>
                                    /
                                    <button class="btn btn-sm btn-danger" wire:click="deleteDebt({{$debt['id']}})"><i
                                            class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
