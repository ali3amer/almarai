<?php

namespace App\Livewire;

use Livewire\Attributes\Rule;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class Category extends Component
{
    public string $title = 'الأقسام';
    public int $id = 0;
    #[Rule('required|min:2')]
    public string $name = '';
    public  string $search = '';
    public Collection $categories;

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Category::create(['name' => $this->name]);
            } else {
                $category = \App\Models\Category::find($id);
                $category->name = $this->name;
                $category->save();
            }
            $this->id = 0;
            $this->name = '';
        }

    }

    public function edit($category)
    {
        $this->id = $category['id'];
        $this->name = $category['name'];
    }

    public function delete($id)
    {
        $category = \App\Models\Category::find($id);
        $category->delete();
    }




    public function render()
    {
        $this->categories = \App\Models\Category::where('name', 'like', '%' . $this->search . '%')->get();

        return view('livewire.category');
    }
}

