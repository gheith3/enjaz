<?php

namespace App\Livewire\Pages;

use App\Models\Organization;
use App\Models\OrganizationType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.site')]
class Institutions extends Component
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public string $type = '';

    public function render()
    {
        $types = OrganizationType::query()->published()->ordered()->get();

        $pills = [['value' => null, 'label' => __('site.all')]];

        foreach ($types as $type) {
            $pills[] = ['value' => $type->slug, 'label' => $type->t('title')];
        }

        $organizations = Organization::query()
            ->published()
            ->with('organizationType')
            ->when($this->type !== '', fn ($query) => $query->whereHas(
                'organizationType',
                fn ($query) => $query->where('slug', $this->type)
            ))
            ->when($this->q !== '', fn ($query) => $query->where(
                fn ($query) => $query
                    ->where('name_ar', 'like', "%{$this->q}%")
                    ->orWhere('name_en', 'like', "%{$this->q}%")
            ))
            ->ordered()
            ->paginate(12)
            ->appends(array_filter(
                request()->except('page'),
                fn ($value) => $value !== null && $value !== ''
            ));

        return view('livewire.pages.institutions', [
            'pills' => $pills,
            'organizations' => $organizations,
        ]);
    }
}
