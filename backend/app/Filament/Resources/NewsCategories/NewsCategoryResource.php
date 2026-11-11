<?php

namespace App\Filament\Resources\NewsCategories;

use App\Filament\Resources\NewsCategories\Pages\ManageNewsCategories;
use App\Filament\Support\Translatable;
use App\Models\NewsCategory;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Sections of the news page (news.html?category=…). */
class NewsCategoryResource extends Resource
{
    protected static ?string $model = NewsCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'التصنيفات';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'قسم أخبار';

    protected static ?string $pluralModelLabel = 'أقسام الأخبار';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::text('name', 'الاسم (عنوان صفحة الأخبار)'),
            Translatable::textarea('intro', 'النص تحت العنوان', rows: 2),
            TextInput::make('slug')->label('المعرّف في الرابط (news?category=…)')->required()->unique(ignoreRecord: true)->alphaDash()
                ->extraInputAttributes(['dir' => 'ltr']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->columns([
                TextColumn::make('name')->label('القسم'),
                TextColumn::make('news_count')->counts('news')->label('الأخبار'),
                TextColumn::make('slug')->label('المعرّف'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('open')->label('عرض')->icon('heroicon-o-arrow-top-right-on-square')->color('gray')
                    ->url(fn (NewsCategory $record) => $record->url(), shouldOpenInNewTab: true),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageNewsCategories::route('/')];
    }
}
