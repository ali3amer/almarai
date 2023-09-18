<div>
    <x-title :$title/>

    <div class="row mt-2">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <label for="name">الإسم</label>
                    <input type="text" id="name" wire:model="name" class="form-control text-center my-2"
                           placeholder="إسم المستخدم ....">
                    <label for="username" class="mt-2">إسم الدخول</label>
                    <input type="text" id="username" wire:model="username" class="form-control text-center my-2"
                           placeholder="إسم الالدخول ....">
                    <label for="password" class="mt-2">كلمة المرور</label>
                    <input type="password" id="password" wire:model="password" class="form-control text-center my-2"
                           placeholder="كلمة المرور ....">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs my-3">
                        @foreach($tabPermissions as $index => $permission)
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="{{'#'.$index}}">{{$index}}</a>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Tab panes -->
                    <div wire:ignore.self class="tab-content mb-3">
                        @foreach($tabPermissions as $index => $permission)
                            <div class="tab-pane container" id="{{$index}}">

                                @foreach($permission as $role)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" value="{{$index .'-'. $role}}" wire:model="permissions" id="{{$role}}">
                                        <label class="form-check-label" for="{{$role}}">{{$role}}</label>
                                    </div>
                                @endforeach

                            </div>
                        @endforeach
                    </div>


                    <div class="row">
                        <div class="col-10">
                            <button class="btn btn-{{ $id == 0 ? 'primary' : 'success' }} w-100"
                                    wire:click="save()">{{ $id == 0 ? 'إضـــــــــــافة' : 'تعـــــــــــــديل' }}</button>
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
                    <input type="text" class="form-control w-50" placeholder="بحث ...." wire:model.live="userSearch">
                </div>
                <div class="card-body">
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
                                    <button class="btn btn-sm btn-info text-white" wire:click="edit({{$user}})"><i
                                            class="bi bi-pen"></i></button>
                                    /
                                    <button class="btn btn-sm btn-danger" wire:click="delete({{$user->id}})"><i
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
