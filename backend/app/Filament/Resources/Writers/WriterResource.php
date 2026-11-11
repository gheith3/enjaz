<?php

namespace App\Filament\Resources\Writers;

use App\Filament\Resources\Writers\Pages\CreateWriter;
use App\Filament\Resources\Writers\Pages\EditWriter;
use App\Filament\Resources\Writers\Pages\ListWriters;
use App\Filament\Support\ContentTable;
use App\Filament\Support\MediaSlot;
use App\Filament\Support\Publishing;
use App\Filament\Support\Translatable;
use App\Filament\Support\TranslatableColumn;
use App\Models\Writer;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

/** أقلام إنجازات (writers.html). */
class WriterResource extends Resource
{
    protected static ?string $model = Writer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'المجلة';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'كاتب';

    protected static ?string $pluralModelLabel = 'الكتّاب';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->columns(2)->schema([
                Translatable::text('name', 'الاسم'),
                TextInput::make('slug')->label('المعرّف (رابط بطاقته في صفحة الأقلام)')->required()->unique(ignoreRecord: true)->alphaDash()->maxLength(100)
                    ->extraInputAttributes(['dir' => 'ltr']),
                Translatable::text('role', 'الصفة (مثل: كاتب رأي)', required: false),
                Select::make('article_category_id')->label('التصنيف (تبويب صفحة الأقلام)')->relationship('category', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->preload(),
                Select::make('achiever_id')->label('ملف المنجز في الموقع (اختياري)')->relationship('achiever', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->searchable()->preload(),
                Translatable::textarea('bio', 'عن الكاتب (تحت مقالاته)', rows: 2),
                Toggle::make('is_featured')->label('قلم العدد'),
                Translatable::textarea('quote', 'اقتباس قلم العدد', rows: 2),
                ...Publishing::fields(),
                MediaSlot::make('photo', 'الصورة'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')->collection('photo')->conversion('card')->label('')->circular()->height(40),
                TextColumn::make('name')->label('الاسم')->searchable(query: TranslatableColumn::search('name')),
                TextColumn::make('category.name')->label('التصنيف')->badge(),
                TextColumn::make('articles_count')->counts('articles')->label('المقالات'),
                IconColumn::make('is_featured')->label('قلم العدد')->boolean(),
                ContentTable::publishedColumn(),
            ])
            ->filters(ContentTable::filters())
            ->recordActions([EditAction::make(), ContentTable::openOnSite('magazine.writers'), RestoreAction::make()])
            ->toolbarActions([ContentTable::bulkActions()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWriters::route('/'),
            'create' => CreateWriter::route('/create'),
            'edit' => EditWriter::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
