<?php

declare(strict_types=1);

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use App\Models\Promotion;
use App\Models\PromotionItem;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('activities.tabs.all'))
                ->icon('heroicon-o-rectangle-stack'),

            'today' => Tab::make(__('activities.tabs.today'))
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', today()))
                ->badge(fn () => $this->getModel()::whereDate('created_at', today())->count())
                ->badgeColor('info'),

            'created' => Tab::make(__('activities.tabs.created'))
                ->icon('heroicon-o-plus-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event', 'created'))
                ->badge(fn () => $this->getModel()::where('event', 'created')->count())
                ->badgeColor('success'),

            'updated' => Tab::make(__('activities.tabs.updated'))
                ->icon('heroicon-o-pencil-square')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event', 'updated'))
                ->badge(fn () => $this->getModel()::where('event', 'updated')->count())
                ->badgeColor('info'),

            'deleted' => Tab::make(__('activities.tabs.deleted'))
                ->icon('heroicon-o-trash')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event', 'deleted'))
                ->badge(fn () => $this->getModel()::where('event', 'deleted')->count())
                ->badgeColor('danger'),

            // 'promotions' => Tab::make('Promotions')
            //     ->icon('heroicon-o-megaphone')
            //     ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('subject_type', [
            //         Promotion::class,
            //         PromotionItem::class,
            //     ]))
            //     ->badge(fn () => $this->getModel()::whereIn('subject_type', [
            //         Promotion::class,
            //         PromotionItem::class,
            //     ])->count())
            //     ->badgeColor('warning'),
        ];
    }
}
