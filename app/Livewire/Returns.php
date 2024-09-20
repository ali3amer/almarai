<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\SaleDebt;
use App\Models\SaleDetail;
use App\Models\SaleReturn;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use mysql_xdevapi\CollectionRemove;

class Returns extends Component
{
    use LivewireAlert;

    protected $listeners = [
        'delete',
    ];
    public string $title = 'المرتجعات';
    public bool $show = false;

    public bool $create = false;
    public bool $read = false;
    public bool $update = false;
    public bool $delete = false;
    public string $productName = '';
    public $payment = 'cash';
    public $bank_id = null;
    public $bank = null;
    public bool $editMode = false;
    public int $id = 0;
    public float $price = 0;
    public $paid = 0;
    public float $quantity = 0;
    public float $amount = 0;
    public $quantityReturn = 0;
    public float $priceReturn = 0;

    public string $due_date = '';
    public string $clientSearch = '';
    public Collection $clients;
    public Collection $returns;
    public Collection $saleDetails;
    public Collection $sales;
    public array $currentClient = [];
    public array $currentDetail = [];
    public string $saleSearch = '';
    public array $currentSale = [];
    public string $buyer = 'client';
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
    public function chooseClient($client)
    {
        $this->currentClient = [];
        $this->currentClient = $client;
        if (!isset($this->currentClient['cash'])) {
            $this->currentClient['cash'] = false;
        }
    }

    public function chooseSale($sale)
    {
        $this->currentSale = [];
        $this->currentSale = $sale;
        $this->saleDetails = SaleDetail::with('product')->where('sale_id', $this->currentSale['id'])->get();

    }

    public function chooseDetail($detail, $product)
    {
        $this->currentDetail = $detail;
        $this->productName = $product['productName'];
        $this->quantity = $detail['quantity'];
        $this->price = $detail['price'];

        $this->due_date = $detail['due_date'] ?? $this->due_date;
    }

    public function getReturns($sale)
    {
        $this->chooseSale($sale);
        $this->returns = SaleReturn::with('product')->where('sale_id', $sale['id'])->get();
    }

    public function calcQuantity()
    {
        $this->reminderQuantity = $this->currentDetail['quantity'] - floatval($this->quantityReturn);
        $this->reminderAmount = $this->quantity * $this->price;
        $this->priceReturn = $this->currentDetail['price'] * floatval($this->quantityReturn);
    }

    public function save()
    {

        if (floatval($this->amount) > floatval(session("safeBalance"))) {
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
        } else {
            $sale = \App\Models\Sale::where('id', $this->currentDetail['sale_id'])->first();

            if (!$this->editMode) {
                SaleReturn::create([
                    'sale_id' => $this->currentDetail['sale_id'],
                    'product_id' => $this->currentDetail['product_id'],
                    'quantity' => floatval($this->quantityReturn),
                    'price' => $this->currentDetail['price'],
                    'amount' => $this->amount,
                    'due_date' => $this->due_date,
                ]);
            } else {
                SaleReturn::where("id", $this->id)->update([
                    'quantity' => floatval($this->quantityReturn),
                    'price' => floatval($this->price),
                    'amount' => $this->amount,
                ]);
            }

            $this->getReturns($sale->toArray());

            $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

            $this->resetData();
        }

    }

    public function edit($id)
    {
        $this->editMode = true;
        $this->id = $id;
        $return = SaleReturn::find($id);
        $saleDetail = SaleDetail::where("sale_id", $return['sale_id'])->where('product_id', $return['product_id'])->first();
        $this->currentDetail = $saleDetail->toArray();
        $this->productName = $saleDetail->product->productName;
        $this->price = $return['price'];
        $this->quantity = $saleDetail->quantity;
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
        SaleReturn::where("id", $id)->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
        $this->getReturns($this->currentSale);
    }

    public function resetData($data = null)
    {
        $this->reset('productName', 'editMode', 'amount', 'quantity', 'price', 'quantityReturn', 'clientSearch', 'currentDetail', 'saleSearch', 'due_date', 'id', 'priceReturn', $data);
    }

    public function render()
    {
        if ($this->due_date == '') {
            $this->due_date = session("date");
        }
            $this->clients = \App\Models\People::where("type", $this->buyer)->where('name', 'LIKE', '%' . $this->clientSearch . '%')->get();
        if (!empty($this->currentClient)) {
            $this->sales = \App\Models\Sale::where('people_id', $this->currentClient['id'])->where('id', 'LIKE', '%' . $this->saleSearch . '%')->get();

            if ($this->currentClient['cash'] && !empty($this->currentDetail)) {
                $this->amount = floatval($this->price) * floatval($this->quantityReturn);
            }
        }

        return view('livewire.returns');
    }
}
