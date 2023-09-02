<div class="my-2">
    <x-alert />
</div>
<div class="card bg-white mt-3 ">
    <div class="card-body p-2">
        @if($title != 'المبيعات')
            <h3 class="text-center m-0 p-0">{{ $title }}</h3>
        @else
            <div class="row">
                <div class="col-4 align-self-center"><h4>{{$title}}</h4></div>
                <div class="col-4">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"
                            style="cursor: pointer"><i class="bi bi-plus-square"></i></button>
                    <button class="btn btn-warning"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-success" wire:click="save()" ><i class="bi bi-bookmark-check"></i></button>

                </div>
                <div class="col-4 align-self-center"><h4>{{$slot}}</h4></div>

            </div>
        @endif

    </div>

</div>

