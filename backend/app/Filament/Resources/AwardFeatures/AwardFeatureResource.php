<?php

namespace App\Filament\Resources\AwardFeatures;

use App\Filament\Support\Translatable;
use App\Models\AwardFeature;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AwardFeatureResource extends Resource
{
    protected static ?string $model = AwardFeature::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 6;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'قسم إضافي';

    protected static ?string $pluralModelLabel = 'أقسام الجوائز الإضافية';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('award_id')->label('الجائزة')->relationship('award', 'name')->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->required(),
            TextInput::make('key')->label('المعرّف (للتنسيق: expo / ihya)')->alphaDash()->maxLength(20)->extraInputAttributes(['dir' => 'ltr']),
            Translatable::text('chip', 'الوسم (مثل: مسابقة خاصة أو التاريخ)', required: false),
            Translatable::text('title', 'العنوان'),
            Translatable::text('subtitle', 'سطر تحت العنوان (المكان)', required: false),
            Translatable::textarea('text', 'النص', rows: 4),
            Translatable::textarea('note', 'ملاحظة', rows: 2),
            Repeater::make('team')->label('الفريق')->defaultItems(0)->addActionLabel('إضافة اسم')->simple(TextInput::make('ar')->label('الاسم')->required()),
            TextInput::make('entries_count')->label('عدد المشاركات (يُضاف إلى أرقام الجائزة)')->numeric(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('award.name')->label('الجائزة'), TextColumn::make('title')->label('العنوان')])
            ->recordActions([EditAction::make()]);
    }

    public static function getRelations(): array
    {
        return [RelationManagers\ImagesRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAwardFeatures::route('/'),
            'create' => Pages\CreateAwardFeature::route('/create'),
            'edit' => Pages\EditAwardFeature::route('/{record}/edit'),
        ];
    }
}
