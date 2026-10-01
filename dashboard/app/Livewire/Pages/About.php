<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\News;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Settings\AboutSettings;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.site')]
class About extends Component
{
    public function render()
    {
        $settings = app(AboutSettings::class);

        $testimonials = Testimonial::query()->published()->ordered()->get()
            ->map(fn (Testimonial $testimonial) => [
                'img' => $testimonial->photo_path ? Storage::url($testimonial->photo_path) : null,
                'text' => $testimonial->t('quote'),
                'name' => implode(' — ', array_filter([
                    $testimonial->t('person_name'),
                    $testimonial->t('position'),
                ])),
            ])
            ->values();

        $teamMembers = TeamMember::query()->published()->ordered()->get();

        $newsItems = News::query()->published()
            ->where('show_on_about', true)
            ->latest('published_at')
            ->get()
            ->map(fn (News $news) => [
                'img' => $news->cover_image_path ? Storage::url($news->cover_image_path) : null,
                'title' => $news->t('title'),
                'date' => $news->published_at?->format('j M Y'),
                'url' => route('news.show', $news),
            ])
            ->values();

        return view('livewire.pages.about', [
            'settings' => $settings,
            'testimonials' => $testimonials,
            'teamMembers' => $teamMembers,
            'newsItems' => $newsItems,
        ]);
    }
}
