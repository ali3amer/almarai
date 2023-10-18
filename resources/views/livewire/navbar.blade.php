<?php
$links = [
    ['store', 'المخازن'],
    ['category', 'الاقسام'],
    ['product', 'المنتجات'],
    ['supplier', 'الموردين'],
    ['client', 'العملاء'],
    ['purchase', 'المشتريات'],
    ['sale', 'المبيعات'],
    ['employee', 'الموظفين'],
    ['expense', 'المصروفات'],
    ['report', 'التقارير'],
    ['returns', 'المرتجعات'],
    ['purchase-returns', 'مرتجعات المشتريات'],
    ['safe', 'الخزنه'],
//    ['debt', 'الديون'],
//    ['claim', 'المطالبات'],
    ['damaged', 'التالف'],
    ['user', 'المستخدمين'],
];

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand visually-hidden" href="#">المراعي</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            @foreach($links as $link)
                @php
                if ($link[0] == 'category') {
                    $permission = 'categories';
                } elseif ($link[0] == 'purchase-returns') {
                    $permission = 'purchase-returns';
                } elseif ($link[0] == 'returns') {
                    $permission = 'returns';
                } else {
                    $permission = $link[0].'s';
                }
                @endphp
                @if(Auth::user()->hasPermission($permission.'-read'))
                    <li class="nav-item">
                        <a wire:navigate class="nav-link {{request()->path() == $link[0] ? 'active-link' : ''}}"
                           href="{{ $link[0] }}">
                            {{ $link[1] }}
                        </a>
                    </li>
                @endif
            @endforeach
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-danger">
                        <i class="bi bi-door-closed"></i> {{auth()->user()->name}}
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>

