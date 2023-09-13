<?php

namespace App\Livewire;

use App\Models\PurchaseDebt;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Claim extends Component
{
    public string $title = 'المطالبات';
    public int $id = 0;
    public string $supplierSearch = '';
    public string $purchaseSearch = '';
    public Collection $suppliers;
    public Collection $purchaseDebts;

    public array $currentSupplier = [];
    public array $currentPurchase = [];
    public float $total_amount = 0;
    public array $currentPurchaseDebts = [];
    public array $cart = [];
    public array $oldQuantities = [];
    public array $details = [];
    public float $paid = 0;
    public float $debtPaid = 0;
    public float $remainder = 0;
    public float $debtRemainder = 0;
    public string $bank = '';
    public string $due_date = '';
    public string $payment = 'cash';
    public array $debts = [];
    public int $debtId = 0;

    public function chooseSupplier($supplier)
    {
        $this->currentSupplier = $supplier;
    }

    public function showPurchase($purchase)
    {
        $this->details = $purchase['purchase_details'];
        $this->paid = $purchase['purchase_debts_sum_paid'];
        $this->total_amount = $purchase['total_amount'];
        $this->remainder = $purchase['total_amount'] - $this->paid;
    }
    public function getDebts($purchase)
    {
        $this->currentPurchase = $purchase;
        $this->id = $purchase['id'];
        $this->debts = $purchase['purchase_debts'];

    }

    public function getReminders()
    {
        $this->payMode = true;

    }

    public function choosePurchase($purchase)
    {
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

    public function payDebt()
    {
        if ($this->debtId == 0) {
            $this->currentSupplier['currentBalance'] -= $this->debtPaid;
            PurchaseDebt::create([
                'purchase_id' => $this->id,
                'paid' => $this->debtPaid,
                'bank' => $this->bank,
                'payment' => $this->payment,
                'remainder' => $this->debtRemainder,
                'current_balance' => $this->currentSupplier['currentBalance'],
                'due_date' => $this->due_date
            ]);
            \App\Models\Supplier::where($this->currentSupplier['id'])->decrement('currentBalance', $this->debtPaid);

            session()->flash('success', 'تم الحفظ بنجاح');
        } else {
            $debtbalance = PurchaseDebt::where('id', $this->debtId)->first();
            $this->currentSupplier['currentBalance'] -= $debtbalance['current_balance'];
            $this->currentSupplier['currentBalance'] += $this->debtPaid;
            PurchaseDebt::where('id', $this->debtId)->update([
                'paid' => $this->debtPaid,
                'bank' => $this->bank,
                'payment' => $this->payment,
                'remainder' => $this->debtRemainder,
                'current_balance' => $this->currentSupplier['currentBalance'],
                'due_date' => $this->due_date
            ]);
            session()->flash('success', 'تم التعديل بنجاح');

        }
        $this->debts = PurchaseDebt::where('purchase_id', $this->currentPurchase['id'])->get()->toArray();
        $this->reset('debtId', 'debtPaid', 'remainder', 'bank', 'payment', 'due_date');
    }

    public function chooseDebt($id)
    {
        $this->debtId = $id;
        $debt = PurchaseDebt::where('id', $id)->first();
        $this->debtPaid = $debt['paid'];
        $this->bank = $debt['bank'];
        $this->payment = $debt['payment'];
        $this->debtRemainder = floatval($debt['reminder']);
        $this->due_date = $debt['due_date'];
    }

    public function deleteDebt($id)
    {
        $debt = PurchaseDebt::where('id', $id)->first();
        \App\Models\Supplier::where('id', $this->currentSupplier['id'])->increment('currentBalance', $debt['paid']);
        $this->debts = PurchaseDebt::where('purchase_id', $this->currentPurchase['id'])->get()->toArray();
    }

    public function render()
    {
        $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();
        if (!empty($this->currentSupplier)) {
            $this->purchaseDebts = \App\Models\Purchase::with('purchaseDebts', 'purchaseDetails.product')->withSum('purchaseDebts', 'paid')->where('supplier_id', $this->currentSupplier['id'])->where('id', 'LIKE', '%' . $this->purchaseSearch . '%')->get()->keyBy('id');
        } else {
            $this->due_date = date('Y-m-d');
        }
        return view('livewire.claim');
    }
}
