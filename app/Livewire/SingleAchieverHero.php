<?php

namespace App\Livewire;

use Livewire\Component;

class SingleAchieverHero extends Component
{
    public $name = 'د. جوخة الحارثية';
    public $english_name = 'Dr. Jokha Al-Harthi';
    public $title = 'أكاديمية بجامعة السلطان قابوس - كاتبة وروائية - فائزة بجائزة البوكر العالمية';

    public function mount()
    {
        // Hardcoded data for Dr. Jokha Al-Harthi
    }

    public function render()
    {
        return view('livewire.single-achiever-hero');
    }
}
