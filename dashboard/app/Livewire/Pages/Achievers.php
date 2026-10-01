<?php

namespace App\Livewire\Pages;

use App\Models\AchievementMainType;
use App\Models\PublicFigure;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.site')]
class Achievers extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: '')]
    public string $type = '';

    public function paginationView()
    {
        return 'components.pagination';
    }

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $figures = PublicFigure::query()
            ->published()
            ->with('achievementMainType')
            ->when($this->type !== '', fn ($query) => $query->whereHas(
                'achievementMainType',
                fn ($type) => $type->where('slug', $this->type)
            ))
            ->when($this->q !== '', fn ($query) => $query->where(
                fn ($search) => $search
                    ->whereLike('name_ar', '%'.$this->q.'%')
                    ->orWhereLike('name_en', '%'.$this->q.'%')
            ))
            ->ordered()
            ->paginate(12);

        $pills = collect([['label' => __('site.all'), 'value' => null, 'icon' => null]])
            ->merge(
                AchievementMainType::query()->published()->ordered()->get()
                    ->map(fn (AchievementMainType $type) => [
                        'label' => $type->t('title'),
                        'value' => $type->slug,
                        'icon' => $type->icon_path,
                    ])
            );

        return view('livewire.pages.achievers', [
            'figures' => $figures,
            'pills' => $pills,
        ]);
    }
}
