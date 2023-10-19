<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Component;

class User extends Component
{
    use LivewireAlert;
    protected $listeners = [
        'delete',
    ];
    public string $title = 'المستخدمين';
    public int $id = 0;
    #[Rule('required', message: 'أدخل إسم المستخدم كامل')]
    public string $name = '';
    public string $userSearch = '';

    public string $username = '';
    public string $password = '';
    public Collection $users;
    public array $permissions = [];
    public array $permissionsList = [
        ['stores', 'المخازن'],
        ['categories', 'الاقسام'],
        ['products', 'المنتجات'],
        ['suppliers', 'الموردين'],
        ['clients', 'العملاء'],
        ['purchases', 'المشتريات'],
        ['sales', 'المبيعات'],
        ['employees', 'الموظفين'],
        ['expenses', 'المصروفات'],
        ['reports', 'التقارير'],
        ['returns', 'المرتجعات'],
        ['purchase-returns', 'مرتجعات المشتريات'],
        ['safes', 'الخزنه'],
        ['damageds', 'التالف'],
        ['users', 'المستخدمين'],
    ];
    public array $tabPermissions = [];
    public Collection $userPermissions;

    protected function rules() {
        return [
            'username' => 'required|unique:users,username,'.$this->id
        ];
    }

    protected function messages() {
        return [
            'username.required' => 'الرجاء إدخال إسم الدخول',
            'username.unique' => 'هذا المستخدم موجود مسبقاً'
        ];
    }


    public function save()
    {
        if ($this->validate()) {
            if ($this->id == 0) {
                $user = \App\Models\User::create([
                    'name' => $this->name,
                    'username' => $this->username,
                    'password' => Hash::make($this->password),
                ]);

                $user->addRole('user');

                $user->syncPermissions($this->permissions);

                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
            } else {
                $user = \App\Models\User::find($this->id);
                $user->name = $this->name;
                $user->username = $this->username;
                if ($this->password != '') {
                    $user->password = Hash::make($this->password);
                }
                if ($user->id != 1) {
                    $user->syncPermissions($this->permissions);
                }
                $user->save();


                $this->permissions = [];

                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
            }
        }
        $this->resetData();
    }

    public function edit($user)
    {
        $this->clearValidation();
        $this->permissions = \App\Models\User::find($user['id'])->allPermissions()->pluck('name')->toArray();
        $this->id = $user['id'];
        $this->name = $user['name'];
        $this->username = $user['username'];
    }

    public function deleteMessage($user)
    {
        $this->confirm("  هل توافق على حذف المستخدم  " . $user['name'] .  "؟", [
            'inputAttributes' => ["id"=>$user['id']],
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
        \App\Models\User::where('id', $data['inputAttributes']['id'])->delete();
        $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
    }

    public function resetData()
    {
        $this->clearValidation();

        $this->reset('name', 'username', 'password', 'id', 'permissions');
    }

    public function render()
    {

        $this->users = \App\Models\User::where('name', 'LIKE', '%' . $this->userSearch . '%')->get();
        return view('livewire.user');
    }
}
