<?php

namespace App\Filament\Resources\PageSections\RelationManagers;

use App\Filament\Support\MediaSlot;
use App\Filament\Support\SectionSettings;
use App\Filament\Support\Translatable;
use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Article;
use App\Models\Award;
use App\Models\LabItem;
use App\Models\News;
use App\Models\Organization;
use App\Models\PageSection;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/** Records picked by hand for a section (e.g. the people of the home sports row), each with its own image and texts. */
class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'عناصر القسم';

    protected static ?string $modelLabel = 'عنصر';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof PageSection && array_key_exists($ownerRecord->key, SectionSettings::ITEMS);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        $named = fn ($attr) => fn ($record) => $record->{$attr};

        return $schema->components([
            MorphToSelect::make('itemable')->label('السجل المرتبط (اختياري: يُستخدم اسمه ورابطه)')->searchable()->preload()->columnSpanFull()
                ->types([
                    Type::make(Achiever::class)->label('منجز')->titleAttribute('name')->getOptionLabelFromRecordUsing($named('name')),
                    Type::make(Achievement::class)->label('إنجاز')->titleAttribute('title')->getOptionLabelFromRecordUsing($named('title')),
                    Type::make(Award::class)->label('جائزة')->titleAttribute('name')->getOptionLabelFromRecordUsing($named('name')),
                    Type::make(Organization::class)->label('مؤسسة')->titleAttribute('name')->getOptionLabelFromRecordUsing($named('name')),
                    Type::make(Article::class)->label('مقال')->titleAttribute('title')->getOptionLabelFromRecordUsing($named('title')),
                    Type::make(News::class)->label('خبر')->titleAttribute('title')->getOptionLabelFromRecordUsing($named('title')),
                    Type::make(LabItem::class)->label('عنصر مختبر')->titleAttribute('title')->getOptionLabelFromRecordUsing($named('title')),
                ]),
            Translatable::text('title', 'العنوان / الاسم (فارغ = اسم السجل)', required: false),
            Translatable::text('label', 'الوصف القصير', required: false),
            Translatable::textarea('text', 'النص', rows: 2),
            Translatable::text('meta', 'بيانات إضافية (مفصولة بـ |)', required: false)->statePath('settings'),
            TextInput::make('url')->label('رابط (فارغ = صفحة السجل)')->maxLength(2048)->extraInputAttributes(['dir' => 'ltr']),
            Translatable::text('link_label', 'نص الرابط', required: false),
            Toggle::make('settings.highlight')->label('مميّز (مثل: شهادة رسمية)'),
            MediaSlot::make('image', 'الصورة'),
            MediaSlot::make('image2', 'صورة ثانية (إن كان القسم يستخدمها)'),
            MediaSlot::make('icon', 'الأيقونة (SVG، إن كان القسم يستخدمها)', focal: false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->description(fn () => SectionSettings::ITEMS[$this->getOwnerRecord()->key] ?? null)
            ->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->collection('image')->label('')->height(48),
                TextColumn::make('title')->label('الاسم')->state(fn ($record) => $record->displayTitle()),
                TextColumn::make('label')->label('الوصف'),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
