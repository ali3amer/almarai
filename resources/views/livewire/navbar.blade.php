<?php
$links = [
    ['category', 'الاقسام'],
    ['product', 'المنتجات'],
    ['supplier', 'الموردين'],
    ['client', 'العملاء'],
    ['purchase', 'المشتريات'],
    ['sale', 'المبيعات'],
];

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">المراعي</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            @foreach($links as $link)
                <li class="nav-item">
                    <a wire:navigate class="nav-link" href="{{ $link[0] }}">
                        {{ $link[1] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</nav>
