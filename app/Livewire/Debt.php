<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\SaleDebt;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Debt extends Component
{
    public string $title = 'الديون';
    public int $id = 0;
    public string $clientSearch = '';
    public string $saleSearch = '';
    public Collection $clients;
    public Collection $saleDebts;
    public Collection $banks;

    public array $currentClient = [];
    public array $currentSale = [];
    public float $total_amount = 0;
    public array $currentSaleDebts = [];
    public array $cart = [];
    public array $oldQuantities = [];
    public array $details = [];
    public float $paid = 0;
    public float $debtPaid = 0;
    public float $remainder = 0;
    public float $debtRemainder = 0;
    public string $bank = '';
    public int $bank_id = 0;
    public string $due_date = '';
    public string $payment = 'cash';
    public array $debts = [];
    public int $debtId = 0;

    public function chooseClient($client)
    {
        $this->currentClient = $client;
    }

    public function showSale($sale)
    {
        $this->id = $sale['id'];
        $this->details = $sale['sale_details'];
        $this->paid = $sale['sale_debts_sum_paid'];
        $this->total_amount = $sale['total_amount'];
        $this->remainder = $sale['total_amount'] - $this->paid;
    }
    public function getDebts($sale)
    {
        $this->currentSale = $sale;
        $this->id = $sale['id'];
        $this->debts = $sale['sale_debts'];

    }

    public function getReminders()
    {
        $this->payMode = true;

    }

    public function chooseSale($sale)
    {
        $this->total_amount = $sale['total_amount'];
        $this->paid = $sale['sale_debts'][0]['paid'];
        $this->payment = $sale['sale_debts'][0]['payment'];
        $this->bank = $sale['sale_debts'][0]['bank'];
        $this->sale_date = $sale['sale_date'];
        $this->id = $sale['id'];
        foreach ($sale['sale_details'] as $detail) {
            $this->cart[$detail['product_id']] = [
                'id' => $detail['product_id'],
                'sale_id' => $detail['sale_id'],
                'product_id' => $detail['product_id'],
                'productName' => $detail['product']['productName'],
                'quantity' => $detail['quantity'],
                'sale_price' => $detail['price'],
                'amount' => $detail['price'] * $detail['quantity'],
            ];

            $this->oldQuantities[$detail['product_id']] = $detail['quantity'];
        }

    }

    public function payDebt()
    {
        if ($this->debtId == 0) {
            $this->currentClient['currentBalance'] -= $this->debtPaid;
            SaleDebt::create([
                'sale_id' => $this->id,
                'paid' => $this->debtPaid,
                'bank' => $this->bank,
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'remainder' => $this->debtRemainder,
                'current_balance' => $this->currentClient['currentBalance'],
                'due_date' => $this->due_date
            ]);
            \App\Models\Client::where($this->currentClient['id'])->decrement('currentBalance', $this->debtPaid);

            if ($this->payment == 'cash') {
                \App\Models\Safe::first()->increment('currentBalance', $this->debtPaid);
            } else {
                \App\Models\Bank::where('id', $this->bank_id)->increment('currentBalance', $this->debtPaid);
            }

            session()->flash('success', 'تم الحفظ بنجاح');
        } else {
            $debtbalance = SaleDebt::where('id', $this->debtId)->first();
            $this->currentClient['currentBalance'] -= $debtbalance['paid'];
            $this->currentClient['currentBalance'] += $this->debtPaid;
            Client::where('id', $this->currentClient['id'])->update('currentBalance', $this->currentClient['currentBalance']);

            if ($debtbalance['payment'] == 'cash') {
                \App\Models\Safe::first()->increment('currentBalance', $debtbalance['paid']);
            } else {
                \App\Models\Bank::where('id', $this->bank_id)->increment('currentBalance', $debtbalance['paid']);
            }

            if ($this->payment == 'cash') {
                \App\Models\Safe::first()->increment('currentBalance', $this->debtPaid);
            } else {
                \App\Models\Bank::where('id', $this->bank_id)->increment('currentBalance', $this->debtPaid);
            }

            SaleDebt::where('id', $this->debtId)->update([
                'paid' => $this->debtPaid,
                'bank' => $this->bank,
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'remainder' => $this->debtRemainder,
                'current_balance' => $this->currentClient['currentBalance'],
                'due_date' => $this->due_date
            ]);
            session()->flash('success', 'تم التعديل بنجاح');

        }
        $this->debts = SaleDebt::where('sale_id', $this->currentSale['id'])->get()->toArray();
        $this->reset('debtId', 'debtPaid', 'remainder', 'bank', 'payment', 'due_date');
    }

    public function chooseDebt($id)
    {
        $this->debtId = $id;
        $debt = SaleDebt::where('id', $id)->first();
        $this->debtPaid = $debt['paid'];
        $this->bank = $debt['bank'];
        $this->bank_id = $debt['bank_id'];
        $this->payment = $debt['payment'];
        $this->debtRemainder = floatval($debt['reminder']);
        $this->due_date = $debt['due_date'];
    }

    public function deleteDebt($id)
    {
        $debt = SaleDebt::where('id', $id)->first();
        \App\Models\Client::where('id', $this->currentClient['id'])->increment('currentBalance', $debt['paid']);
        if ($debt['payment'] == 'cash') {
            \App\Models\Safe::first()->decrement('currentBalance', $debt['paid']);
        } else {
            \App\Models\Bank::where('id', $debt['bank_id'])->decrement('currentBalance', $debt['paid']);
        }
        $this->debts = SaleDebt::where('sale_id', $this->currentSale['id'])->get()->toArray();
    }

    public function render()
    {
        $this->banks = Bank::all();
        $this->clients = \App\Models\Client::where('clientName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        if (!empty($this->currentClient)) {
            $this->saleDebts = \App\Models\Sale::with('saleDebts', 'saleDetails.product')->withSum('saleDebts', 'paid')->where('client_id', $this->currentClient['id'])->where('id', 'LIKE', '%' . $this->saleSearch . '%')->get()->keyBy('id');
        } else {
            $this->due_date = date('Y-m-d');
        }
        return view('livewire.debt');
    }
}
