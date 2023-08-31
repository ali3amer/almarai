<div>
    <x-title :$title></x-title>

    <div class="row mt-2">

        <div class="col-4">
            <div class="card bg-white">
                <div class="card-body">
                    <x-alert />
                    <form action="" wire:submit="save({{ $form->id }})">
                        <label for="productName" class="form-label">إسم المنتج</label>
                        <input type="text" wire:model="form.productName" class="form-control"
                               placeholder="إسم المنتج ..." id="productName">
                        <div>
                            @error('form.productName') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <label for="store_id" class="form-label">المخزن</label>
                        <select wire:model="form.store_id" class="form-select">
                            <option value=0>------------------</option>
                            @foreach($stores as $store)
                                <option
                                    value="{{ $store->id }}" @selected($store->id == $form->store_id)>{{ $store->storeName }}</option>
                            @endforeach
                        </select>
                        <div>
                            @error('form.store_id') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>

                        <label for="category_id" class="form-label">القسم</label>
                        <select wire:model="form.category_id" class="form-select">
                            <option value=0>------------------</option>
                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}" @selected($category->id == $form->category_id)>{{ $category->categoryName }}</option>
                            @endforeach
                        </select>
                        <div>
                            @error('form.category_id') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>

                        <label for="sale_price" class="form-label">السعر</label>
                        <input type="text" wire:model="form.sale_price" class="form-control" placeholder="السعر ..." id="productName">
                        <div>
                            @error('form.sale_price') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="d-grid mt-2">
                            <button wire:loading.attr="disabled" class="btn btn- btn-primary">حفــــــــــــــــظ</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-8">
            <div class="card bg-white">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4">
                            <input wire:model.live="search" class="form-control" placeholder="بحث ......">
                        </div>
                        <div class="col-4">
                            <select class="form-select" wire:model.live="store_id">
                                <option value="0">----------------</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->storeName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <select class="form-select" wire:model.live="category_id">
                                <option value="0">----------------</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->categoryName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body overflow-scroll" style="max-height: 350px">
                    @if(count($products) > 0)
                        <table class="table table-bordered text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>إسم المنتج</th>
                                <th>المخزن</th>
                                <th>القسم</th>
                                <th>السعر</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody class="text-white">
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $loop->index + 1}}</td>
                                    <td>{{ $product->productName }}</td>
                                    <td>{{ $product->storeName }}</td>
                                    <td>{{ $product->categoryName }}</td>
                                    <td>{{ number_format($product->sale_price, 2) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white" wire:click="edit({{$product}})">
                                            تعديل
                                        </button>
                                        /
                                        <button class="btn btn-sm btn-danger" wire:click="delete({{$product->id}})">
                                            حذف
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-danger text-center">لا يوجد منتجات .....</div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
