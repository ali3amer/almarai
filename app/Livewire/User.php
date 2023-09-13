<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class User extends Component
{
    public string $title = 'المستخدمين';
    public int $id = 0;
    public string $name = '';
    public string $userSearch = '';
    public string $username = '';
    public string $password = '';
    public Collection $users;

    public function save()
    {
        if ($this->id == 0) {
            \App\Models\User::create([
                'name' => $this->name,
                'username' => $this->username,
                'password' => Hash::make($this->password),
            ]);
            session()->flash('success', 'تم الحفظ بنجاح');
        } else {
            $user = \App\Models\User::find($this->id);
            $user->name = $this->name;
            $user->username = $this->username;
            if ($this->password != '') {
                $user->password = Hash::make($this->password);
            }
            $user->save();
            session()->flash('success', 'تم التعديل بنجاح');
        }
        $this->resetData();
    }

    public function edit($user) {
        $this->id = $user['id'];
        $this->name = $user['name'];
        $this->username = $user['username'];
    }

    public function delete($id)
    {
        \App\Models\User::where('id', $id)->delete();
        session()->flash('success', 'تم الحذف بنجاح');
    }

    public function resetData()
    {
        $this->reset('name', 'username', 'password', 'id');
    }

    public function render()
    {
        $this->users = \App\Models\User::where('name', 'LIKE', '%'.$this->userSearch.'%')->get();
        return view('livewire.user');
    }
}
