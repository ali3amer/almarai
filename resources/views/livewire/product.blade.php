<div wire:keydown.escape.window="resetData()">

    <x-title :$title/>

    <div class="invoice d-none d-print-block">
        <h2 dir="rtl">قائمة الأسعار</h2>
        <div>
            <table class="table text-center printInvoice">
                <thead>
                <tr>
                    <th>إسم المنتج</th>
                    <th>سعر الوحده</th>
                    <th>الكمية المطلوبة</th>
                    <th>الجمله</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($list))
                    @foreach($list as $product)
                        @if($product->stock > 0)
                            <tr>
                                <td>{{ $product->productName }}</td>
                                <td>{{ $product->sale_price }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
    {{--    <livewire:Title :$title />--}}


    <div class="row mt-2">
        <div class="col-3">
            <div class="card ">
                <div class="card-body">

                    @if($settings && $settings->barcode)
                        <label for="barcode" class="form-label">الباركود</label>
                        <input type="text" autocomplete="off" wire:keydown.enter="getProduct()"
                               wire:model.live="form.barcode"
                               class="form-control @error('form.barcode') is-invalid @enderror"
                               placeholder="الباركود ..." id="barcode">
                        <div>
                            @error('form.barcode') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    @if($settings && $settings->batch)
                        <label for="batch" class="form-label">الباتش</label>
                        <input type="text" autocomplete="off" wire:model="form.batch"
                               class="form-control @error('form.batch') is-invalid @enderror"
                               placeholder="الباتش ..." id="batch">
                        <div>
                            @error('form.batch') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <label for="productName" class="form-label">إسم المنتج</label>
                    <input type="text" autocomplete="off" wire:model="form.productName"
                           class="form-control @error('form.productName') is-invalid @enderror"
                           placeholder="إسم المنتج ..." id="productName">
                    <div>
                        @error('form.productName') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    <label for="unit" class="form-label">الوحده</label>
                    <input type="text" autocomplete="off" wire:model="form.unit" class="form-control"
                           placeholder="الوحده ..." id="unit">

                    <label for="store_id" class="form-label">المخزن</label>
                    <select wire:model="form.store_id" id="store_id"
                            class="form-select @error('form.store_id') is-invalid @enderror">
                        <option value=0>------------------</option>
                        @foreach($stores as $store)
                            <option
                                value="{{ $store->id }}">{{ $store->storeName }}</option>
                        @endforeach
                    </select>
                    <div>
                        @error('form.store_id') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    <label for="category_id" class="form-label">القسم</label>
                    <select wire:model="form.category_id" id="category_id"
                            class="form-select @error('form.category_id') is-invalid @enderror">
                        <option value=0>------------------</option>
                        @foreach($categories as $category)
                            <option
                                value="{{ $category->id }}">{{ $category->categoryName }}</option>
                        @endforeach
                    </select>
                    <div>
                        @error('form.category_id') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    <label for="stock" class="form-label">الكمية الإفتتاحيه</label>
                    <input type="text" autocomplete="off" wire:model="form.initialStock"
                           class="form-control @error('form.initialStock') is-invalid @enderror"
                           placeholder="الكمية الإفتتاحيى ..." id="initialStock">
                    <div>
                        @error('form.initialStock') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    @if($form->id != 0)
                        <label for="stock" class="form-label">الكمية</label>
                        <input type="text" disabled autocomplete="off" wire:model="stock"
                               class="form-control @error('stock') is-invalid @enderror"
                               placeholder="الكمية ..." id="stock">
                        <div>
                            @error('stock') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                    @endif


                    <label for="purchase_price" class="form-label">سعر الجرد</label>
                    <input type="text" autocomplete="off" wire:model="form.purchase_price"
                           class="form-control @error('form.purchase_price') is-invalid @enderror"
                           placeholder="السعر الجرد ..." id="purchase_price">
                    <div>
                        @error('form.purchase_price') <span
                            class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    <label for="sale_price" class="form-label">سعر البيع</label>
                    <input type="text" autocomplete="off" wire:model="form.sale_price"
                           class="form-control @error('form.sale_price') is-invalid @enderror"
                           placeholder="السعر البيع..." id="sale_price">
                    <div>
                        @error('form.sale_price') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                    @if($settings && $settings->expired_date)
                        <label for="expired_date" class="form-label">تاريخ الانتهاء</label>
                        <input type="date" autocomplete="off" wire:model="form.expired_date"
                               class="form-control @error('form.expired_date') is-invalid @enderror"
                               id="expired_date">
                        <div>
                            @error('form.expired_date') <span
                                class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="d-grid mt-2">
                        <button wire:click="save({{ $form->id }})"
                                @disabled(!$create) class="btn btn- btn-{{$form->id == 0 ? 'primary' : 'success'}}">{{$form->id == 0 ? 'حفــــــــــــــــظ' : 'تعـــــــــــــديل'}}</button>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-9">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4">
                            <input wire:model.live="search" class="form-control"
                                   placeholder="بحث ......">
                        </div>
                        <div class="col-3">
                            <select class="form-select" wire:model.live="store_id">
                                <option value="0">----------------</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->storeName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <select class="form-select" wire:model.live="category_id">
                                <option value="0">----------------</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->categoryName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2">
                            <button class="btn btn- btn-primary" wire:click="priceList" id="print"><i
                                    class="bi bi-cash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(!empty($products) && $read)
                        <div class="scroll">
                            <table class="table text-center">
                                <thead>
                                <tr>
                                    <th>إسم المنتج</th>
                                    <th>الوحده</th>
                                    <th>المخزن</th>
                                    <th>القسم</th>
                                    <th>سعر الجرد</th>
                                    <th>سعر البيع</th>
                                    <th>الكميه الإفتتاحية</th>
                                    <th>الكميه</th>
                                    <th>التحكم</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{ $product->productName }}</td>
                                        <td>{{ $product->unit }}</td>
                                        <td>{{ $stores[$product->store_id]['storeName'] }}</td>
                                        <td>{{ $categories[$product->category_id]['categoryName'] }}</td>
                                        <td>{{ number_format($product->purchase_price, 2) }}</td>
                                        <td>{{ number_format($product->sale_price, 2) }}</td>
                                        <td>{{ number_format($product->initialStock, 2) }}</td>
                                        <td>{{ number_format($product->stock, 2) }}</td>
                                        <td>
                                            <button
                                                @disabled(!$update)  class="btn btn-sm btn-info text-white"
                                                wire:click="edit({{$product->id}})">
                                                <i class="bi bi-pen"></i>
                                            </button>
                                            /
                                            <button
                                                @disabled((!$delete)) class="btn btn-sm btn-danger"
                                                wire:click="deleteMessage({{$product}})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-danger text-center">لا يوجد منتجات .....</div>
                    @endif

                </div>
                <div class="card-footer">
                    {{ $products->links() }}
                </div>
            </div>
        </div>

    </div>
</div>
