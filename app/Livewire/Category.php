<?php

namespace App\Livewire;

use Livewire\Attributes\Rule;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class Category extends Component
{
    public string $title = 'الأقسام';
    public int $id = 0;
    #[Rule('required|min:2', message: 'أدخل إسم القسم')]
    public string $categoryName = '';
    public  string $search = '';
    public Collection $categories;

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Category::create(['categoryName' => $this->categoryName]);
            } else {
                $category = \App\Models\Category::find($id);
                $category->categoryName = $this->categoryName;
                $category->save();
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
    }




    public function render()
    {
        $this->categories = \App\Models\Category::where('categoryName', 'like', '%' . $this->search . '%')->get();

        return view('livewire.category');
    }
}

