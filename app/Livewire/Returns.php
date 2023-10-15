<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\SaleDebt;
use App\Models\SaleDetail;
use App\Models\SaleReturn;
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

    public string $productName = '';
    public bool $editMode = false;
    public int $id = 0;
    public float $price = 0;
    public float $quantity = 0;
    public float $amount = 0;
    public $quantityReturn = 0;
    public float $priceReturn = 0;

    public string $return_date = '';
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

    public function chooseClient($client)
    {
        $this->currentClient = [];
        $this->currentClient = $client;
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

        $this->return_date = $detail['return_date'] ?? $this->return_date;
        $this->amount = $detail['quantity'] * $detail['price'];
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

        if ($this->id == 0) {

            \App\Models\SaleDetail::where('id', $this->currentDetail['id'])->decrement('quantity', floatval($this->quantityReturn));

            \App\Models\Product::where('id', $this->currentDetail['product_id'])->increment('stock', floatval($this->quantityReturn));

            $sale = \App\Models\Sale::where('id', $this->currentDetail['sale_id'])->first();

            $sale->decrement('total_amount', $this->priceReturn);
            if ($this->buyer == 'client') {
                \App\Models\ClientDebt::create([
                    'client_id' => $this->currentClient['id'],
                    'paid' => $this->priceReturn,
                    'debt' => 0,
                    'type' => 'pay',
                    'bank' => '',
                    'payment' => 'cash',
                    'bank_id' => null,
                    'due_date' => $this->return_date,
                    'note' => 'تم إرجاع منتج من فاتوره رقم #' . $sale['id'],
                    'sale_id' => $sale['id'],
                    'user_id' => auth()->id()
                ]);
            } elseif ($this->buyer == 'supplier') {
                \App\Models\SupplierDebt::create([
                    'supplier_id' => $this->currentClient['id'],
                    'paid' => $this->priceReturn,
                    'debt' => 0,
                    'type' => 'pay',
                    'bank' => '',
                    'payment' => 'cash',
                    'bank_id' => null,
                    'due_date' => $this->return_date,
                    'note' => 'تم إرجاع منتج من مبيعات فاتوره رقم #' . $sale['id'],
                    'sale_id' => $sale['id'],
                    'user_id' => auth()->id()
                ]);
            } elseif ($this->buyer == 'employee') {
                \App\Models\EmployeeDebt::create([
                    'employee_id' => $this->currentClient['id'],
                    'paid' => $this->priceReturn,
                    'debt' => 0,
                    'type' => 'pay',
                    'bank' => '',
                    'payment' => 'cash',
                    'bank_id' => null,
                    'due_date' => $this->return_date,
                    'note' => 'تم إرجاع منتج من فاتوره رقم #' . $sale['id'],
                    'sale_id' => $sale['id'],
                    'user_id' => auth()->id()
                ]);
            }

            SaleReturn::create([
                'sale_id' => $this->currentDetail['sale_id'],
                'product_id' => $this->currentDetail['product_id'],
                'quantity' => floatval($this->quantityReturn),
                'return_date' => $this->return_date,
                'price' => $this->currentDetail['price']
            ]);

            $this->getReturns($this->currentSale);
            $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
        }

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

    public function resetData($data = null)
    {
        $this->reset('productName', 'editMode', 'amount', 'quantity', 'price', 'quantityReturn', 'clientSearch', 'currentDetail', 'saleSearch', 'return_date', $data);
    }

    public function render()
    {
        if ($this->return_date == '') {
            $this->return_date = date('Y-m-d');
        }
        if ($this->buyer == 'client') {
            $this->clients = \App\Models\Client::where('clientName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        } elseif ($this->buyer == 'supplier') {
            $this->clients = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        } elseif ($this->buyer == 'employee') {
            $this->clients = \App\Models\Employee::where('employeeName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        }
        if (!empty($this->currentClient)) {
            if ($this->buyer == 'client') {
                $this->sales = \App\Models\Sale::where('client_id', $this->currentClient['id'])->where('id', 'LIKE', '%' . $this->saleSearch . '%')->get();
            } elseif($this->buyer == 'employee') {
                $this->sales = \App\Models\Sale::where('employee_id', $this->currentClient['id'])->where('id', 'LIKE', '%' . $this->saleSearch . '%')->get();
            } elseif ($this->buyer == 'supplier') {
                $this->sales = \App\Models\Sale::where('supplier_id', $this->currentClient['id'])->where('id', 'LIKE', '%' . $this->saleSearch . '%')->get();

            }
        }
        return view('livewire.returns');
    }
}
