<?php

namespace App\Livewire;

use App\Models\SaleDebt;
use App\Models\Setting;
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
        'save'
    ];

    public string $title = 'المشتريات';
    public int $id = 0;
    public $bank_id = null;
    public $note = null;
    public int $debtId = 0;
    public string $due_date = '';
    public bool $print = false;
    public string $buyer = 'supplier';
    public string $search = '';
    public Collection $purchases;
    public Collection $suppliers;
    public Collection $banks;
    public string $productSearch = '';
    public string $supplierSearch = '';

    public float $amount = 0;
    public $paid = 0;
    public string $payment = 'cash';
    public $bank = null;

    public array $currentSupplier = [];
    public array $oldQuantities = [];
    public array $currentProduct = [];
    public array $cart = [];
    public string $purchaseSearch = '';
    public float $remainder = 0;
    public float $currentPurchasesBalance = 0;
    public bool $editMode = false;
    public array $currentPurchaseDebts = [];
    public array $currentPurchase = [];
    public Collection $purchaseDebts;
    public array $invoice = [];
    public $discount = 0;
    public $cost = 0;
    public Setting $settings;


    public function mount()
    {
        if (Setting::count() != 0) {
            $this->settings = Setting::first();
        } else {
            $this->settings = Setting::create([
                "name" => "pos",
                "barcode" => false,
                "batch" => false,
                "expired_date" => false,
            ]);
        }

        if (\App\Models\People::where("type", "supplier")->count() == 0) {
            \App\Models\People::create(['name' => "نقدي", 'phone' => "", 'initialSalesBalance' => 0,'initialPurchasesBalance' => 0,'initialDepositsBalance' => 0, 'startingDate' => session("date"), 'type' => "supplier", 'blocked' => false, 'cash' => true]);
        }
        if (\App\Models\People::where("type", "supplier")->where("cash", true)->first() != null) {
            $this->currentSupplier = \App\Models\People::where("type", "supplier")->where("cash", true)->first()->toArray();
        } else {
            $this->currentSupplier = \App\Models\People::where("type", "supplier")->first()->toArray();
        }

        $this->currentPurchasesBalance = \App\Models\Purchase::where("people_id", $this->currentSupplier['id'])->sum("remainder") + $this->currentSupplier['initialPurchasesBalance'];

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
                    'people_id' => $this->currentSupplier['id'],
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                    'bank' => $this->bank,
                    'paid' => floatval($this->paid),
                    'discount' => floatval($this->discount),
                    'remainder' => $this->remainder,
                    'amount' => $this->amount,
                    'note' => $this->note,
                    'due_date' => $this->due_date,
                    'user_id' => auth()->id(),
                ]);
                $this->id = $purchase['id'];
                $this->currentPurchasesBalance += $this->remainder;

                foreach ($this->cart as $item) {
                    PurchaseDetail::create([
                        'purchase_id' => $purchase['id'],
                        'product_id' => $item['id'],
                        'quantity' => floatval($item['quantity']),
                        'price' => floatval($item['price']),
                    ]);

                }
            } else {
                \App\Models\Purchase::where("id", $this->id)->update([
                    "people_id" => $this->currentSupplier['id'],
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                    'bank' => $this->bank,
                    'paid' => floatval($this->paid),
                    'remainder' => $this->remainder,
                    'discount' => floatval($this->discount),
                    'amount' => $this->amount,
                    'note' => $this->note,
                    'due_date' => $this->due_date,
                    'user_id' => auth()->id(),
                ]);

                $this->currentPurchasesBalance -= $this->invoice['remainder'];
                $this->currentPurchasesBalance += $this->remainder;
                PurchaseDetail::where("purchase_id", $this->id)->forceDelete();
                foreach ($this->cart as $item) {
                    PurchaseDetail::create([
                        'purchase_id' => $this->id,
                        'product_id' => floatval($item['product_id']),
                        'quantity' => floatval($item['quantity']),
                        'price' => floatval($item['price']),
                    ]);
                }
            }

            $this->showInvoice($this->id);

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
        $this->invoice['date'] = $this->due_date;
        $this->invoice['client'] = $this->currentSupplier['name'];
        $this->invoice['cart'] = $this->cart;
        $this->invoice['remainder'] = $this->remainder;
        $this->invoice['discount'] = floatval($this->discount);
        $this->invoice['paid'] = floatval($this->paid);
        $this->invoice['cost'] = floatval($this->cost);
        $this->invoice['showMode'] = false;
        $this->invoice['amount'] = floatval($this->amount);
        $this->dispatch('sale_created', $this->invoice);
    }

    public function chooseSupplier($supplier)
    {
        $this->currentSupplier = $supplier;
        $this->currentSupplier['blocked'] = $this->buyer != 'employee' ? $this->currentSupplier['blocked'] : false;
        $this->currentSupplier['cash'] = $this->buyer == "client" ? $this->currentSupplier['cash'] : false;

            $supplier = \App\Models\People::where("type", $this->buyer)->find($supplier['id']);
            $this->currentPurchasesBalance = $supplier->currentPurchasesBalance;


        if ($this->currentSupplier['cash']) {
            $this->paid = $this->amount;
        }
    }

    public function chooseProduct(\App\Models\Product $product)
    {
        $this->currentProduct = $product->toArray();
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
            $this->cart[$this->currentProduct['id']]['product_id'] = floatval($this->currentProduct['id']);
            $this->cost += $this->cart[$this->currentProduct['id']]['amount'];

        } else {
            $this->cost -= $this->cart[$this->currentProduct['id']]['amount'];
            $this->cart[$this->currentProduct['id']]['quantity'] += floatval($this->currentProduct['quantity']);
            $this->cart[$this->currentProduct['id']]['amount'] = floatval($this->cart[$this->currentProduct['id']]['price']) * floatval($this->cart[$this->currentProduct['id']]['quantity']);
            $this->cost += $this->cart[$this->currentProduct['id']]['amount'];

        }
        $this->currentProduct = [];
        $this->calcRemainder();
    }

    public function deleteFromCart($id)
    {
        $this->cost -= $this->cart[$id]['amount'];

        if ($this->paid) {
            $this->paid = $this->cost;
        }

        unset($this->cart[$id]);
        if (empty($this->cart)) {
            $this->amount = 0;
            $this->remainder = 0;
            $this->paid = 0;
            $this->discount = 0;
            $this->cost = 0;
        }
        $this->calcRemainder();

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
        $this->invoice['payment'] = $purchase['payment'];
        $this->invoice['bank'] = $purchase['bank'];
        $this->invoice['bank_id'] = $purchase['bank_id'];
        $this->invoice['date'] = $purchase['due_date'];
        $this->invoice['client'] = $this->currentSupplier['name'];
        $this->invoice['cart'] = PurchaseDetail::where('purchase_id', $purchase['id'])->join('products', 'products.id', '=', 'purchase_details.product_id')->get()->keyBy("product_id")->toArray();
        $this->invoice['remainder'] = floatval($purchase['remainder']);
        $this->invoice['paid'] = floatval($purchase['paid']);
        $this->invoice['discount'] = floatval($purchase['discount']);
        $this->invoice['cost'] = floatval($purchase['amount']) + floatval($purchase['discount']);
        $this->invoice['amount'] = $purchase['amount'];
        $this->invoice['showMode'] = false;

        if ($this->invoice['paid'] > 0) {
            $this->payment = $purchase['payment'];
            $this->invoice['paidId'] = $purchase['id'];
        }

        $this->dispatch('sale_created', $this->invoice);
    }

    public function choosePurchase($id)
    {
        $this->id = $this->invoice['id'];
        $this->payment = $this->invoice['payment'];
        $this->bank = $this->invoice['bank'];
        $this->bank_id = $this->invoice['bank_id'];
        $this->paid = floatval($this->invoice['paid']);
        $this->remainder = floatval($this->invoice['remainder']);
        $this->discount = floatval($this->invoice['discount']);
        $this->amount = floatval($this->invoice['amount']);
        $this->cost = floatval($this->invoice['amount']) + floatval($this->invoice['discount']);
        $this->due_date = $this->invoice['date'];
        $this->cart = $this->invoice['cart'];
        foreach ($this->cart as $item) {
            $this->cart[$item['product_id']]['amount'] = floatval($item['price']) * floatval($item['quantity']);
        }
        $this->editMode = false;
    }

    public function editMessage()
    {
        $this->confirm("  هل توافق على تعديل الفاتورة ؟", [
            'inputAttributes' => [],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "save",
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
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

        \App\Models\Purchase::where('id', $id)->forceDelete();

        $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
    }

    public function calcRemainder()
    {
        $this->amount = $this->cost - floatval($this->discount);
        if ($this->currentSupplier['cash']) {
            $this->paid = $this->amount;
        } else {
            $this->remainder = floatval($this->amount) - floatval($this->paid);
        }
    }

    public function resetData($item = null)
    {

        $item == "currentSupplier" ? $this->reset('search', 'supplierSearch', 'id', 'oldQuantities', $item) : $this->reset('currentProduct', 'cart', 'bank', 'payment', 'bank', 'bank_id', 'search', 'supplierSearch', 'discount', 'cost', 'paid', 'remainder', 'amount', 'id', 'oldQuantities', $item);
    }

    public function render()
    {

        if ($this->payment == "bank" && $this->bank_id == null) {
            if ($this->banks->count() != 0) {
                $this->bank_id = $this->banks->first()->id;
            }
        }

        if (!empty($this->currentSupplier)) {
            $this->purchases = \App\Models\Purchase::where('people_id', $this->currentSupplier['id'])
                ->where('id', 'LIKE', '%' . $this->purchaseSearch . '%')->latest()->get();
        }
        if ($this->due_date == '') {
            $this->due_date = session("date");
        }
            $this->suppliers = \App\Models\People::where("type", $this->buyer)->where('name', 'LIKE', '%' . $this->supplierSearch . '%')->get();

        if ($this->settings->barcode) {
            $barcode = \App\Models\Product::where("barcode", $this->productSearch)->first();

            if ($barcode) {
                $this->chooseProduct($barcode);
            }
        }

        $product = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->simplePaginate(10);

        return view('livewire.purchase', [
            'products' => $product
        ]);
    }
}
