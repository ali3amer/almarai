<?php

namespace App\Livewire;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class Category extends Component
{
    use LivewireAlert;
    protected $listeners = [
        'delete'
    ];

    public string $title = 'الأقسام';
    public int $id = 0;
    public string $categoryName = '';
    public  string $search = '';
    public Collection $categories;

    protected function rules() {
        return [
            'categoryName' => 'required|unique:categories,categoryName,'.$this->id
        ];
    }

    protected function messages() {
        return [
            'categoryName.required' => 'الرجاء إدخال إسم القسم',
            'categoryName.unique' => 'هذا القسم موجود مسبقاً'
        ];
    }

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Category::create(['categoryName' => $this->categoryName]);
                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

            } else {
                $category = \App\Models\Category::find($id);
                $category->categoryName = $this->categoryName;
                $category->save();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);

            }
            $this->id = 0;
            $this->categoryName = '';
        }

    }

    public function edit($category)
    {
        $this->id = $category['id'];
        $this->categoryName = $category['categoryName'];
    }

    public function deleteMessage($category)
    {
        $this->confirm("  هل توافق على حذف قسم  " . $category['categoryName'] .  "؟", [
            'inputAttributes' => ["id"=>$category['id']],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "delete",
            "value" => $category['id'],
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }

    public function delete($data)
    {
        $category = \App\Models\Category::find($data['inputAttributes']['id']);
        $category->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);

    }




    public function render()
    {
        $this->categories = \App\Models\Category::where('categoryName', 'like', '%' . $this->search . '%')->get();

        return view('livewire.category');
    }
}

