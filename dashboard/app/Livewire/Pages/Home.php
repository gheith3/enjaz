<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\Achievement;
use App\Models\AchievementMainType;
use App\Models\PublicFigure;
use App\Settings\HomeSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.site')]
class Home extends Component
{
    public function render(HomeSettings $settings)
    {
        $locale = app()->getLocale();

        $quote = $locale === 'en' && $settings->quote_en !== ''
            ? $settings->quote_en
            : $settings->quote_ar;

        $quoteAuthor = $locale === 'en' && $settings->quote_author_en !== ''
            ? $settings->quote_author_en
            : $settings->quote_author_ar;

        $heroImageUrl = $settings->hero_image !== ''
            ? Storage::url($settings->hero_image)
            : null;

        $carouselItems = PublicFigure::query()->published()
            ->where('in_home_carousel', true)
            ->ordered()
            ->get()
            ->map(fn (PublicFigure $figure) => [
                'src' => Storage::url($figure->tile_image_path ?: $figure->image_path),
                'alt' => $figure->t('name'),
                'url' => route('achievers.show', $figure),
            ])
            ->values();

        $stats = Cache::remember('home.stats', now()->addHour(), fn () => [
            'achievers' => PublicFigure::query()->published()->count(),
            'achievements' => Achievement::query()->published()->count(),
            'fields' => AchievementMainType::query()->published()->count(),
        ]);

        $latestAchievements = Achievement::query()->published()
            ->with(['achievementMainType', 'subAchievementType'])
            ->latest('achieved_at')
            ->take(3)
            ->get();

        $featuredFigure = PublicFigure::query()->published()
            ->where('is_featured', true)
            ->ordered()
            ->first();

        $featuredBannerUrl = $featuredFigure?->banner_image_path
            ? Storage::url($featuredFigure->banner_image_path)
            : null;

        $featuredExcerpt = $featuredFigure
            ? Str::limit($featuredFigure->t('description'), 450)
            : null;

        $categoryRows = AchievementMainType::query()->published()
            ->where('show_on_home', true)
            ->ordered()
            ->take(3)
            ->with(['publicFigures' => fn ($query) => $query->published()->ordered()->take(4)])
            ->get()
            ->filter(fn (AchievementMainType $type) => $type->publicFigures->isNotEmpty())
            ->values();

        return view('livewire.pages.home', [
            'quote' => $quote,
            'quoteAuthor' => $quoteAuthor,
            'heroImageUrl' => $heroImageUrl,
            'carouselItems' => $carouselItems,
            'stats' => $stats,
            'latestAchievements' => $latestAchievements,
            'featuredFigure' => $featuredFigure,
            'featuredBannerUrl' => $featuredBannerUrl,
            'featuredExcerpt' => $featuredExcerpt,
            'categoryRows' => $categoryRows,
            'metaDescription' => $quote !== '' ? Str::limit($quote, 160) : __('site.meta.description'),
        ]);
    }
}
