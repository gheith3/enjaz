<?php

namespace App\Filament\Resources\Organizations\RelationManagers;

use App\Filament\Resources\News\NewsResource;
use App\Models\News;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** News and achievements published by the organization (design org-news.html). */
class NewsRelationManager extends RelationManager
{
    protected static string $relationship = 'news';

    protected static ?string $title = 'أخبار المؤسسة';

    protected static ?string $modelLabel = 'خبر';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('happened_at', 'desc')
            ->columns([
                TextColumn::make('title')->label('العنوان')->limit(70),
                TextColumn::make('kind')->label('النوع')->formatStateUsing(fn (string $state) => News::KINDS[$state] ?? $state)->badge(),
                TextColumn::make('happened_at')->label('التاريخ')->date('Y-m-d'),
                IconColumn::make('is_published')->label('منشور')->boolean(),
            ])
            ->headerActions([
                Action::make('new')->label('خبر جديد')->icon('heroicon-o-plus')
                    ->url(fn () => NewsResource::getUrl('create', ['organization' => $this->getOwnerRecord()->getKey()])),
            ])
            ->recordActions([
                Action::make('edit')->label('تعديل')->icon('heroicon-o-pencil-square')->url(fn (News $r) => NewsResource::getUrl('edit', ['record' => $r])),
            ]);
    }
}
