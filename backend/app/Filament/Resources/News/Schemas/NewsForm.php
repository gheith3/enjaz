<?php

namespace App\Filament\Resources\News\Schemas;

use App\Filament\Support\MediaSlot;
use App\Filament\Support\Publishing;
use App\Filament\Support\Translatable;
use App\Models\News;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class NewsForm
{
    private const TOOLBAR = [['bold', 'italic', 'link'], ['h3', 'bulletList', 'orderedList', 'blockquote'], ['undo', 'redo']];

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('الأساسية')->icon('heroicon-o-newspaper')->columns(2)->schema([
                    Translatable::text('title', 'العنوان'),
                    TextInput::make('slug')->label('المعرّف في الرابط')->required()->unique(ignoreRecord: true)->alphaDash()->maxLength(100)
                        ->extraInputAttributes(['dir' => 'ltr']),
                    Select::make('organization_id')->label('المؤسسة')->relationship('organization', 'name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->preload()->searchable()
                        ->default(fn () => request()->integer('organization') ?: null),
                    Select::make('news_category_id')->label('قسم الأخبار')->relationship('category', 'name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->preload()
                        ->helperText('أخبار القسم تُقرأ كاملة في صفحة الأخبار. أخبار المؤسسات لها صفحة خاصة.'),
                    Select::make('kind')->label('النوع')->options(News::KINDS)->required()->default('news'),
                    Translatable::text('tag', 'الوسم (مثل: شراكة، زيارة)', required: false),
                    DatePicker::make('happened_at')->label('التاريخ')->native(false),
                    Translatable::textarea('dek', 'السطر التعريفي تحت العنوان', rows: 2),
                    Translatable::textarea('summary', 'الملخص (في القوائم)', rows: 2),
                    Toggle::make('show_in_himam')->label('يظهر في «همم بلا حدود» (صفحة ملهمون من عُمان)'),
                    Toggle::make('show_on_home')->label('يظهر في الصفحة الرئيسية'),
                    ...Publishing::fields(),
                ]),
                Tab::make('النص')->icon('heroicon-o-document-text')->schema([
                    Grid::make(2)->schema([
                        RichEditor::make('body.ar')->label('النص')->toolbarButtons(self::TOOLBAR),
                        RichEditor::make('body.en')->label('النص (English)')->toolbarButtons(self::TOOLBAR)->extraInputAttributes(['dir' => 'ltr']),
                    ]),
                    Repeater::make('facts')->label('حقائق سريعة')->defaultItems(0)->addActionLabel('إضافة')->collapsible()
                        ->schema([Translatable::text('label', 'العنوان'), Translatable::text('value', 'القيمة')]),
                ]),
                Tab::make('الصورة')->icon('heroicon-o-photo')->schema([
                    MediaSlot::make('cover', 'صورة الخبر'),
                    Translatable::text('cover_caption', 'تعليق / مصدر الصورة (في صفحة الأخبار يظهر ملاحظةً تحت النص)', required: false),
                ]),
            ]),
        ]);
    }
}
