<!-- Modal -->
<div wire:ignore.self class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h1 class="modal-title fs-5" id="exampleModalLabel">العملاء</h1>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-4 align-self-center"><h5>العملاء</h5></div>
                                <div class="col-6"><input type="text" placeholder="بحث ..." class="form-control"
                                                          wire:model.live="clientSearch"></div>
                            </div>
                        </div>
                        <table class="table table-responsive">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">إسم العميل</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clients as $client)
                                <tr style="cursor: pointer" wire:click="chooseClient({{$client}})"
                                    data-bs-dismiss="modal"
                                    aria-label="Close">
                                    <td scope="row">{{$loop->index + 1}}</td>
                                    <td>{{$client->clientName}}</td>
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

