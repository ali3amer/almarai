<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\PurchaseDebt;
use App\Models\PurchaseDetail;
use Cassandra\Date;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Purchase extends Component
{

    public string $title = 'المشتريات';
    public int $id = 0;
    public int $bank_id = 1;
    public int $debtId = 0;
    public string $purchase_date = '';
    public string $due_date = '';
    public bool $print = false;

    public string $search = '';
    public Collection $purchases;
    public Collection $banks;
    public Collection $suppliers;
    public Collection $products;
    public string $productSearch = '';
    public string $supplierSearch = '';

    public float $total_amount = 0;
    public float $currentBalance = 0;
    public $paid = 0;
    public string $payment = 'cash';
    public string $bank = '';

    public array $currentSupplier = [];
    public array $oldQuantities = [];
    public array $currentProduct = [];
    public array $cart = [];
    public string $purchaseSearch = '';
    public float $remainder = 0;
    public bool $editMode = false;
    public array $currentPurchaseDebts = [];
    public array $currentPurchase = [];

    public Collection $purchaseDebts;
    public array $invoice;

    public function mount()
    {
        $this->currentSupplier = \App\Models\Supplier::find(1)->toArray();
        $this->banks = Bank::all();
        $this->currentBalance = $this->currentSupplier['currentBalance'];
    }

    public function save()
    {
        if ($this->id == 0) {
            $purchase = \App\Models\Purchase::create([
                'supplier_id' => $this->currentSupplier['id'],
                'total_amount' => $this->total_amount,
                'purchase_date' => $this->purchase_date,
                'user_id' => auth()->id()
            ]);

            $this->currentSupplier['currentBalance'] += $this->total_amount;

            PurchaseDebt::create([
                'purchase_id' => $purchase['id'],
                'paid' => 0,
                'bank' => '',
                'payment' => 'cash',
                'remainder' => $this->total_amount,
                'supplier_balance' => $this->currentSupplier['currentBalance'],
                'due_date' => $this->purchase_date,
                'user_id' => auth()->id()
            ]);

            \App\Models\Supplier::where('id', $this->currentSupplier['id'])->increment('currentBalance', $this->total_amount);

            if ($this->paid != 0) {
                $this->currentSupplier['currentBalance'] -= $this->paid;
                PurchaseDebt::create([
                    'purchase_id' => $purchase['id'],
                    'paid' => $this->paid,
                    'bank' => $this->bank,
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                    'remainder' => $this->remainder,
                    'supplier_balance' => $this->currentSupplier['currentBalance'],
                    'due_date' => $this->purchase_date,
                    'user_id' => auth()->id()
                ]);

                if ($this->payment == 'cash') {
                    \App\Models\Safe::first()->increment('currentBalance', $this->paid);
                } else {
                    Bank::where('id', $this->bank_id)->first()->increment('currentBalance', $this->paid);
                }
                \App\Models\Supplier::where('id', $this->currentSupplier['id'])->decrement('currentBalance', $this->paid);
            }

            foreach ($this->cart as $item) {
                PurchaseDetail::create([
                    'purchase_id' => $purchase['id'],
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['purchase_price'],
                ]);

                \App\Models\Product::where('id', $item['id'])->increment('stock', $item['quantity']);
            }

            $this->id = $purchase['id'];

            session()->flash('success', 'تم الحفظ بنجاح');

        } else {
            $purchase = \App\Models\Purchase::where('id', $this->id)->first();
            $this->currentSupplier['currentBalance'] -= $purchase['total_amount'];
            \App\Models\Supplier::where('id', $this->currentSupplier['id'])->decrement('currentBalance', $purchase['total_amount']);
            \App\Models\Purchase::where('id', $this->id)->update([
                'total_amount' => $this->total_amount,
                'purchase_date' => $this->purchase_date,
                'user_id' => auth()->id()
            ]);
            \App\Models\Supplier::where('id', $this->currentSupplier['id'])->increment('currentBalance', $this->total_amount);
            $this->currentSupplier['currentBalance'] += $this->total_amount;

            $debt = PurchaseDebt::where('purchase_id', $this->id)->first();
            if ($debt['payment'] == 'cash') {
                \App\Models\Safe::first()->decrement('currentBalance', $debt['paid']);
            } else {
                Bank::where('id', $debt['bank_id'])->decrement('currentBalance', $debt['paid']);
            }
            $debt->update([
                'purchase_id' => $this->id,
                'paid' => $this->paid,
                'bank' => $this->bank,
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'remainder' => $this->remainder,
                'supplier_balance' => $this->currentSupplier['currentBalance'],
                'due_date' => $this->purchase_date,
                'user_id' => auth()->id()
            ]);

            if ($this->payment == 'cash') {
                \App\Models\Safe::first()->increment('currentBalance', $this->paid);
            } else {
                Bank::where('id', $this->bank_id)->increment('currentBalance', $this->paid);
            }

            PurchaseDetail::where('purchase_id', $this->id)->delete();

            foreach ($this->oldQuantities as $key => $quantity) {
                \App\Models\Product::where('id', $key)->decrement('stock', $quantity);
            }

            foreach ($this->cart as $item) {
                PurchaseDetail::create([
                    'purchase_id' => $this->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['purchase_price'],
                ]);
                \App\Models\Product::where('id', $item['id'])->increment('stock', $item['quantity']);
            }
            session()->flash('success', 'تم التعديل بنجاح');

        }

        $this->invoice['id'] = $purchase['id'];
        $this->invoice['sale_date'] = $purchase['purchase_date'];
        $this->invoice['client'] = $this->currentSupplier['supplierName'];
        $this->invoice['cart'] = $this->cart;
        $this->invoice['total_amount'] = $this->total_amount;
        $this->dispatch('sale_created', $this->invoice);

        $this->resetData();

    }

    public function printInvoice($print)
    {
    }

    public function edit($purchase)
    {

    }

    public function delete($id)
    {

    }

    public function chooseSupplier($supplier)
    {
        $this->currentSupplier = [];
        $this->currentSupplier = $supplier;
        $this->currentBalance = $this->currentSupplier['currentBalance'];
    }

    public function chooseProduct($product)
    {
        $this->currentProduct = $product;
        $this->currentProduct['quantity'] = 1;
        $this->currentProduct['amount'] = $product['purchase_price'];
        $this->productSearch = '';
    }

    public function calcCurrentProduct()
    {

        $this->currentProduct['amount'] = floatval($this->currentProduct['purchase_price']) * floatval($this->currentProduct['quantity']);
    }


    public function addToCart()
    {
        $this->cart[$this->currentProduct['id']] = $this->currentProduct;
        $this->cart[$this->currentProduct['id']]['amount'] = floatval($this->currentProduct['purchase_price']) * floatval($this->currentProduct['quantity']);
        $this->total_amount += $this->cart[$this->currentProduct['id']]['amount'];
        $this->currentProduct = [];
        $this->calcRemainder();
    }

    public function deleteFromCart($id)
    {
        $this->total_amount -= $this->cart[$id]['amount'];
        $this->calcRemainder();
        unset($this->cart[$id]);
        if (empty($this->cart)) {
            $this->remainder = 0;
            $this->currentBalance -= $this->remainder;
            $this->paid = 0;
        }
    }

    public function showPurchases()
    {
        $this->editMode = !$this->editMode;
    }

    public function choosePurchase($purchase)
    {
        $this->editMode = !$this->editMode;
        $this->total_amount = $purchase['total_amount'];
        $this->paid = $purchase['purchase_debts'][0]['paid'];
        $this->payment = $purchase['purchase_debts'][0]['payment'];
        $this->bank = $purchase['purchase_debts'][0]['bank'];
        $this->purchase_date = $purchase['purchase_date'];
        $this->id = $purchase['id'];
        foreach ($purchase['purchase_details'] as $detail) {
            $this->cart[$detail['product_id']] = [
                'id' => $detail['product_id'],
                'purchase_id' => $detail['purchase_id'],
                'product_id' => $detail['product_id'],
                'productName' => $detail['product']['productName'],
                'quantity' => $detail['quantity'],
                'purchase_price' => $detail['price'],
                'amount' => $detail['price'] * $detail['quantity'],
            ];

            $this->oldQuantities[$detail['product_id']] = $detail['quantity'];
        }

    }

    public function calcRemainder()
    {
        $this->currentBalance -= $this->remainder;
        $this->remainder = $this->total_amount - floatval($this->paid);
        $this->currentBalance += $this->remainder;
    }

    public function resetData($item = null)
    {
        $this->reset( 'currentProduct', 'cart', 'search', 'supplierSearch', 'paid', 'remainder', 'total_amount', 'id', 'oldQuantities', $item);
    }

    public function render()
    {

        if (!empty($this->currentSupplier)) {

            $this->purchases = \App\Models\Purchase::where('supplier_id', $this->currentSupplier['id'])
                ->where('id', 'LIKE', '%' . $this->purchaseSearch . '%')->where('purchase_date', 'LIKE', '%' . $this->purchaseSearch . '%')
                ->with('purchaseDetails.product', 'purchaseDebts')->get();
        }

        if ($this->purchase_date == '') {
            $this->purchase_date = date('Y-m-d');
        }
        $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();
        $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->get();
        return view('livewire.purchase');
    }
}
