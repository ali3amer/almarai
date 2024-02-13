<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Settings extends Component
{
    use LivewireAlert;

    public $title = "الإعدادات";

    public $name = "Point Of Sale";
    public $barcode = false;
    public $batch = false;
    public $expired_date = false;

    public $logo = "";

    public function mount()
    {
        $settings = Setting::first();
        if ($settings) {
            $this->name = $settings->name;
            $this->barcode = (bool)$settings->barcode;
            $this->batch = (bool)$settings->batch;
            $this->expired_date = (bool)$settings->expired_date;
        }
    }
    public function save()
    {
        $settings = Setting::first();

        if ($settings) {
            $settings->delete();
        }

        Setting::create([
            "name" => $this->name,
            "barcode" => $this->barcode,
            "batch" => $this->batch,
            "expired_date" => $this->expired_date,
        ]);

        $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

    }

    public function render()
    {
        return view('livewire.settings');
    }
}
