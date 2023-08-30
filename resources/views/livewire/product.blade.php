<div>
    <x-title :$title ></x-title>

    <div class="row mt-2">

        <div class="col-4">
            <div class="card bg-white">
                <div class="card-body">
                    <form action="" wire:submit="save({{ $id }})">
                        <label for="productName" class="form-label">إسم المنتج</label>
                        <input type="text" wire:model="productName" class="form-control" placeholder="إسم المنتج ..."  id="productName">
                        <div>
                            @error('productName') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>


                        <label for="store_id" class="form-label">المخزن</label>
                        <select wire:model="store_id" class="form-select">
                            <option value="">------------------</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" @selected($store->id == $store_id)>{{ $store->name }}</option>
                            @endforeach
                        </select>

                        <label for="category_id" class="form-label">القسم</label>
                        <select wire:model="category_id" class="form-select">
                            <option value="">------------------</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected($category->id == $category_id)>{{ $category->name }}</option>
                            @endforeach
                        </select>

                        <label for="sale_price" class="form-label">السعر</label>
                        <input type="text" wire:model="sale_price" class="form-control" placeholder="السعر ..." name="sale_price" id="productName">

                        <div class="d-grid mt-2">
                            <button class="btn btn- btn-primary">حفــــــــــــــــظ</button>
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
                            <select class="form-select" wire:model="store_id" wire:change="searchProduct()">
                                <option value="0">----------------</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <select class="form-select" wire:model="category_id" wire:change="searchProduct()">
                                <option value="0">----------------</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(!empty($products))
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
                                    <td>{{ $product->store->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ number_format($product->sale_price, 2) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white" wire:click="edit({{$product}})">تعديل</button> /
                                        <button class="btn btn-sm btn-danger" wire:click="delete({{$product->id}})">حذف</button>
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
