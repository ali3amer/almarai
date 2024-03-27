<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\PurchaseDebt;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use App\Models\SaleDetail;
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
            $quantity = \App\Models\Product::where("id", $this->currentDetail['product_id'])->first()->stock;
            if (floatval($this->quantityReturn) < floatval($quantity)) {

                $purchase = \App\Models\Purchase::where('id', $this->currentDetail['purchase_id'])->first();

                \App\Models\PurchaseDebt::create([
                    'supplier_id' => $this->currentSupplier['id'],
                    'paid' => floatval($this->priceReturn),
                    'purchase_id' => $this->currentDetail['purchase_id'],
                    'debt' => 0,
                    'type' => 'pay',
                    'bank' => '',
                    'payment' => 'cash',
                    'bank_id' => null,
                    'due_date' => $this->return_date,
                    'note' => 'تم خصم قيمة المنتج المرجع من فاتورة #' . $purchase['id'],
                    'user_id' => auth()->id()
                ]);


                if (floatval($purchase["paid"]) == 0 || floatval($purchase["paid"]) < $this->priceReturn) {
                    $purchasePaid = 0;
                } else {
                    $purchasePaid = floatval($purchase["paid"]) - $this->priceReturn;
                }

                if ($purchasePaid != 0 || floatval($purchase["paid"]) >= $this->priceReturn) {
                    \App\Models\PurchaseDebt::create([
                        'supplier_id' => $this->currentSupplier['id'],
                        'paid' => 0,
                        'purchase_id' => $this->currentDetail['purchase_id'],
                        'debt' => floatval($this->priceReturn),
                        'type' => 'debt',
                        'bank' => '',
                        'payment' => 'cash',
                        'bank_id' => null,
                        'due_date' => $this->return_date,
                        'note' => 'تم دفع قيمة المنتج المرجع الى العميل من فاتورة #' . $purchase['id'],
                        'user_id' => auth()->id()
                    ]);
                }
                $total_amount = $purchase['total_amount'] - $this->priceReturn;
                $remainder = $total_amount - $purchasePaid - $purchase['discount'];
                $purchase->update([
                    'paid' => $purchasePaid,
                    'remainder' => $remainder,
                    'total_amount' => $total_amount,
                ]);

                PurchaseDetail::where("purchase_id", $purchase['id'])->where("product_id", $this->currentDetail['product_id'])->decrement("quantity", floatval($this->quantityReturn));

                PurchaseReturn::create([
                    'purchase_id' => $purchase['id'],
                    'product_id' => $this->currentDetail['product_id'],
                    'quantity' => floatval($this->quantityReturn),
                    'return_date' => $this->return_date,
                    'price' => $this->currentDetail['price']
                ]);

                $this->getReturns($purchase->toArray());

                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
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
            $this->return_date = session("date");
        }
        $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();

        if (!empty($this->currentSupplier)) {
            $this->purchases = \App\Models\Purchase::where('supplier_id', $this->currentSupplier['id'])->where('id', 'LIKE', '%' . $this->purchaseSearch . '%')->get();
        }
        return view('livewire.purchase-returns');
    }
}
