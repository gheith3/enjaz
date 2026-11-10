<?php

namespace App\Filament\Resources\AwardEditions;

use App\Filament\Support\MediaSlot;
use App\Filament\Support\Translatable;
use App\Models\AwardCategory;
use App\Models\AwardEdition;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AwardEditionResource extends Resource
{
    protected static ?string $model = AwardEdition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 5;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'دورة جائزة';

    protected static ?string $pluralModelLabel = 'دورات الجوائز';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->schema([
                Select::make('award_id')->label('الجائزة')->relationship('award', 'name')->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->required()->live()->columnSpan(2),
                Select::make('type')->label('النوع')->options(AwardEdition::TYPES)->placeholder('—'),
                TextInput::make('number')->label('رقم الدورة')->numeric(),
                TextInput::make('year')->label('السنة')->required()->maxLength(20),
                TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0),
            ]),
            Translatable::text('title', 'العنوان / الموضوع', required: false),
            Translatable::text('place', 'الموقع', required: false),
            Grid::make(3)->schema([
                TextInput::make('entries_count')->label('عدد المشاركات')->numeric(),
                TextInput::make('finals_count')->label('عدد المتأهلين للنهائي')->numeric(),
                TextInput::make('area')->label('مساحة الموقع (م²)')->maxLength(20),
            ]),
            Translatable::textarea('brief', 'وصف المسابقة', rows: 2),
            Translatable::textarea('note', 'ملاحظة', rows: 2),
            Grid::make(2)->schema([
                Translatable::text('jury', 'رئيس التحكيم', required: false),
                Translatable::text('exec', 'الجهة المنفذة', required: false),
                Translatable::text('ceremony', 'موقع التتويج', required: false),
            ]),
            Translatable::textarea('jury_all', 'لجنة التحكيم كاملة', rows: 2),
            Select::make('withheld')->label('فروع حُجبت في هذه الدورة')->multiple()
                ->options(fn (Get $get) => AwardCategory::query()->where('award_id', $get('award_id'))->where('kind', 'branch')->get()->mapWithKeys(fn ($c) => [$c->key => $c->name])),
            Section::make('الدورة الجارية')->collapsible()->schema([
                Toggle::make('is_current')->label('هذه هي الدورة الجارية (لم يُعلن فائزوها)'),
                Translatable::text('status', 'حالة الدورة (مثل: يُرتقب إعلان الفائزين...)', required: false),
                Repeater::make('open_fields')->label('المجالات المطروحة')->defaultItems(0)->columns(2)->addActionLabel('إضافة مجال')->schema([
                    Select::make('category')->label('الفرع')->required()
                        ->options(fn (Get $get) => AwardCategory::query()->where('award_id', $get('../../award_id'))->where('kind', 'branch')->get()->mapWithKeys(fn ($c) => [$c->key => $c->name])),
                    Translatable::text('text', 'المجال'),
                ]),
            ]),
            Section::make('الخريطة (جائزة معمارية)')->collapsible()->collapsed()->columns(3)->schema([
                Translatable::text('map_label', 'اسم الموقع على الخريطة', required: false),
                TextInput::make('map_x')->label('س (0–104)')->numeric(),
                TextInput::make('map_y')->label('ص (0–137)')->numeric(),
            ]),
            Toggle::make('is_partial')->label('الفائزون موثّقون جزئيًا'),
            MediaSlot::make('cover', 'صورة الدورة (التعليق والمصدر في معرض الصور)'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort_order')
            ->columns([
                TextColumn::make('award.name')->label('الجائزة'),
                TextColumn::make('number')->label('الرقم'),
                TextColumn::make('year')->label('السنة'),
                TextColumn::make('winners_count')->counts('winners')->label('الفائزون'),
            ])
            ->filters([SelectFilter::make('award_id')->label('الجائزة')->relationship('award', 'name')->getOptionLabelFromRecordUsing(fn ($record) => $record->name)])
            ->recordActions([EditAction::make()]);
    }

    public static function getRelations(): array
    {
        return [RelationManagers\WinnersRelationManager::class, RelationManagers\GalleryRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAwardEditions::route('/'),
            'create' => Pages\CreateAwardEdition::route('/create'),
            'edit' => Pages\EditAwardEdition::route('/{record}/edit'),
        ];
    }
}
