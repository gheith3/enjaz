<?php

namespace App\Filament\Resources\LabItems\Schemas;

use App\Filament\Support\MediaSlot;
use App\Filament\Support\Publishing;
use App\Filament\Support\Translatable;
use App\Models\LabItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class LabItemForm
{
    private const TOOLBAR = [['bold', 'italic', 'link'], ['h2', 'bulletList', 'orderedList', 'blockquote'], ['attachFiles'], ['undo', 'redo']];

    public static function configure(Schema $schema): Schema
    {
        $inv = fn (Get $get) => ($get('kind') ?? 'invention') === 'invention';

        return $schema->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('الأساسية')->icon('heroicon-o-beaker')->columns(2)->schema([
                    Select::make('kind')->label('النوع')->options(LabItem::KINDS)->required()->default('invention')->live(),
                    TextInput::make('slug')->label('المعرّف في الرابط')->required()->unique(ignoreRecord: true)->alphaDash()->maxLength(100)
                        ->notIn(array_values(LabItem::SEGMENTS))->extraInputAttributes(['dir' => 'ltr']),
                    Translatable::text('title', 'العنوان'),
                    Translatable::text('short', 'عنوان مختصر (في البطاقات وقاعة العرض)', required: false)->visible($inv),
                    Translatable::text('who', 'المبتكر، أو الباحث الرئيسي للمنشور (في البطاقات)', required: false),
                    Translatable::text('authors', 'كل المؤلفين (يظهر في الصفحة وفي المرجع)', required: false)->hidden($inv),
                    Select::make('achiever_id')->label('ملف المنجز في الموقع (اختياري)')->relationship('achiever', 'name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->searchable()->preload(),
                    Select::make('organization_id')->label('المؤسسة (اختياري)')->relationship('organization', 'name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->searchable()->preload(),
                    Translatable::text('org_label', 'اسم الجهة إن لم تكن مؤسسة في الموقع', required: false),
                    Translatable::text('field', 'المجال (في المنشورات يُستخدم لفرز صفحة «كل المنشورات»)', required: false),
                    DatePicker::make('happened_at')->label('التاريخ')->native(false)->required(),
                    Select::make('date_precision')->label('دقة التاريخ')->options(['month' => 'شهر وسنة', 'day' => 'يوم كامل', 'year' => 'سنة فقط'])->default('month')->required(),
                    ...Publishing::fields(),
                ]),
                Tab::make('الميدالية والمعرض')->icon('heroicon-o-trophy')->columns(2)->visible($inv)->schema([
                    Select::make('medal')->label('الميدالية')->options(LabItem::MEDALS),
                    TextInput::make('event')->label('المعرض (اسم الفرز، مثل: ITEX ماليزيا)')->maxLength(40)
                        ->helperText('العناصر التي لها الاسم نفسه تُجمع تحت زر فرز واحد في صفحة «كل الابتكارات».'),
                    Translatable::text('event_label', 'المعرض كما يظهر في البطاقة (مثل: ITEX 2026)', required: false),
                    Translatable::text('place', 'المكان (مثل: كوالالمبور، ماليزيا)', required: false),
                ]),
                Tab::make('النص')->icon('heroicon-o-document-text')->schema([
                    Translatable::textarea('lead', 'الفقرة الأولى (بخط أكبر)', rows: 3)->visible($inv),
                    Translatable::textarea('summary', 'ملخص البحث', rows: 5)->hidden($inv),
                    Grid::make(2)->hidden($inv)->schema([
                        Translatable::text('finding_value', 'النتيجة البارزة (رقم قصير، مثل: ٩٥٪)', required: false)->columnSpanFull(),
                        Translatable::textarea('finding_text', 'شرح النتيجة', rows: 2)->columnSpanFull(),
                        Translatable::text('journal', 'المجلة / جهة النشر', required: false)->columnSpanFull(),
                        Translatable::text('pub_type', 'نوع المنشور (مثل: بحث منشور)', required: false)->columnSpanFull(),
                    ]),
                    Grid::make(2)->schema([
                        RichEditor::make('body.ar')->label('نص القصة (للمنشور: نص إضافي اختياري)')->toolbarButtons(self::TOOLBAR)
                            ->fileAttachmentsDisk(config('media-library.disk_name'))->fileAttachmentsDirectory('media/lab')->fileAttachmentsVisibility('public'),
                        RichEditor::make('body.en')->label('النص (English)')->toolbarButtons(self::TOOLBAR)->extraInputAttributes(['dir' => 'ltr'])
                            ->fileAttachmentsDisk(config('media-library.disk_name'))->fileAttachmentsDirectory('media/lab')->fileAttachmentsVisibility('public'),
                    ]),
                ]),
                Tab::make('الصورة')->icon('heroicon-o-photo')->schema([
                    MediaSlot::make('cover', 'الصورة الرئيسية'),
                    Toggle::make('cover_is_illustrative')->label('صورة تعبيرية (يظهر عليها «صورة تعبيرية»)'),
                    Grid::make(2)->schema([
                        TextInput::make('credit_text')->label('مصدر الصور')->maxLength(255),
                        TextInput::make('credit_url')->label('رابط مصدر الصور')->url()->maxLength(2048)->extraInputAttributes(['dir' => 'ltr']),
                    ]),
                ]),
            ]),
        ]);
    }
}
