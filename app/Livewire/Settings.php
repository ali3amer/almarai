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

    public $initialBalance = 0;
    public $capital = 0;
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
        } else {
            Setting::create([
                "name" => "pos",
                "barcode" => false,
                "batch" => false,
                "expired_date" => false,
            ]);
        }

        $safe = \App\Models\Safe::first();

        if ($safe) {
            $this->initialBalance = $safe->initialBalance;
            $this->capital = $safe->capital;
        }
    }

    public function createSettings()
    {
        Setting::create([
            "name" => "pos",
            "barcode" => false,
            "batch" => false,
            "expired_date" => false,
        ]);
    }
    public function save()
    {

        Setting::first()->update([
            "name" => $this->name,
            "barcode" => $this->barcode,
            "batch" => $this->batch,
            "expired_date" => $this->expired_date,
        ]);

        $safe = \App\Models\Safe::first();

        if ($safe) {
            $safe->update([
                "capital" => $this->capital,
                "initialBalance" => $this->initialBalance,
            ]);
        }

        $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

    }

    public function render()
    {
        return view('livewire.settings');
    }
}
