<div class="invoice mb-3 pb-3" dir="rtl">
    <h6 class="d-none d-print-block text-center py-2" style="background-color: #4a5568; color: #fff">{{ $settings ? $settings->name : "POS" }}</h6>
    <h6>فاتوره رقم {{$invoice['id'] ?? ''}}</h6>
    <h6>إسم {{ $invoice['clientType'] ?? '' }} : {{$invoice['client'] ?? ''}}</h6>
    <h6>التاريخ : {{$invoice['date'] ?? ''}}</h6>

    <table class="printInvoice text-center">
        <thead>
        <tr>
            <th>#</th>
            <th>اسم المنتج</th>
            <th>سعر الوحدة</th>
            <th>الكمية</th>
            <th>المجموع</th>
            @if($settings)
                @if($settings->expired_date)
                    <th>تاريخ الانتهاء</th>
                @endif
            @endif
        </tr>
        </thead>
        <tbody>
        @if(isset($invoice['cart']))
            @foreach($invoice['cart'] as $item)
                <tr style="cursor: pointer" class="align-items-center">
                    <td scope="row">{{$loop->index + 1}}</td>
                    <td>{{$item['productName']}}</td>
                    <td>{{number_format(floatval($item['price']), 2)}}</td>
                    <td>{{number_format(floatval($item['quantity']), 2)}}</td>
                    <td>{{number_format(floatval($item['price']) * floatval($item['quantity']), 2)}}</td>
                    @if($settings)
                        @if($settings->expired_date)
                            <td>{{ $item['expired_date'] }}</td>
                        @endif
                    @endif
                </tr>
            @endforeach
        @endif

        </tbody>
        <tfoot>
        @if(isset($invoice['showMode']) && !$invoice['showMode'])
        <tr>
            <td colspan="4">المجموع الكلي</td>
            <td>{{isset($invoice['amount']) ? number_format($invoice['amount'], 2) : ''}}</td>
        </tr>
        <tr>
            <td colspan="4">التخفيض</td>
            <td>{{isset($invoice['discount']) ? number_format($invoice['discount'], 2) : ''}}</td>
        </tr>
        @endif
        <tr>
            <td colspan="4">الصافي</td>
            <td>{{isset($invoice['total_amount']) ? number_format($invoice['total_amount'], 2) : ''}}</td>
        </tr>
        @if(isset($invoice['showMode']) && !$invoice['showMode'])
            <tr>
                <td colspan="4">المدفوع</td>
                <td>{{isset($invoice['paid']) ? number_format($invoice['paid'], 2) : ''}}</td>
            </tr>
            <tr>
                <td colspan="4">المتبقي</td>
                <td>{{isset($invoice['remainder']) ? number_format($invoice['remainder'], 2) : ''}}</td>
            </tr>
        @endif
        </tfoot>
    </table>
</div>
