<?php

namespace App\Livewire\Pages;

use App\Models\Achievement;
use App\Models\AchievementMainType;
use App\Models\Organization;
use App\Models\OrganizationType;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class OrganizationsPage extends Component
{
    public null|string $searchQuery = null;
    public null|int $main = null;
    public string $selected = 'all';
    public Collection $organizations;

    public function mount(): void
    {
        $this->setOrganizations();
    }

    public function search(): void
    {
        $this->searchQuery = str($this->searchQuery)->isNotEmpty()
            ? $this->searchQuery : null;
        $this->setOrganizations();
    }

    public function setType($key): void
    {
        $this->selected = $key;
        if ($key == 'all') {
            $this->main = null;
            $this->setOrganizations();
            return;
        }
        $this->main = $key;
        $this->setOrganizations();
    }

    public function getCategories(): array
    {
        $list = [];
        $list['all'] = 'الكل';
        foreach (OrganizationType::query()->get() as $type) {
            $list[$type->id] = $type->title_ar;
        }
        return $list;
    }

    public function setOrganizations(): void
    {
        $this->organizations = Organization::query()
            ->latest()
            ->when(str($this->searchQuery)->isNotEmpty(), function ($query) {
                return $query->where('name_ar', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('name_en', 'like', '%' . $this->searchQuery . '%');
            })
            ->when($this->main != null, function ($query) {
                return $query->where('organization_type_id', $this->main);
            })
            ->get();
    }

    public function render(): View
    {
        return view('livewire.pages.organizations-page');
    }
}
