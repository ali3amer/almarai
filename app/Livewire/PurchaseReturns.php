<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\PurchaseDebt;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use Illuminate\Database\Eloquent\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use mysql_xdevapi\CollectionRemove;

class PurchaseReturns extends Component
{
    use LivewireAlert;

    protected $listeners = [
        'delete',
    ];
    public string $title = 'مرتجعات المشتريات';

    public string $productName = '';
    public bool $editMode = false;
    public int $id = 0;
    public float $price = 0;
    public float $quantity = 0;
    public float $amount = 0;
    public $quantityReturn = 0;
    public float $priceReturn = 0;

    public string $return_date = '';
    public string $supplierSearch = '';
    public Collection $suppliers;
    public Collection $returns;
    public Collection $purchaseDetails;
    public Collection $purchases;
    public array $currentSupplier = [];
    public array $currentDetail = [];
    public string $purchaseSearch = '';
    public array $currentPurchase = [];
    public string $buyer = 'supplier';
    /**
     * @var float|mixed
     */
    public $reminderQuantity = 0;

    public function chooseSupplier($supplier)
    {
        $this->currentSupplier = [];
        $this->currentSupplier = $supplier;
    }

    public function choosePurchase($purchase)
    {
        $this->currentPurchase = [];
        $this->currentPurchase = $purchase;
        $this->purchaseDetails = PurchaseDetail::with('product')->where('purchase_id', $this->currentPurchase['id'])->get();

    }

    public function chooseDetail($detail, $product)
    {
        $this->currentDetail = $detail;
        $this->productName = $product['productName'];
        $this->quantity = $detail['quantity'];
        $this->price = $detail['price'];

        $this->return_date = $detail['return_date'] ?? $this->return_date;
        $this->amount = $detail['quantity'] * $detail['price'];
    }

    public function getReturns($purchase)
    {
        $this->choosePurchase($purchase);
        $this->returns = PurchaseReturn::with('product')->where('purchase_id', $purchase['id'])->get();
    }

    public function calcQuantity()
    {
        $this->reminderQuantity = $this->currentDetail['quantity'] - floatval($this->quantityReturn);
        $this->reminderAmount = $this->quantity * $this->price;
        $this->priceReturn = $this->currentDetail['price'] * floatval($this->quantityReturn);
    }

    public function save()
    {
        if ($this->id == 0) {

            \App\Models\PurchaseDetail::where('id', $this->currentDetail['id'])->decrement('quantity', floatval($this->quantityReturn));

            \App\Models\Product::where('id', $this->currentDetail['product_id'])->decrement('stock', floatval($this->quantityReturn));

            $purchase = \App\Models\Purchase::where('id', $this->currentDetail['purchase_id'])->first();

            $purchase->decrement('total_amount', $this->priceReturn);

            if ($this->buyer == 'supplier') {
                \App\Models\SupplierDebt::create([
                    'supplier_id' => $this->currentSupplier['id'],
                    'paid' => $this->priceReturn,
                    'debt' => 0,
                    'type' => 'pay',
                    'bank' => '',
                    'payment' => 'cash',
                    'bank_id' => null,
                    'due_date' => $this->return_date,
                    'note' =>  'تم إرجاع منتج من فاتوره رقم #' . $purchase['id'],
                    'purchase_id' => $purchase['id'],
                    'user_id' => auth()->id()
                ]);
            }

            PurchaseReturn::create([
                'purchase_id' => $this->currentDetail['purchase_id'],
                'product_id' => $this->currentDetail['product_id'],
                'quantity' => floatval($this->quantityReturn),
                'return_date' => $this->return_date,
                'price' => $this->currentDetail['price']
            ]);

            $this->getReturns($this->currentPurchase);
            $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
        }

        $this->resetData();
    }

    public function deleteMessage($return)
    {
        $this->confirm("  هل توافق على الحذف ؟", [
            'inputAttributes' => ["return" => $return],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "delete",
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }

    public function resetData($data = null)
    {
        $this->reset('productName', 'editMode', 'amount', 'quantity', 'price', 'quantityReturn', 'supplierSearch', 'currentDetail', 'purchaseSearch', 'return_date', $data);
    }

    public function render()
    {
        if ($this->return_date == '') {
            $this->return_date = date('Y-m-d');
        }
        $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();

        if (!empty($this->currentSupplier)) {
            $this->purchases = \App\Models\Purchase::where('supplier_id', $this->currentSupplier['id'])->where('id', 'LIKE', '%' . $this->purchaseSearch . '%')->get();
        }
        return view('livewire.purchase-returns');
    }
}
