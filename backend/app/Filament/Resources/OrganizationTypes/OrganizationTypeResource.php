<?php

namespace App\Filament\Resources\OrganizationTypes;

use App\Filament\Resources\OrganizationTypes\Pages\ManageOrganizationTypes;
use App\Filament\Support\Translatable;
use App\Filament\Support\TranslatableColumn;
use App\Models\OrganizationType;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class OrganizationTypeResource extends Resource
{
    protected static ?string $model = OrganizationType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|UnitEnum|null $navigationGroup = 'التصنيفات';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'نوع مؤسسة';

    protected static ?string $pluralModelLabel = 'أنواع المؤسسات';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::text('name', 'الاسم (مثل: حكومي)'),
            Translatable::text('label', 'الوسم على البطاقة (مثل: مؤسسة حكومية)'),
            Translatable::text('group_label', 'عنوان المجموعة في القائمة (مثل: المؤسسات الحكومية)'),
            TextInput::make('slug')->label('المعرّف')->required()->unique(ignoreRecord: true)->alphaDash()->extraInputAttributes(['dir' => 'ltr']),
            Select::make('template')->label('تصميم صفحة المؤسسة')->options(OrganizationType::TEMPLATES)->required(),
            Select::make('css_class')->label('لون الوسم')->options(['t-gov' => 'حكومي', 't-mil' => 'عسكري', 't-pvt' => 'خاص', 't-edu' => 'أكاديمي', 't-sport' => 'رياضي']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->columns([
                TextColumn::make('name')->label('النوع')->searchable(query: TranslatableColumn::search('name')),
                TextColumn::make('template')->label('التصميم')->formatStateUsing(fn (string $state) => OrganizationType::TEMPLATES[$state] ?? $state)->badge(),
                TextColumn::make('organizations_count')->counts('organizations')->label('المؤسسات'),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageOrganizationTypes::route('/')];
    }
}
