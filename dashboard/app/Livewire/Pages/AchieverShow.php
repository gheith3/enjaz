<?php

namespace App\Livewire\Pages;

use App\Models\AchievementScope;
use App\Models\PublicFigure;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.site')]
class AchieverShow extends Component
{
    use WithPagination;

    public PublicFigure $figure;

    #[Url(as: 'scope', except: '')]
    public string $scope = '';

    #[Url(except: 'desc')]
    public string $sort = 'desc';

    public function mount(PublicFigure $publicFigure): void
    {
        $this->figure = PublicFigure::query()
            ->published()
            ->with('achievementMainType')
            ->whereKey($publicFigure->getKey())
            ->firstOrFail();
    }

    public function paginationView()
    {
        return 'components.pagination';
    }

    public function updatingScope(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function toggleSort(): void
    {
        $this->sort = $this->sort === 'desc' ? 'asc' : 'desc';
        $this->resetPage();
    }

    public function render()
    {
        $base = $this->figure->achievements()
            ->published()
            ->with(['achievementScope', 'achievementMainType', 'subAchievementType']);

        $internationalCount = (clone $base)
            ->whereHas('achievementScope', fn ($q) => $q->where('slug', 'international'))
            ->count();

        $localCount = (clone $base)
            ->whereHas('achievementScope', fn ($q) => $q->where('slug', 'local'))
            ->count();

        $scopeTabs = collect([['label' => __('site.all'), 'value' => null]])
            ->merge(
                AchievementScope::query()->published()->ordered()->get()
                    ->map(fn ($scope) => ['label' => $scope->t('title'), 'value' => $scope->slug])
            );

        $achievements = $base
            ->when($this->scope !== '', fn ($q) => $q->whereHas(
                'achievementScope',
                fn ($s) => $s->where('slug', $this->scope)
            ))
            ->orderBy('achieved_at', $this->sort === 'asc' ? 'asc' : 'desc')
            ->paginate(9)
            ->withQueryString();

        $mediaItems = $this->figure->videoMedia()->published()->latest()->take(3)->get()
            ->map(fn ($video) => [
                'kind' => 'video',
                'title' => $video->t('title'),
                'image' => $video->cover_image_path,
                'url' => route('media.videos'),
                'created_at' => $video->created_at,
            ])
            ->concat(
                $this->figure->imageMedia()->published()->latest()->take(3)->get()
                    ->map(fn ($album) => [
                        'kind' => 'image',
                        'title' => $album->t('title'),
                        'image' => $album->images[0] ?? null,
                        'url' => route('media.images-albums.show', $album),
                        'created_at' => $album->created_at,
                    ])
            )
            ->sortByDesc('created_at')
            ->take(3)
            ->values();

        $related = PublicFigure::query()
            ->published()
            ->with('achievementMainType')
            ->where('achievement_main_type_id', $this->figure->achievement_main_type_id)
            ->whereKeyNot($this->figure->getKey())
            ->ordered()
            ->take(5)
            ->get();

        return view('livewire.pages.achiever-show', [
            'internationalCount' => $internationalCount,
            'localCount' => $localCount,
            'scopeTabs' => $scopeTabs,
            'mediaItems' => $mediaItems,
            'achievements' => $achievements,
            'related' => $related,
        ]);
    }
}
