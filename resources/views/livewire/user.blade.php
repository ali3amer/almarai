<div>
    <x-title :$title/>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <label for="name">الإسم</label>
                    <input autocomplete="off"  type="text" id="name" wire:model="name" class="form-control text-center my-2 @error('name') is-invalid @enderror"
                           placeholder="إسم المستخدم ....">
                    <div>
                        @error('name') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <label for="username" class="mt-2">إسم الدخول</label>
                    <input autocomplete="off"  type="text" id="username" wire:model="username" class="form-control text-center my-2 @error('username') is-invalid @enderror"
                           placeholder="إسم الالدخول ....">
                    <div>
                        @error('username') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <label for="password" class="mt-2">كلمة المرور</label>
                        <input autocomplete="off"  type="password" id="password" wire:model="password" class="form-control text-center my-2  @error('password') is-invalid @enderror"
                               placeholder="كلمة المرور ....">
                    @error('password') <span class="error text-danger">{{ $message }}</span> @enderror

                    <table class="table table-responsive table-bordered text-center table-striped my-2">
                        <thead>
                        <tr>
                            <th>الصلاحية</th>
                            <th>عرض</th>
                            <th>إنشاء</th>
                            <th>تعديل</th>
                            <th>حذف</th>
                        </tr>
                        </thead>
                        @foreach($permissionsList as $permission)
                            <tr>
                                <td>{{$permission[1]}}</td>
                                <td><input class="form-check-input" type="checkbox" wire:model="permissions" value="{{$permission[0] . '-read'}}" value="" aria-label="..."></td>
                                <td><input class="form-check-input" type="checkbox" wire:model="permissions" value="{{$permission[0] . '-create'}}" value="" aria-label="..."></td>
                                <td><input class="form-check-input" type="checkbox" wire:model="permissions" value="{{$permission[0] . '-update'}}" value="" aria-label="..."></td>
                                <td><input class="form-check-input" type="checkbox" wire:model="permissions" value="{{$permission[0] . '-delete'}}" value="" aria-label="..."></td>
                            </tr>
                        @endforeach
                    </table>

                    <div class="row">
                        <div class="col-10">
                            <button class="btn btn-{{ $id == 0 ? 'primary' : 'success' }} w-100"
                                   @disabled(!Auth::user()->hasPermission('users-read')) wire:click="save()">{{ $id == 0 ? 'إضـــــــــــافة' : 'تعـــــــــــــديل' }}</button>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-danger w-100" wire:click="resetData()"><i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <input autocomplete="off"  type="text" class="form-control w-50" placeholder="بحث ...." wire:model.live="userSearch">
                </div>
                <div class="card-body">
                    <div class="scroll">
                        <table class="table table-responsive text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>الإسم الكامل</th>
                                <th>إسم المستخدم</th>
                                <th>التحكم</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{$loop->index + 1}}</td>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->username}}</td>
                                    <td>
                                        <button @disabled(!Auth::user()->hasPermission('users-update')) class="btn btn-sm btn-info text-white" wire:click="edit({{$user}})"><i
                                                class="bi bi-pen"></i></button>
                                        /
                                        <button @disabled(!Auth::user()->hasPermission('users-delete')) class="btn btn-sm btn-danger" wire:click="deleteMessage({{$user}})"><i
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
</div>
