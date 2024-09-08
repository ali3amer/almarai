<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\PurchaseDebt;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use App\Models\SaleDetail;
use App\Models\SaleReturn;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class PurchaseReturns extends Component
{
    use LivewireAlert;

    protected $listeners = [
        'delete',
    ];
    public string $title = 'مرتجعات المشتريات';

    public string $productName = '';
    public $payment = 'cash';
    public $bank_id = null;
    public $bank = null;
    public bool $editMode = false;
    public bool $create = false;
    public bool $read = false;
    public bool $update = false;
    public bool $delete = false;
    public int $id = 0;
    public float $price = 0;
    public float $quantity = 0;
    public float $amount = 0;
    public $quantityReturn = 0;
    public float $priceReturn = 0;

    public string $due_date = '';
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
    public $reminderAmount = 0;


    public function mount()
    {
        $this->settings = Setting::first();
        $user = auth()->user();
        $this->create = $user->hasPermission('returns-create');
        $this->read = $user->hasPermission('returns-read');
        $this->update = $user->hasPermission('returns-update');
        $this->delete = $user->hasPermission('returns-delete');
    }
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

        $this->due_date = $detail['due_date'] ?? $this->due_date;
        $this->amount = 0;
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
        $purchase = \App\Models\Purchase::where('id', $this->currentDetail['purchase_id'])->first();

        $quantity = \App\Models\Product::where("id", $this->currentDetail['product_id'])->first()->stock;
        if (floatval($this->quantityReturn) < floatval($quantity)) {

            if (!$this->editMode) {
                PurchaseReturn::create([
                    'purchase_id' => $purchase['id'],
                    'product_id' => $this->currentDetail['product_id'],
                    'quantity' => floatval($this->quantityReturn),
                    'due_date' => $this->due_date,
                    'price' => $this->currentDetail['price'],
                    'amount' => $this->amount
                ]);
            } else {
                PurchaseReturn::where("id", $this->id)->update([
                    'quantity' => floatval($this->quantityReturn),
                    'price' => $this->currentDetail['price'],
                    'amount' => $this->amount
                ]);

            }

            $this->getReturns($purchase->toArray());

            $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
            $this->resetData();

        } else {
            $this->confirm("لايمكنك إرجاع كمية المنتج لان الكمية الموجوده بالمخزن اقل من المرتجعه", [
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


    public function edit($id)
    {
        $this->editMode = true;
        $this->id = $id;
        $return = PurchaseReturn::find($id);
        $purchaseDetail = PurchaseDetail::where("purchase_id", $return['purchase_id'])->where('product_id', $return['product_id'])->first();
        $this->currentDetail = $purchaseDetail->toArray();
        $this->productName = $purchaseDetail->product->productName;
        $this->price = $return['price'];
        $this->quantity = $purchaseDetail->quantity;
        $this->quantityReturn = $return['quantity'];
        $this->priceReturn = $return['quantity'] * $return['price'];
        $this->amount = $return['amount'];
        $this->due_date = $return['due_date'];
    }

    public function deleteMessage($id)
    {
        $this->confirm("  هل توافق على الحذف ؟", [
            'inputAttributes' => ["id" => $id],
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

    public function delete($data)
    {
        $id = $data['inputAttributes']['id'];
        PurchaseReturn::where("id", $id)->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
        $this->getReturns($this->currentPurchase);
    }
    public function resetData($data = null)
    {
        $this->reset('productName', 'editMode', 'amount', 'quantity', 'price', 'quantityReturn', 'priceReturn', 'supplierSearch', 'currentDetail', 'purchaseSearch', 'id', 'due_date', $data);
    }

    public function render()
    {
        if ($this->due_date == '') {
            $this->due_date = session("date");
        }
        $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();

        if (!empty($this->currentSupplier)) {
            $this->purchases = \App\Models\Purchase::where('supplier_id', $this->currentSupplier['id'])->where('id', 'LIKE', '%' . $this->purchaseSearch . '%')->get();

            if ($this->currentSupplier['cash'] && !empty($this->currentDetail)) {
                $this->amount = floatval($this->price) * floatval($this->quantityReturn);
            }
        }
        return view('livewire.purchase-returns');
    }
}
