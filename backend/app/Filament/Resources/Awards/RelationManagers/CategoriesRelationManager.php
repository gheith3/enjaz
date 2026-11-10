<?php

namespace App\Filament\Resources\Awards\RelationManagers;

use App\Filament\Support\Translatable;
use App\Models\AwardCategory;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/** Branches, prizes (edition types), competition categories and fields of an award. */
class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';

    protected static ?string $title = 'الفروع والفئات والمجالات';

    protected static ?string $modelLabel = 'بند';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('kind')->label('النوع')->options(AwardCategory::KINDS)->required()->live(),
            TextInput::make('key')->label('المعرّف')->required()->alphaDash()->maxLength(30)->extraInputAttributes(['dir' => 'ltr'])
                ->helperText('فروع الثقافة: culture, arts, lit (لها أيقونات). الدورات: oman, arab. الفئات: ind, org.'),
            Translatable::text('name', 'الاسم'),
            Translatable::textarea('hint', 'وصف قصير', rows: 2),
            Translatable::text('medal', 'الوسام (لنوع الدورة)', required: false),
            Translatable::text('basis', 'أساس المنح (لنوع الدورة)', required: false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([
                TextColumn::make('name')->label('الاسم'),
                TextColumn::make('kind')->label('النوع')->formatStateUsing(fn (string $state) => AwardCategory::KINDS[$state] ?? $state)->badge(),
                TextColumn::make('key')->label('المعرّف'),
            ])
            ->filters([SelectFilter::make('kind')->label('النوع')->options(AwardCategory::KINDS)])
            ->headerActions([CreateAction::make()])->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
