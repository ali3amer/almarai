<?php

namespace App\Livewire;

use Livewire\Attributes\Rule;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class Category extends Component
{
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
                session()->flash('success', 'تم الحفظ بنجاح');

            } else {
                $category = \App\Models\Category::find($id);
                $category->categoryName = $this->categoryName;
                $category->save();
                session()->flash('success', 'تم التعديل بنجاح');

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

    public function delete($id)
    {
        $category = \App\Models\Category::find($id);
        $category->delete();
        session()->flash('success', 'تم الحذف بنجاح');

    }




    public function render()
    {
        $this->categories = \App\Models\Category::where('categoryName', 'like', '%' . $this->search . '%')->get();

        return view('livewire.category');
    }
}

