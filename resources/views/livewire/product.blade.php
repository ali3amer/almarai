<div>
    <x-title :$title></x-title>

    <div class="row mt-2">

        <div class="col-4">
            <div class="card ">
                <div class="card-body">
                    <form action="" wire:submit="save({{ $form->id }})">
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

                        <label for="sale_price" class="form-label">سعر البيع</label>
                        <input type="text" autocomplete="off" wire:model="form.sale_price"
                               class="form-control @error('form.sale_price') is-invalid @enderror"
                               placeholder="السعر البيع..." id="sale_price">
                        <div>
                            @error('form.sale_price') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>

                        <label for="purchase_price" class="form-label">سعر الجرد</label>
                        <input type="text" autocomplete="off" wire:model="form.purchase_price"
                               class="form-control @error('form.purchase_price') is-invalid @enderror"
                               placeholder="السعر الجرد ..." id="purchase_price">
                        <div>
                            @error('form.purchase_price') <span
                                class="error text-danger">{{ $message }}</span> @enderror
                        </div>

                        @if($form->id == 0)
                            <label for="stock" class="form-label">الكمية</label>
                            <input type="text" autocomplete="off" wire:model="form.stock"
                                   class="form-control @error('form.stock') is-invalid @enderror"
                                   placeholder="الكمية ..." id="stock">
                            <div>
                                @error('form.stock') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div class="d-grid mt-2">
                            <button
                                @disabled(!Auth::user()->hasPermission('products-create')) class="btn btn- btn-{{$form->id == 0 ? 'primary' : 'success'}}">{{$form->id == 0 ? 'حفــــــــــــــــظ' : 'تعـــــــــــــديل'}}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4">
                            <input wire:keydown="searchProduct()" wire:model.live="search" class="form-control" placeholder="بحث ......">
                        </div>
                        <div class="col-4">
                            <select wire:change="searchProduct()" class="form-select" wire:model.live="store_id">
                                <option value="0">----------------</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->storeName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <select wire:change="searchProduct()" class="form-select" wire:model.live="category_id">
                                <option value="0">----------------</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->categoryName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(!empty($products) && Auth::user()->hasPermission('products-read'))
                        <div class="scroll">
                            <table class="table text-center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>إسم المنتج</th>
                                    <th>الوحده</th>
                                    <th>المخزن</th>
                                    <th>القسم</th>
                                    <th>سعر البيع</th>
                                    <th>سعر الجرد</th>
                                    <th>الكميه</th>
                                    <th>التحكم</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{ $loop->index + 1}}</td>
                                        <td>{{ $product->productName }}</td>
                                        <td>{{ $product->unit }}</td>
                                        <td>{{ $product->storeName }}</td>
                                        <td>{{ $product->categoryName }}</td>
                                        <td>{{ number_format($product->sale_price, 2) }}</td>
                                        <td>{{ number_format($product->purchase_price, 2) }}</td>
                                        <td>{{ number_format($product->stock, 2) }}</td>
                                        <td>
                                            <button
                                                @disabled(!Auth::user()->hasPermission('products-update'))  class="btn btn-sm btn-info text-white"
                                                wire:click="edit({{$product}})">
                                                <i class="bi bi-pen"></i>
                                            </button>
                                            /
                                            <button
                                                @disabled(!Auth::user()->hasPermission('products-delete') || count($product->saleDetails) > 0 || count($product->purchaseDetails) > 0) class="btn btn-sm btn-danger"
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
            </div>
        </div>
    </div>
</div>
