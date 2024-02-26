<div>
    <x-title :$title/>

    <div class="card bg-white mt-2">
        <div class="card-body">
            <form action="" wire:submit="save()">
                <label for="name" class="form-label">إسم النظام</label>
                <input type="text" autocomplete="off" wire:model="name" class="form-control"
                       placeholder="البيان ..."
                       name="name" id="name">
                <div>
                    @error('name') <span class="error text-danger">{{ $message }}</span> @enderror
                </div>

                <label for="initialBalance" class="form-label">الرصيد الافتتاحي للخزنه</label>
                <input type="text" autocomplete="off" wire:model="initialBalance" class="form-control"
                       placeholder="الرصيد الافتتاحي للخزنه ..."
                       name="initialBalance" id="initialBalance">
                <div>
                    @error('initialBalance') <span class="error text-danger">{{ $message }}</span> @enderror
                </div>

                <label for="capital" class="form-label">رأس المال</label>
                <input type="text" autocomplete="off" wire:model="capital" class="form-control"
                       placeholder="رأس المال ..."
                       name="capital" id="capital">
                <div>
                    @error('capital') <span class="error text-danger">{{ $message }}</span> @enderror
                </div>

                <label for="logo" class="form-label">لوقو</label>
                <input type="file" autocomplete="off" wire:model="logo" class="form-control"
                       name="logo" id="logo">
                <div>
                    @error('logo') <span class="error text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="my-2">
                    <label for="barcode" class="form-check-label">الباركود</label>
                    <input type="checkbox" wire:model.live="barcode" id="barcode" class="form-check-input">
                    <div>
                        @error('barcode') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="my-0">
                    <label for="batch" class="form-check-label">الباتش</label>
                    <input type="checkbox" wire:model.live="batch" id="batch" class="form-check-input">
                    <div>
                        @error('batch') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                </div>

                <div class="my-2">
                    <label for="expired_date" class="form-check-label">تاريخ الإنتهاء</label>

                    <input type="checkbox" wire:model.live="expired_date" id="expired_date" class="form-check-input">
                    <div>
                        @error('expired_date') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                </div>

                <div class="d-grid mt-2">
                    <button
                        class="btn btn- btn-primary">حفـــــــــــــــــــظ
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
