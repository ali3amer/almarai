<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
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
    public array $permissions = [];
    public array $tabPermissions = [];
    public Collection $userPermissions;

    public function save()
    {
        dd($this->permissions);
        if ($this->id == 0) {
            $user = \App\Models\User::create([
                'name' => $this->name,
                'username' => $this->username,
                'password' => Hash::make($this->password),
            ]);

            $user->addRole('user');

            $user->syncPermissions($this->permissions);

            session()->flash('success', 'تم الحفظ بنجاح');
        } else {
            $user = \App\Models\User::find($this->id);
            $user->name = $this->name;
            $user->username = $this->username;
            if ($this->password != '') {
                $user->password = Hash::make($this->password);
            }
            $user->save();

            $user->syncPermissions($this->permissions);

            session()->flash('success', 'تم التعديل بنجاح');
        }
        $this->resetData();
    }

    public function edit($user) {
//        $user->allPermissions()->pluck('name');
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
//        dd(auth()->user()->hasPermission(['users-create']));

        $userPermissions = Auth::user()->allPermissions()->pluck('name');
        foreach ($userPermissions as $permission) {
            $this->tabPermissions[explode('-', $permission)[0]][] =  explode('-', $permission)[1];
        }

        $this->users = \App\Models\User::where('name', 'LIKE', '%'.$this->userSearch.'%')->get();
        return view('livewire.user');
    }
}
