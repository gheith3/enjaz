<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Filament\Support\MediaSlot;
use App\Filament\Support\Translatable;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ArticleForm
{
    private const TOOLBAR = [['bold', 'italic', 'link'], ['h2', 'bulletList', 'orderedList'], ['undo', 'redo']];

    public static function configure(Schema $schema): Schema
    {
        $name = fn ($record) => $record->name;

        return $schema->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('الأساسية')->icon('heroicon-o-pencil-square')->columns(2)->schema([
                    Translatable::text('title', 'العنوان'),
                    TextInput::make('slug')->label('المعرّف في الرابط')->required()->unique(ignoreRecord: true)->alphaDash()->maxLength(100)
                        ->extraInputAttributes(['dir' => 'ltr']),
                    Select::make('writer_id')->label('الكاتب')->relationship('writer', 'name')->getOptionLabelFromRecordUsing($name)->searchable()->preload(),
                    Select::make('article_category_id')->label('التصنيف')->relationship('category', 'name')->getOptionLabelFromRecordUsing($name)->preload(),
                    Select::make('issue_id')->label('العدد')->relationship('issue', 'number')->preload(),
                    Translatable::textarea('dek', 'السطر التعريفي تحت العنوان', rows: 2),
                    Toggle::make('show_on_home')->label('يظهر في الصفحة الرئيسية'),
                    Toggle::make('is_published')->label('منشور')->helperText('غير المنشور يظهر في لوحة التحكم فقط.'),
                    DateTimePicker::make('published_at')->label('تاريخ المقال')->native(false)->required()->default(now())
                        ->helperText('يظهر تحت اسم الكاتب. تاريخ لاحق = يُنشر في موعده.'),
                    Placeholder::make('read_minutes')->label('مدة القراءة')->content(fn ($record) => $record ? $record->read_minutes.' دقائق (تُحسب من النص عند الحفظ)' : 'تُحسب من النص عند الحفظ'),
                ]),
                Tab::make('النص')->icon('heroicon-o-document-text')->schema([
                    Translatable::textarea('lead', 'الفقرة الأولى (بخط أكبر)', rows: 4),
                    Grid::make(2)->schema([
                        RichEditor::make('body.ar')->label('النص')->toolbarButtons(self::TOOLBAR)
                            ->helperText('العناوين (H2) تقسم المقال. الاقتباس يوضع بعد أول فقرة من القسم الأول، والخلاصة قبل العنوان الثاني.'),
                        RichEditor::make('body.en')->label('النص (English)')->toolbarButtons(self::TOOLBAR)->extraInputAttributes(['dir' => 'ltr']),
                    ]),
                    Translatable::textarea('pull_quote', 'اقتباس بارز', rows: 2),
                    Section::make('خلاصة المقال')->columns(2)->schema([
                        Translatable::text('box_title', 'العنوان', required: false),
                        Repeater::make('box_items.ar')->label('البنود')->simple(TextInput::make('item')->required())->defaultItems(0)->reorderable(),
                        Repeater::make('box_items.en')->label('البنود (English)')->simple(TextInput::make('item')->required())->defaultItems(0)->reorderable(),
                    ]),
                    Grid::make(2)->schema([
                        TagsInput::make('tags.ar')->label('الوسوم')->helperText('بدون #. الضغط على الوسم يبحث عنه في المقالات.'),
                        TagsInput::make('tags.en')->label('الوسوم (English)'),
                    ]),
                ]),
                Tab::make('الصورة')->icon('heroicon-o-photo')->schema([
                    MediaSlot::make('cover', 'صورة الغلاف'),
                    Translatable::text('cover_caption', 'تعليق الصورة', required: false),
                ]),
            ]),
        ]);
    }
}
