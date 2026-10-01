<?php

namespace App\Livewire\Pages;

use App\Models\Achievement;
use App\Models\AchievementMainType;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Achievements extends Component
{
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public ?string $type = null;

    #[Url]
    public ?string $scope = null;

    public function render()
    {
        $types = AchievementMainType::query()->published()->ordered()->get();

        $pillItems = collect([['value' => null, 'label' => __('site.all'), 'icon' => null]])
            ->concat($types->map(fn (AchievementMainType $type) => [
                'value' => $type->slug,
                'label' => $type->t('title'),
                'icon' => $type->icon_path,
            ]))
            ->all();

        $achievements = Achievement::query()->published()
            ->with(['achievementMainType', 'subAchievementType'])
            ->when($this->type, fn ($query) => $query->whereHas(
                'achievementMainType',
                fn ($typeQuery) => $typeQuery->where('slug', $this->type)
            ))
            ->when($this->scope, fn ($query) => $query->whereHas(
                'achievementScope',
                fn ($scopeQuery) => $scopeQuery->where('slug', $this->scope)
            ))
            ->when(trim($this->q) !== '', function ($query) {
                $term = '%'.trim($this->q).'%';

                $query->where(fn ($search) => $search
                    ->where('title_ar', 'like', $term)
                    ->orWhere('title_en', 'like', $term)
                    ->orWhere('excerpt_ar', 'like', $term)
                    ->orWhere('excerpt_en', 'like', $term));
            })
            ->ordered()
            ->latest('achieved_at')
            ->paginate(12)
            ->withQueryString();

        return view('livewire.pages.achievements', [
            'pillItems' => $pillItems,
            'achievements' => $achievements,
        ])->layout('layouts.site');
    }
}
