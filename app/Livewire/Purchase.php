<?php

namespace App\Livewire;

use App\Models\SaleDebt;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Bank;
use App\Models\PurchaseDebt;
use App\Models\PurchaseDetail;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Purchase extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $listeners = [
        'cancelPurchase',
    ];

    public string $title = 'المشتريات';
    public int $id = 0;
    public int $bank_id = 0;
    public int $debtId = 0;
    public string $purchase_date = '';
    public string $due_date = '';
    public bool $print = false;
    public string $buyer = 'supplier';
    public string $search = '';
    public Collection $purchases;
    public Collection $suppliers;
    public Collection $banks;
    public string $productSearch = '';
    public string $supplierSearch = '';

    public float $total_amount = 0;
    public $paid = 0;
    public string $payment = 'cash';
    public string $bank = '';

    public array $currentSupplier = [];
    public array $oldQuantities = [];
    public array $currentProduct = [];
    public array $cart = [];
    public string $purchaseSearch = '';
    public float $remainder = 0;
    public float $currentBalance = 0;
    public bool $editMode = false;
    public array $currentPurchaseDebts = [];
    public array $currentPurchase = [];
    public Collection $purchaseDebts;
    public array $invoice = [];
    public $discount = 0;
    public $amount = 0;

    public function mount()
    {
        if (\App\Models\Supplier::count() == 0) {
            \App\Models\Supplier::create(['supplierName' => "نقدي", 'phone' => "", 'initialBalance' => 0, 'startingDate' => session("date"), 'initialSalesBalance' => 0, 'blocked' => false, 'cash' => true]);
        }
        if (\App\Models\Supplier::where("cash", true)->first() != null) {
            $this->currentSupplier = \App\Models\Supplier::where("cash", true)->first()->toArray();
        } else {
            $this->currentSupplier = \App\Models\Supplier::first()->toArray();
        }

        $supplier = PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->get();
        $this->currentBalance = $supplier->sum('debt') - $supplier->sum('paid') + $this->currentSupplier['initialBalance'];

        $this->banks = Bank::all();
        if ($this->banks->count() != 0) {
            $this->bank_id = $this->banks->first()->id;
        }

    }

    public function save()
    {
        if (floatval($this->paid) >= 0 && floatval($this->paid) <= floatval(session($this->payment == "cash" ? "safeBalance" : "bankBalance"))) {
            if ($this->id == 0) {
                $purchase = \App\Models\Purchase::create([
                    'supplier_id' => $this->currentSupplier['id'],
                    'paid' => floatval($this->paid),
                    'discount' => floatval($this->discount),
                    'remainder' => $this->remainder,
                    'total_amount' => $this->total_amount,
                    'purchase_date' => $this->purchase_date,
                    'user_id' => auth()->id(),
                ]);

                \App\Models\PurchaseDebt::create([
                    'supplier_id' => $this->currentSupplier['id'],
                    'paid' => 0,
                    'debt' => $this->total_amount,
                    'type' => 'debt',
                    'bank' => $this->bank,
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                    'due_date' => $this->purchase_date,
                    'note' => 'تم الشراء بالآجل بفاتورة #' . $purchase['id'],
                    'purchase_id' => $purchase['id'],
                    'user_id' => auth()->id()
                ]);

                if ($this->paid != 0) {
                    \App\Models\PurchaseDebt::create([
                        'supplier_id' => $this->currentSupplier['id'],
                        'paid' => floatval($this->paid),
                        'debt' => 0,
                        'type' => 'pay',
                        'bank' => $this->bank,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'due_date' => $this->purchase_date,
                        'note' => 'تم دفع مبلغ',
                        'purchase_id' => $purchase['id'],
                        'user_id' => auth()->id()
                    ]);
                }

                $this->currentBalance += $this->remainder;

                foreach ($this->cart as $item) {
                    PurchaseDetail::create([
                        'purchase_id' => $purchase['id'],
                        'product_id' => $item['id'],
                        'quantity' => floatval($item['quantity']),
                        'price' => floatval($item['price']),
                    ]);

//                    \App\Models\Product::where('id', $item['id'])->increment('stock', floatval($item['quantity']));
                }
            }

            $this->showInvoice($purchase['id']);

            $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

            $this->resetData();
        } else {
            $this->confirm("المبلغ المدفوع أكبر من المبلغ المتوفر", [
                'toast' => false,
                'showConfirmButton' => false,
                'confirmButtonText' => 'موافق',
                'onConfirmed' => "cancelSale",
                'showCancelButton' => true,
                'cancelButtonText' => 'إلغاء',
                'confirmButtonColor' => '#dc2626',
                'cancelButtonColor' => '#4b5563'
            ]);
        }

    }

    public function showInvoice($id = null)
    {
        $this->invoice['id'] = $id;
        $this->invoice['type'] = 'purchase';
        $this->invoice['date'] = $this->purchase_date;
        $this->invoice['client'] = $this->currentSupplier[$this->buyer . 'Name'];
        $this->invoice['cart'] = $this->cart;
        $this->invoice['remainder'] = $this->remainder;
        $this->invoice['discount'] = floatval($this->discount);
        $this->invoice['paid'] = floatval($this->paid);
        $this->invoice['amount'] = floatval($this->amount);
        $this->invoice['showMode'] = false;
        $this->invoice['total_amount'] = floatval($this->total_amount);
        $this->dispatch('sale_created', $this->invoice);
    }

    public function chooseSupplier($supplier)
    {
        $this->currentSupplier = $supplier;
        $supplier = PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->get();
        $this->currentBalance = $supplier->sum('debt') - $supplier->sum('paid') + $this->currentSupplier['initialBalance'];
    }

    public function chooseProduct($product)
    {
        $this->currentProduct = $product;
        $this->currentProduct['quantity'] = 1;
        $this->currentProduct['price'] = $product['purchase_price'];
        $this->currentProduct['amount'] = $product['purchase_price'];
        $this->productSearch = '';

    }

    public function calcCurrentProduct()
    {
        $this->currentProduct['amount'] = floatval($this->currentProduct['price']) * floatval($this->currentProduct['quantity']);
    }


    public function addToCart()
    {
        if (!isset($this->cart[$this->currentProduct['id']])) {
            $this->cart[$this->currentProduct['id']] = $this->currentProduct;
            $this->cart[$this->currentProduct['id']]['amount'] = floatval($this->currentProduct['price']) * floatval($this->currentProduct['quantity']);
            $this->amount += $this->cart[$this->currentProduct['id']]['amount'];

        } else {
            $this->amount -= $this->cart[$this->currentProduct['id']]['amount'];
            $this->cart[$this->currentProduct['id']]['quantity'] += floatval($this->currentProduct['quantity']);
            $this->cart[$this->currentProduct['id']]['amount'] = floatval($this->cart[$this->currentProduct['id']]['price']) * floatval($this->cart[$this->currentProduct['id']]['quantity']);
            $this->amount += $this->cart[$this->currentProduct['id']]['amount'];

        }
        $this->currentProduct = [];
        $this->calcRemainder();
    }

    public function deleteFromCart($id)
    {
        $this->amount -= $this->cart[$id]['amount'];

        if ($this->paid) {
            $this->paid = $this->amount;
        }

        unset($this->cart[$id]);
        if (empty($this->cart)) {
            $this->remainder = 0;
            $this->paid = 0;
            $this->discount = 0;
        }
        $this->calcRemainder();

    }

    public function showPurchases()
    {
        $this->editMode = !$this->editMode;
        if(!$this->editMode) {
            $this->id = 0;
            $this->bank = "";
            $this->payment = "cash";
        }
    }

    public function getPurchase($purchase)
    {

        $this->invoice['id'] = $purchase['id'];
        $this->invoice['type'] = 'purchase';
        $this->invoice['clientType'] = 'المورد';
        $this->invoice['date'] = $purchase['purchase_date'];
        $this->invoice['client'] = $this->currentSupplier[$this->buyer . 'Name'];
        $this->invoice['cart'] = PurchaseDetail::where('purchase_id', $purchase['id'])->join('products', 'products.id', '=', 'purchase_details.product_id')->get()->toArray();
        $this->invoice['remainder'] = floatval($purchase['remainder']);
        $this->invoice['paid'] = floatval($purchase['paid']);
        $this->invoice['discount'] = floatval($purchase['discount']);
        $this->invoice['amount'] = floatval($purchase['total_amount']) + floatval($purchase['discount']);
        $this->invoice['total_amount'] = $purchase['total_amount'];
        $this->invoice['showMode'] = false;

        $paid = PurchaseDebt::where("purchase_id", $purchase["id"])->where("type", "pay")->first();

        if ($paid) {
            $this->payment = $paid['payment'];
            $this->bank = $paid['bank'];
            $this->invoice['paidId'] = $paid['id'];
        }

        $this->dispatch('sale_created', $this->invoice);
    }

    public function changePayment($id)
    {
        PurchaseDebt::where("id", $id)->update([
            'payment' => $this->payment,
            'bank' => $this->payment == "bank" ? $this->bank : ""
        ]);

        $this->bank = $this->payment == "bank" ? $this->bank : "";

        $this->alert('success', 'تم تعديل وسيلة الدفع بنجاح', ['timerProgressBar' => true]);

    }

    public function deleteMessage($id)
    {
        $this->confirm("  هل توافق على إلغاء الفاتورة ؟", [
            'inputAttributes' => ["id" => $id],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "cancelPurchase",
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }

    public function cancelPurchase($data)
    {
        $id = $data['inputAttributes']['id'];
        $items = PurchaseDetail::where('purchase_id', $id)->get();
        foreach ($items as $item) {
//            \App\Models\Product::where('id', $item['product_id'])->decrement('stock', floatval(floatval($item['quantity'])));
            \App\Models\PurchaseDetail::where('id', $item['id'])->delete();
        }

        PurchaseDetail::where("purchase_id", $id)->delete();;
        \App\Models\Purchase::where('id', $id)->delete();

        PurchaseDebt::where("purchase_id", $id)->where("type", "debt")->delete();

        $paid = PurchaseDebt::where("purchase_id", $id)->where("type", "pay")->first();


        \App\Models\PurchaseDebt::create([
            'supplier_id' => $this->currentSupplier['id'],
            'paid' => $this->invoice['total_amount'],
            'debt' => 0,
            'type' => 'pay',
            'bank' => '',
            'payment' => 'cash',
            'bank_id' => null,
            'due_date' => $this->purchase_date,
            'note' => 'تم إلغاء فاتوره مشتريات رقم #' . $this->invoice['id'],
            'purchase_id' => $this->invoice['id'],
            'user_id' => auth()->id()
        ])->delete();

        if ($paid) {
            \App\Models\SaleDebt::create([
                'supplier_id' => $this->currentSupplier['id'],
                'paid' => 0,
                'debt' => $paid->paid,
                'type' => 'debt',
                'bank' => '',
                'payment' => 'cash',
                'bank_id' => null,
                'due_date' => $this->purchase_date,
                'note' => 'تم إلغاء  مدفوعات فاتوره مشتريات رقم #' . $this->invoice['id'],
                'sale_id' => $this->invoice['id'],
                'user_id' => auth()->id()
            ])->delete();
            $paid->delete();
        }

        $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);


    }

    public function calcRemainder()
    {
        $this->total_amount = $this->amount - floatval($this->discount);
        if ($this->currentSupplier['cash']) {
            $this->paid = $this->total_amount;
        } else {
            $this->remainder = floatval($this->total_amount) - floatval($this->paid);
        }
    }

    public function resetData($item = null)
    {

        $item == "currentSupplier" ? $this->reset( 'search', 'supplierSearch', 'id', 'oldQuantities', $item) : $this->reset('currentProduct', 'cart', 'bank', 'search', 'supplierSearch', 'discount', 'amount', 'paid', 'remainder', 'total_amount', 'id', 'oldQuantities', $item);
    }

    public function render()
    {

        if ($this->payment == "bank" && $this->bank_id == null) {
            if ($this->banks->count() != 0) {
                $this->bank_id = $this->banks->first()->id;
            }
        }

        if (!empty($this->currentSupplier)) {
            $this->purchases = \App\Models\Purchase::where('supplier_id', $this->currentSupplier['id'])
                ->where('id', 'LIKE', '%' . $this->purchaseSearch . '%')->where('purchase_date', 'LIKE', '%' . $this->purchaseSearch . '%')->get();
        }
        if ($this->purchase_date == '') {
            $this->purchase_date = session("date");
        }
        $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();

        return view('livewire.purchase', [
            'products' => \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->simplePaginate(10)
        ]);
    }
}
