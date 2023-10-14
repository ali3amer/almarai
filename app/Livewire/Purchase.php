<?php

namespace App\Livewire;

use App\Models\SaleDetail;
use App\Models\SupplierDebt;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Bank;
use App\Models\PurchaseDebt;
use App\Models\PurchaseDetail;
use Cassandra\Date;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Purchase extends Component
{
    use LivewireAlert;

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
    public Collection $products;
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

    public function mount()
    {
        $this->currentSupplier = \App\Models\Supplier::find(1)->toArray();
        $this->banks = Bank::all();
        $supplier = SupplierDebt::where('supplier_id', $this->currentSupplier['id'])->get();
        $this->currentBalance = $supplier->sum('debt') - $supplier->sum('paid');
    }

    public function save()
    {
        if ($this->id == 0) {
            $purchase = \App\Models\Purchase::create([
                'supplier_id' => $this->currentSupplier['id'],
                'total_amount' => $this->total_amount,
                'purchase_date' => $this->purchase_date,
                'user_id' => auth()->id(),
            ]);

            $type = $this->paid == 0 ? 'debt' : 'pay';


            \App\Models\SupplierDebt::create([
                'supplier_id' => $this->currentSupplier['id'],
                'paid' => 0,
                'debt' => $this->remainder,
                'type' => $type,
                'bank' => $this->bank,
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'due_date' => $this->purchase_date,
                'purchase_id' => $purchase['id'],
                'note' => 'تم الإستيراد بالآجل',
                'user_id' => auth()->id()
            ]);

            $this->currentBalance += $this->total_amount;

            if ($this->paid != 0) {
                \App\Models\SupplierDebt::create([
                    'supplier_id' => $this->currentSupplier['id'],
                    'paid' => $this->paid,
                    'debt' => 0,
                    'type' => $type,
                    'bank' => $this->bank,
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                    'due_date' => $this->purchase_date,
                    'note' => 'تم دفع مبلغ',
                    'user_id' => auth()->id()
                ]);

                $this->currentBalance -= floatval($this->paid);
            }


            foreach ($this->cart as $item) {
                PurchaseDetail::create([
                    'purchase_id' => $purchase['id'],
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                \App\Models\Product::where('id', $item['id'])->increment('stock', $item['quantity']);
            }
        }




        $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

        $this->invoice['id'] = $purchase['id'];
        $this->invoice['type'] = 'purchase';
        $this->invoice['purchase_date'] = $purchase['purchase_date'];
        $this->invoice['client'] = $this->currentSupplier[$this->buyer . 'Name'];
        $this->invoice['cart'] = $this->cart;
        $this->invoice['remainder'] = $this->remainder;
        $this->invoice['paid'] = $this->paid;
        $this->invoice['showMode'] = false;
        $this->invoice['total_amount'] = $this->total_amount;
        $this->dispatch('sale_created', $this->invoice);
        $this->resetData();

    }

    public function chooseSupplier($supplier)
    {
        $this->currentSupplier = $supplier;
        $supplier = SupplierDebt::where('supplier_id', $this->currentSupplier['id'])->get();
        $this->currentBalance = $supplier->sum('debt') - $supplier->sum('paid');
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
        $this->cart[$this->currentProduct['id']] = $this->currentProduct;
        $this->cart[$this->currentProduct['id']]['amount'] = floatval($this->currentProduct['price']) * floatval($this->currentProduct['quantity']);
        $this->total_amount += $this->cart[$this->currentProduct['id']]['amount'];
        $this->currentProduct = [];
        $this->calcRemainder();
        $this->dispatch('productSearchFocus');
    }

    public function deleteFromCart($id)
    {
        $this->total_amount -= $this->cart[$id]['amount'];
        $this->paid = $this->total_amount;

        $this->calcRemainder();
        unset($this->cart[$id]);
        if (empty($this->cart)) {
            $this->remainder = 0;
            $this->paid = 0;
        }
    }

    public function showPurchases()
    {
        $this->editMode = !$this->editMode;
    }

    public function getPurchase($purchase)
    {
        $this->invoice['id'] = $purchase['id'];
        $this->invoice['type'] = 'purchase';
        $this->invoice['clientType'] = 'المورد';
        $this->invoice['date'] = $purchase['purchase_date'];
        $this->invoice['client'] = $this->currentSupplier[$this->buyer . 'Name'];
        $this->invoice['cart'] = PurchaseDetail::where('purchase_id', $purchase['id'])->join('products', 'products.id', '=', 'purchase_details.product_id')->get()->toArray();
        $this->invoice['remainder'] = $this->remainder;
        $this->invoice['paid'] = $this->paid;
        $this->invoice['total_amount'] = $purchase['total_amount'];
        $this->invoice['showMode'] = true;
        $this->dispatch('sale_created', $this->invoice);
    }

    public function deleteMessage($id)
    {
        $this->confirm("  هل توافق على الحذف ؟", [
            'inputAttributes' => ["id"=>$id],
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
        \App\Models\Purchase::where('id', $id)->delete();
        $items = PurchaseDetail::where('purchase_id', $id)->get();
        foreach ($items as $item) {
            \App\Models\Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            \App\Models\PurchaseDetail::where('id', $item['id'])->delete();
        }

        \App\Models\SupplierDebt::create([
            'supplier_id' => $this->currentSupplier['id'],
            'paid' => 0,
            'debt' => $this->invoice['total_amount'],
            'type' => 'debt',
            'bank' => '',
            'payment' => 'cash',
            'bank_id' => null,
            'due_date' => $this->purchase_date,
            'purchase_id' => $this->invoice['id'],
            'note' => $this->invoice['id'] . '#تم إلغاء الفاتوره رقم ',
            'user_id' => auth()->id()
        ]);

    }

    public function calcRemainder()
    {
        $this->remainder = $this->total_amount - floatval($this->paid);
    }

    public function resetData($item = null)
    {
        $this->reset('currentProduct', 'cart', 'search', 'supplierSearch', 'paid', 'remainder', 'total_amount', 'id', 'oldQuantities', $item);
    }

    public function render()
    {


        if (!empty($this->currentSupplier)) {
            $this->purchases = \App\Models\Purchase::where('supplier_id', $this->currentSupplier['id'])
                ->where('id', 'LIKE', '%' . $this->purchaseSearch . '%')->where('purchase_date', 'LIKE', '%' . $this->purchaseSearch . '%')->get();
        }
        if ($this->purchase_date == '') {
            $this->purchase_date = date('Y-m-d h:m:i');
        }
        $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();

        $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->get();
        return view('livewire.purchase');
    }
}
