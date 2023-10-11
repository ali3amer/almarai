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

    public function chooseSupplier($supplier)
    {
        $this->currentSupplier = [];
        $this->currentSupplier = $supplier;
    }

    public function choosePurchase($purchase, $editMode)
    {
        $this->editMode = $editMode;

        $this->currentPurchase = [];
        $this->currentPurchase = $purchase;
        $this->id = $this->currentPurchase['id'];
        $this->purchaseDetails = PurchaseDetail::with('product')->where('purchase_id', $this->currentPurchase['id'])->get();

    }

    public function chooseDetail($detail, $product)
    {
        $this->id = $this->editMode ? $detail['id'] : 0;
        $this->currentDetail = $detail;
        $this->productName = $product['productName'];
        $this->quantity = $detail['quantity'];
        $this->price = $detail['price'];

        $this->return_date = $detail['return_date'] ?? $this->return_date;
        $this->amount = $detail['quantity'] * $detail['price'];
    }

    public function getReturns($purchase)
    {
        $this->choosePurchase($purchase, true);
        $this->returns = PurchaseReturn::with('product')->where('purchase_id', $purchase['id'])->get();
    }

    public function calcQuantity()
    {
        $this->quantity = $this->currentDetail['quantity'] - floatval($this->quantityReturn);
        $this->amount = $this->quantity * $this->price;
        $this->priceReturn = $this->currentDetail['price'] * floatval($this->quantityReturn);
    }

    public function save()
    {

        if ($this->id == 0) {

            \App\Models\PurchaseDetail::where('id', $this->currentDetail['id'])->decrement('quantity', floatval($this->quantityReturn));

            \App\Models\Product::where('id', $this->currentDetail['product_id'])->increment('stock', floatval($this->quantityReturn));

            $purchase = \App\Models\Purchase::where('id', $this->currentDetail['purchase_id'])->first();

            $purchase->decrement('total_amount', $this->priceReturn);

            if ($this->buyer == 'supplier') {
                \App\Models\SupplierDebt::create([
                    'supplier_id' => $this->currentSupplier['id'],
                    'paid' => 0,
                    'debt' => $this->priceReturn,
                    'type' => 'debt',
                    'bank' => '',
                    'payment' => 'cash',
                    'bank_id' => null,
                    'due_date' => $this->return_date,
                    'note' => $purchase['id'] . 'تم إرجاع منتج من فاتوره رقم #',
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
            $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
        } else {
            PurchaseDetail::where('purchase_id', $this->currentDetail['purchase_id'])->where('product_id', $this->currentDetail['product_id'])->increment('quantity', floatval($this->quantityReturn));

            \App\Models\Product::where('id', $this->currentDetail['product_id'])->decrement('stock', floatval($this->quantityReturn));

            \App\Models\Purchase::where('id', $this->currentDetail['purchase_id'])->increment('total_amount', $this->priceReturn);


            if (isset($debt['paid'])) {
                $return = PurchaseReturn::where('id', $this->id)->first();
                if ($debt['payment'] == 'cash') {
                    \App\Models\Safe::first()->decrement('currentBalance', $return['quantity'] * $return['price']);
                    \App\Models\Safe::first()->increment('currentBalance', $this->amount);
                } else {
                    Bank::where('id', $debt['bank_id'])->decrement('currentBalance', $return['quantity'] * $return['price']);
                    Bank::where('id', $debt['bank_id'])->increment('currentBalance', $this->amount);
                }
                if ($this->buyer == 'supplier') {
                    \App\Models\Supplier::where('id', $this->currentSupplier['id'])->decrement('currentBalance', $debt['paid']);
                } elseif ($this->buyer == 'supplier') {
                    \App\Models\Supplier::where('id', $this->currentSupplier['id'])->increment('currentBalance', $debt['paid']);
                }
            }

            PurchaseReturn::where('id', $this->id)->update([
                'quantity' => $this->currentDetail['quantity'] - floatval($this->quantityReturn),
                'return_date' => $this->return_date
            ]);
        }

        $this->alert('success', 'تم تعديل الفاتوره بنجاح', ['timerProgressBar' => true]);
        $this->resetData();
    }

    public function deleteMessage($return)
    {
        $this->confirm("  هل توافق على الحذف ؟", [
            'inputAttributes' => ["return"=>$return],
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
        $return = $data['inputAttributes']['return'];
        PurchaseReturn::where('id', $return['id'])->delete();
        \App\Models\Purchase::where('id', $return['purchase_id'])->increment('total_amount', $return['quantity'] * $return['price']);
        PurchaseDetail::where('purchase_id', $return['purchase_id'])->where('product_id', $return['product_id'])->increment('quantity', $return['quantity']);
        \App\Models\Product::where('id', $return['product_id'])->decrement('stock', $return['quantity']);
        $debt = PurchaseDebt::where('id', $return['purchase_id'])->where('paid', '!=', 0)->first();

        if (isset($debt['paid'])) {

            if ($this->buyer == 'supplier') {
                \App\Models\Supplier::where('id', $this->currentSupplier['id'])->increment('currentBalance', $debt['paid']);
            } elseif ($this->buyer == 'supplier') {
                \App\Models\Supplier::where('id', $this->currentSupplier['id'])->decrement('currentBalance', $debt['paid']);
            }
            if ($debt['payment'] == 'cash') {
                \App\Models\Safe::first()->increment('currentBalance', $debt['paid']);
            } else {
                \App\Models\Bank::where('id', $debt['bank_id'])->increment('currentBalance', $debt['paid']);
            }
        }
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);

    }

    public function resetData($data = null)
    {
        $this->reset('productName', 'editMode', 'amount', 'quantity', 'price', 'quantityReturn', 'supplierSearch', 'currentDetail', 'currentPurchase', 'purchaseSearch', 'return_date', $data);
    }

    public function render()
    {
        if ($this->return_date == '') {
            $this->return_date = date('Y-m-d');
        }
        if ($this->buyer == 'supplier') {
            $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();
        } elseif ($this->buyer == 'supplier') {
            $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();
        } elseif ($this->buyer == 'employee') {
            $this->suppliers = \App\Models\Employee::where('employeeName', 'LIKE', '%' . $this->supplierSearch . '%')->get();
        }
        if (!empty($this->currentSupplier)) {
            if ($this->buyer == 'supplier') {
                $this->purchases = \App\Models\Purchase::where('supplier_id', $this->currentSupplier['id'])->where('id', 'LIKE', '%' . $this->purchaseSearch . '%')->get();
            } elseif($this->buyer == 'employee') {
                $this->purchases = \App\Models\Purchase::where('employee_id', $this->currentSupplier['id'])->where('id', 'LIKE', '%' . $this->purchaseSearch . '%')->get();
            } elseif ($this->buyer == 'supplier') {
                $this->purchases = \App\Models\Purchase::where('supplier_id', $this->currentSupplier['id'])->where('id', 'LIKE', '%' . $this->purchaseSearch . '%')->get();

            }
        }
        return view('livewire.purchase-returns');
    }
}
