<?php

namespace App\Livewire\Components;

use App\Models\Organization;
use Illuminate\View\View;
use Livewire\Component;

class OrganizationCard extends Component
{
    public Organization $organization;
    public function render() : View
    {
        return view('livewire.components.organization-card');
    }
}
