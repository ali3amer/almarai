<div class="invoice" dir="rtl">

    <h6>فاتوره رقم {{$invoice['id'] ?? ''}}</h6>
    <h6>إسم العميل : {{$invoice['client'] ?? ''}}</h6>
    <h6>التاريخ : {{$invoice['sale_date'] ?? ''}}</h6>
    <table class="printInvoice text-center">
        <thead>
        <tr>
            <th>#</th>
            <th>اسم المنتج</th>
            <th>سعر الوحدة</th>
            <th>الكمية</th>
            <th>المجموع</th>
        </tr>
        </thead>
        <tbody>
        @if(isset($invoice['cart']))
            @foreach($invoice['cart'] as $item)
                <tr style="cursor: pointer" class="align-items-center">
                    <td scope="row">{{$loop->index + 1}}</td>
                    <td>{{$item['productName']}}</td>
                    <td>{{number_format(floatval($item['sale_price']), 2)}}</td>
                    <td>{{number_format(floatval($item['quantity']), 2)}}</td>
                    <td>{{number_format($item['amount'], 2)}}</td>
                </tr>
            @endforeach
        @endif

        </tbody>
        <tfoot>
        <tr>
            <td colspan="4">المجموع الكلي</td>
            <td>{{isset($invoice['total_amount']) ? number_format($invoice['total_amount'], 2) : ''}}</td>
        </tr>
        </tfoot>
    </table>
</div>
