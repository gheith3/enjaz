<?php

namespace App\Filament\Resources\Pages\RelationManagers;

use App\Filament\Resources\PageSections\PageSectionResource;
use App\Models\PageSection;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

/**
 * Order and visibility of a page's sections. The list of possible sections is fixed in code
 * (config/site.php); sections are added by `php artisan site:sync`.
 */
class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'أقسام الصفحة';

    protected static ?string $modelLabel = 'قسم';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('اسحب الأقسام لترتيبها، وأوقف ما لا تريد عرضه.')
            ->reorderable('sort_order', fn () => ! config('site.pages.'.$this->getOwnerRecord()->key.'.fixed_order'))
            ->defaultSort('sort_order')
            ->paginated(false)
            ->columns([
                TextColumn::make('label')->label('القسم')->state(fn (PageSection $record) => $record->label()),
                ToggleColumn::make('is_enabled')->label('ظاهر'),
                IconColumn::make('ready')->label('جاهز في الموقع')->boolean()
                    ->state(fn (PageSection $record) => $record->isRenderable())
                    ->tooltip('يظهر القسم في الموقع عند اكتمال بنائه.'),
            ])
            ->recordActions([
                Action::make('edit')->label('النصوص والعناصر')->icon('heroicon-o-pencil-square')
                    ->url(fn (PageSection $record) => PageSectionResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
