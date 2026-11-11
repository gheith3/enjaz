<?php

namespace App\Filament\Resources\Issues;

use App\Filament\Resources\Issues\Pages\ManageIssues;
use App\Filament\Support\Translatable;
use App\Models\Issue;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Issues of the magazine: the newest published one is shown on the articles page (mast, lead article, quote). */
class IssueResource extends Resource
{
    protected static ?string $model = Issue::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'المجلة';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'عدد';

    protected static ?string $pluralModelLabel = 'الأعداد';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('number')->label('رقم العدد')->numeric()->minValue(1)->required()->unique(ignoreRecord: true),
            DatePicker::make('issued_at')->label('تاريخ العدد (الشهر)')->native(false),
            Translatable::text('tagline', 'السطر تحت اسم المجلة', required: false),
            Select::make('lead_article_id')->label('مقال العدد')->relationship('leadArticle', 'title')
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->title)->searchable()->preload(),
            Translatable::textarea('quote', 'اقتباس العدد', rows: 2),
            Translatable::text('quote_cite', 'صاحب الاقتباس (مثل: بدر العلوي، من «…»)', required: false),
            Toggle::make('is_published')->label('منشور')->default(true)->helperText('أحدث عدد منشور يظهر في صفحة المقالات.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('number', 'desc')
            ->columns([
                TextColumn::make('number')->label('العدد'),
                TextColumn::make('issued_at')->label('التاريخ')->date('Y-m'),
                TextColumn::make('leadArticle.title')->label('مقال العدد')->limit(50),
                TextColumn::make('articles_count')->counts('articles')->label('المقالات'),
                ToggleColumn::make('is_published')->label('منشور'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageIssues::route('/')];
    }
}
