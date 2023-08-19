<div>
    <x-title :$title ></x-title>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card bg-white">
                <div class="card-body">
                    <form action="" wire:submit="save({{ $id }})">
                        <label for="name" class="form-label">إسم المنتج</label>
                        <input type="text" wire:model="name" class="form-control" placeholder="إسم المنتج ..." name="name" id="name">
                        <div>
                            @error('name') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <label for="category_id" class="form-label">القسم</label>
                        <select wire:model="category_id" class="form-select">
                            <option value="">------------------</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected($category->id == $category_id)>{{ $category->name }}</option>
                            @endforeach
                        </select>

                        <label for="price" class="form-label">السعر</label>
                        <input type="text" wire:model="price" class="form-control" placeholder="السعر ..." name="price" id="name">

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
                    <input wire:model.live="search" class="form-control w-50" placeholder="بحث ......">
                </div>
                <div class="card-body">
                    @if(count($products) > 0)
                        <table class="table table-bordered text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>إسم المنتج</th>
                                <th>القسم</th>
                                <th>السعر</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody class="text-white">
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $loop->index + 1}}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ number_format($product->price, 2) }}</td>
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
