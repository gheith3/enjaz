<?php

namespace App\Filament\Resources\Fields;

use App\Filament\Resources\Fields\Pages\ManageFields;
use App\Filament\Support\Translatable;
use App\Filament\Support\TranslatableColumn;
use App\Models\Field;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class FieldResource extends Resource
{
    protected static ?string $model = Field::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'التصنيفات';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'مجال';

    protected static ?string $pluralModelLabel = 'المجالات';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::text('name', 'الاسم'),
            TextInput::make('slug')->label('المعرّف في الرابط')->required()->unique(ignoreRecord: true)
                ->alphaDash()->maxLength(60)->extraInputAttributes(['dir' => 'ltr']),
            Select::make('icon')->label('الأيقونة')->options(array_combine(Field::ICONS, Field::ICONS)),
            Select::make('card_tone')->label('لون البطاقة التعريفية')->options(['sports' => 'رياضي', 'culture' => 'ثقافي'])->required()->default('culture'),
            Toggle::make('is_active')->label('ظاهر في التصنيفات')->default(true),
            Translatable::textarea('description', 'الوصف (صفحة من نحن)'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->columns([
                ImageColumn::make('icon')->label('')->state(fn (Field $record) => $record->iconUrl())->height(24),
                TextColumn::make('name')->label('المجال')->searchable(query: TranslatableColumn::search('name')),
                TextColumn::make('achievers_count')->counts('achievers')->label('المنجزون'),
                TextColumn::make('achievements_count')->counts('achievements')->label('الإنجازات'),
                ToggleColumn::make('is_active')->label('ظاهر'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageFields::route('/')];
    }
}
