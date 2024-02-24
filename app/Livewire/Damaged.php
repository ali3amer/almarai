<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Component;
use function Symfony\Component\Translation\t;

class Damaged extends Component
{
    use LivewireAlert;
    protected $listeners = [
        'delete',
    ];
    public string $title = 'المنتجات التالفه';
    public string $productsSearch = '';
    public string $damaged_date = '';
    public int $id = 0;
    #[Rule('required|min:1')]
    public float $quantity = 0;
    public Collection $products;
    public Collection $damageds;
    public array $currentProduct = [];
    public array $currentDamaged = [];

    public function chooseProduct($product)
    {
        $this->currentProduct = $product;
    }

    public function save()
    {
        if ($this->id == 0) {
            \App\Models\Damaged::create([
                'product_id' => $this->currentProduct['id'],
                'quantity' => $this->quantity,
                'damaged_date' => $this->damaged_date,
            ]);

//            \App\Models\Product::where('id', $this->currentProduct['id'])->decrement('stock', $this->quantity);
            $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
        } else {
            \App\Models\Damaged::where('id', $this->id)->update([
                'product_id' => $this->currentProduct['id'],
                'quantity' => $this->quantity,
                'damaged_date' => $this->damaged_date,
            ]);

//            \App\Models\Product::where('id', $this->currentProduct['id'])->increment('stock', $this->currentDamaged['quantity']);
//            \App\Models\Product::where('id', $this->currentProduct['id'])->decrement('stock', $this->quantity);
            $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
        }

        $this->resetData();
    }

    public function edit($damaged)
    {
        $this->id = $damaged['id'];
        $this->currentDamaged = $damaged;
        $this->quantity = $damaged['quantity'];
        $this->currentProduct = $damaged['product'];
        $this->damaged_date = $damaged['damaged_date'];
    }

    public function deleteMessage($damaged)
    {
        $this->confirm("  هل توافق على الحذف ؟", [
            'inputAttributes' => ["damaged"=>$damaged],
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
        $damaged = $data['inputAttributes']['damaged'];
//        \App\Models\Product::where('id', $damaged['product_id'])->increment('stock', $damaged['quantity']);
        \App\Models\Damaged::where('id', $damaged['id'])->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
        $this->resetData();
    }

    public function resetData()
    {
        $this->reset('productsSearch', 'id', 'quantity', 'currentProduct', 'currentDamaged', 'damaged_date');
    }

    public function render()
    {
        if ($this->damaged_date == '') {
            $this->damaged_date = session("date");
        }
        $this->damageds = \App\Models\Damaged::with('product')->get();
        $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productsSearch . '%')->get();
        return view('livewire.damaged');
    }
}
