<?php

namespace App\Support\Site;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\News;
use Illuminate\Support\Collection;

/**
 * «إنجازات المؤسسة» on an institution's page and its achievements page: the achievements linked to the
 * organization (to it or to its people, from Organization::allAchievements()) and its published news, as one
 * list, newest first.
 */
class OrganizationFeed
{
    /**
     * @param  Collection<int, array{achievement: Achievement, type: string}>  $achievements
     * @param  Collection<int, News>  $news
     * @return Collection<int, array{kind: string, url: string, title: string, image: ?string, focal: string, date: ?string, label: string, who: ?string, tag: ?string}>
     */
    public static function of(Collection $achievements, Collection $news): Collection
    {
        $rows = $achievements->map(function (array $row) {
            /** @var Achievement $achievement */
            $achievement = $row['achievement'];
            $achiever = $achievement->primaryAchiever();
            $cover = $achievement->image('cover');

            return [
                'kind' => 'achievement',
                'url' => $achievement->url(),
                'title' => (string) $achievement->title,
                'image' => $achievement->imageUrl('cover', 'card') ?? $achiever?->imageUrl('portrait', 'card'),
                'focal' => $cover ? Achievement::focal($cover) : Achiever::focal($achiever?->image('portrait')),
                'date' => $achievement->achieved_at?->toDateString(),
                'label' => $achievement->dateLabel(),
                'who' => $achiever?->name,
                'tag' => $achievement->disciplineLabel() ?: null,
            ];
        });

        $items = $news->map(fn (News $item) => [
            'kind' => 'news',
            'url' => $item->url(),
            'title' => (string) $item->title,
            'image' => $item->imageUrl('cover', 'card'),
            'focal' => News::focal($item->image('cover')),
            'date' => $item->happened_at?->toDateString(),
            'label' => $item->dateLabel(),
            'who' => null,
            'tag' => null,
        ]);

        return $rows->concat($items)->sortByDesc(fn (array $item) => $item['date'] ?? '')->values();
    }
}
