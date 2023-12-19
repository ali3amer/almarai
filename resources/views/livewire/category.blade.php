<div>
        <x-title :$title/>
{{--    <livewire:Title :$title />--}}

    <div class="row mt-2">
        <div class="col-4">
            <div class="card bg-white">
                <div class="card-body">
                    <form action="" wire:submit="save({{ $id }})">
                        <label for="categoryName" class="form-label">إسم القسم</label>
                        <input type="text" wire:model="categoryName" autocomplete="off" class="form-control @error('categoryName') is-invalid @enderror" placeholder="إسم القسم ..." id="categoryName">
                        <div>
                            @error('categoryName') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="d-grid mt-2">
                            <button @disabled(!$create) class="btn btn btn-{{$id == 0 ? 'primary' : 'success' }}">{{$id == 0 ? 'حفـــــــــــــــــــظ' : 'تعـــــــــديل' }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <input wire:model.live="search" class="form-control w-50" placeholder="بحث ......">
                </div>

                <div class="card-body">
                    @if(count($categories) > 0 && $read)
                        <div class="scroll">
                            <table class="table text-center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>إسم القسم</th>
                                    <th>التحكم</th>
                                </tr>
                                </thead>
                                <tbody class="text-white">
                                @foreach($categories as $category)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $category->categoryName }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info text-white" @disabled(!$update) wire:click="edit({{$category}})"><i class="bi bi-pen"></i></button> /
                                            <button class="btn btn-sm btn-danger" @disabled(!$delete || count($category->products) > 0) wire:click="deleteMessage({{$category}})"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-danger text-center">لايوجد أقسام ....</div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
