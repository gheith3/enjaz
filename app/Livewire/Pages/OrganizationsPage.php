<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Illuminate\View\View;
use App\Models\Achievement;
use App\Models\Organization;
use Livewire\WithPagination;
use App\Models\OrganizationType;
use Illuminate\Support\Collection;
use App\Models\AchievementMainType;
use App\Traits\InvalidatesPageCache;
use Illuminate\Pagination\LengthAwarePaginator;

class OrganizationsPage extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public null|string $searchQuery = null;
    public null|int $main = null;
    public string $selected = 'all';

    public function mount(): void
    {
        // Remove this since we don't need to set organizations anymore
        // $this->setOrganizations();
    }

    public function search(): void
    {
        $this->searchQuery = str($this->searchQuery)->isNotEmpty()
            ? $this->searchQuery : null;
        // Remove this since we don't need to set organizations anymore
        // $this->setOrganizations();
    }

    public function updatingSearchQuery(): void
    {
        $this->resetPage();
    }

    public function setType($key): void
    {
        $this->resetPage();
        $this->selected = $key;
        if ($key == 'all') {
            $this->main = null;
            // Remove this since we don't need to set organizations anymore
            // $this->setOrganizations();
            return;
        }
        $this->main = $key;
        // Remove this since we don't need to set organizations anymore
        // $this->setOrganizations();
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

    public function getOrganizations(): LengthAwarePaginator
    {
        return Organization::query()
            ->latest()
            ->when(str($this->searchQuery)->isNotEmpty(), function ($query) {
                return $query->where('name_ar', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('name_en', 'like', '%' . $this->searchQuery . '%');
            })
            ->when($this->main != null, function ($query) {
                return $query->where('organization_type_id', $this->main);
            })
            ->paginate(20);
    }

    public function render(): View
    {
        return view('livewire.pages.organizations-page', [
            'organizations' => $this->getOrganizations(),
        ]);
    }
}
