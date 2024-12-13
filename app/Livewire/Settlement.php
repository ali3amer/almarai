<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Settlement extends Component
{
    use LivewireAlert;
    protected $listeners = [
        'delete',
    ];
    public string $title = 'التسويات';
    public bool $show = false;

    public string $productsSearch = '';
    public string $due_date = '';
    public int $id = 0;
    #[Rule('required|min:1')]
    public $quantity = 0;
    #[Rule('required')]
    public $type = null;
    #[Rule('required')]
    public $note = null;
    public Collection $products;
    public Collection $settlements;
    public array $currentProduct = [];
    public array $currentSettlement = [];

    public function chooseProduct($product)
    {
        $this->currentProduct = $product;
    }

    public function save()
    {
        if ($this->id == 0) {
            \App\Models\Settlement::create([
                'product_id' => $this->currentProduct['id'],
                'quantity' => $this->quantity,
                'type' => $this->type,
                'note' => $this->note,
                'due_date' => $this->due_date,
            ]);

//            \App\Models\Product::where('id', $this->currentProduct['id'])->decrement('stock', $this->quantity);
            $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
        } else {
            \App\Models\Settlement::where('id', $this->id)->update([
                'product_id' => $this->currentProduct['id'],
                'quantity' => floatval($this->quantity),
                'type' => $this->type,
                'note' => $this->note,
                'due_date' => $this->due_date,
            ]);

//            \App\Models\Product::where('id', $this->currentProduct['id'])->increment('stock', $this->currentSettlement['quantity']);
//            \App\Models\Product::where('id', $this->currentProduct['id'])->decrement('stock', $this->quantity);
            $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
        }

        $this->resetData();
    }

    public function edit($settlement)
    {
        $this->id = $settlement['id'];
        $this->currentSettlement = $settlement;
        $this->quantity = $settlement['quantity'];
        $this->currentProduct = $settlement['product'];
        $this->type = $settlement['type'];
        $this->note = $settlement['note'];
        $this->due_date = $settlement['due_date'];
    }

    public function deleteMessage($settlement)
    {
        $this->confirm("  هل توافق على الحذف ؟", [
            'inputAttributes' => ["settlement"=>$settlement],
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
        $settlement = $data['inputAttributes']['settlement'];
//        \App\Models\Product::where('id', $settlement['product_id'])->increment('stock', $settlement['quantity']);
        \App\Models\Settlement::where('id', $settlement['id'])->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
        $this->resetData();
    }

    public function resetData()
    {
        $this->reset('productsSearch', 'id', 'quantity', 'type', 'note', 'currentProduct', 'currentSettlement', 'due_date');
    }
    public function render()
    {
        if ($this->due_date == '') {
            $this->due_date = session("date");
        }
        $this->settlements = \App\Models\Settlement::with('product')->get();
        $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productsSearch . '%')->get();

        return view('livewire.settlement');
    }
}
