<div>
    <x-title :$title ></x-title>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card bg-white">
                <div class="card-body">
                    <form action="" wire:submit="save({{ $id }})">
                        <label for="employeeName" class="form-label">إسم الموظف</label>
                        <input type="text" wire:model="employeeName" class="form-control" placeholder="إسم الموظف ..." id="employeeName">
                        <div>
                            @error('employeeName') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <label for="employeeName" class="form-label">المرتب</label>
                        <input type="text" wire:model="salary" class="form-control" placeholder="المرتب" id="salary">
                        <div>
                            @error('salary') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="d-grid mt-2">
                            <button class="btn btn- btn-primary">حفـــــــــــــــــــظ</button>
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
                    @if(count($employees) > 0)
                        <table class="table table-bordered text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>إسم الموظف</th>
                                <th>المرتب</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody class="text-white">
                            @foreach($employees as $category)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $category->employeeName }}</td>
                                    <td>{{ number_format($category->salary, 2) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white" wire:click="edit({{$category}})">Edit</button> /
                                        <button class="btn btn-sm btn-danger" wire:click="delete({{$category->id}})">delete</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-danger text-center">لايوجد موظفين ....</div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
