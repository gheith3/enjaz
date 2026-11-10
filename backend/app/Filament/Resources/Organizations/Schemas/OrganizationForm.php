<?php

namespace App\Filament\Resources\Organizations\Schemas;

use App\Filament\Support\MediaSlot;
use App\Filament\Support\Publishing;
use App\Filament\Support\Translatable;
use App\Models\Organization;
use App\Models\OrganizationType;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class OrganizationForm
{
    /** Template of the selected type (institution | university | federation), to show only the relevant fields. */
    private static function template(Get $get): string
    {
        return OrganizationType::query()->whereKey($get('organization_type_id'))->value('template') ?? 'institution';
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('الأساسية')->icon('heroicon-o-building-office-2')->columns(2)->schema([
                    Translatable::text('name', 'الاسم'),
                    Translatable::text('short_name', 'الاسم المختصر (يظهر في القوائم الضيقة)', required: false),
                    TextInput::make('slug')->label('المعرّف في الرابط')->required()->unique(ignoreRecord: true)->alphaDash()->maxLength(80)
                        ->extraInputAttributes(['dir' => 'ltr']),
                    Select::make('organization_type_id')->label('النوع')->relationship('type', 'name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->preload()->required()->live(),
                    TextInput::make('abbr')->label('الاختصار (مثل SQU)')->maxLength(30)->extraInputAttributes(['dir' => 'ltr']),
                    Select::make('academic_group')->label('الفئة الأكاديمية')->options(Organization::ACADEMIC_GROUPS)
                        ->visible(fn (Get $get) => self::template($get) === 'university'),
                    Select::make('sector')->label('القطاع')->options(Organization::SECTORS)
                        ->visible(fn (Get $get) => self::template($get) === 'university'),
                    Translatable::text('sport', 'الرياضة', required: false)->visible(fn (Get $get) => self::template($get) === 'federation'),
                    Translatable::text('city', 'المدينة', required: false),
                    Translatable::text('hq', 'المقر', required: false),
                    TextInput::make('founded')->label('سنة التأسيس')->numeric()->minValue(1800)->maxValue(2100),
                    TextInput::make('declared')->label('سنة الإشهار')->numeric()->minValue(1800)->maxValue(2100)
                        ->visible(fn (Get $get) => self::template($get) === 'federation'),
                    TextInput::make('website')->label('الموقع الإلكتروني')->url()->extraInputAttributes(['dir' => 'ltr']),
                    Toggle::make('is_featured')->label('مميّزة (تظهر أولًا)'),
                    Toggle::make('is_partner')->label('في «شركاء النجاح» بالرئيسية'),
                    ...Publishing::fields(),
                ]),
                Tab::make('التعريف')->icon('heroicon-o-document-text')->schema([
                    Translatable::text('tagline', 'الشعار النصي', required: false),
                    Translatable::text('formerly', 'الاسم السابق', required: false),
                    Translatable::textarea('about', 'نبذة', rows: 8),
                    Repeater::make('focus_areas')->label('مجالات العمل (وسوم تحت الاسم)')->defaultItems(0)->addActionLabel('إضافة مجال')
                        ->simple(TextInput::make('ar')->label('المجال')->required())
                        ->visible(fn (Get $get) => self::template($get) === 'institution'),
                    Repeater::make('memberships')->label('العضويات الدولية')->defaultItems(0)->addActionLabel('إضافة عضوية')->columns(2)
                        ->schema([Translatable::text('name', 'الجهة'), TextInput::make('year')->label('السنة')->numeric()])
                        ->visible(fn (Get $get) => self::template($get) === 'federation'),
                    Section::make('التصنيفات العالمية')->collapsible()->visible(fn (Get $get) => self::template($get) === 'university')->schema([
                        Translatable::text('ranking_label', 'العنوان (مثل: تصنيف QS العالمي)', required: false),
                        Repeater::make('rankings')->label('الترتيب حسب السنة')->defaultItems(0)->columns(2)->addActionLabel('إضافة سنة')
                            ->schema([TextInput::make('year')->label('السنة')->numeric()->required(), TextInput::make('rank')->label('الترتيب')->required()]),
                        Repeater::make('ranking_highlights')->label('أبرز التخصصات')->defaultItems(0)->addActionLabel('إضافة')
                            ->schema([Translatable::text('subject', 'التخصص'), Translatable::text('text', 'الترتيب / الملاحظة')]),
                        Translatable::text('ranking_note', 'ملاحظة / مصدر', required: false),
                    ]),
                ]),
                Tab::make('الهوية والصور')->icon('heroicon-o-photo')->schema([
                    MediaSlot::make('logo', 'الشعار', focal: false),
                    MediaSlot::make('logo_wide', 'الشعار العرضي (اختياري)', focal: false),
                    Grid::make(3)->schema([
                        TextInput::make('logo_ratio')->label('نسبة عرض الشعار إلى ارتفاعه')->numeric()->step(0.01)
                            ->helperText('أقل من 1.6 يظهر كشعار مربع، وأكثر كلوحة عرضية.'),
                        ColorPicker::make('color_primary')->label('اللون الأساسي'),
                        ColorPicker::make('color_secondary')->label('اللون الثانوي'),
                    ]),
                    MediaSlot::make('cover', 'صورة الغلاف'),
                ]),
                Tab::make('المعرض')->icon('heroicon-o-rectangle-stack')->schema([
                    Translatable::textarea('gallery_intro', 'مقدمة المعرض'),
                    Repeater::make('gallery_chapters')->label('فصول المكتبة (للجامعات)')->defaultItems(0)->columns(2)->addActionLabel('إضافة فصل')
                        ->schema([TextInput::make('key')->label('المعرّف')->required()->alphaDash(), Translatable::text('label', 'العنوان')]),
                    Grid::make(2)->schema([
                        TextInput::make('gallery_credit.text')->label('حقوق الصور (نص)'),
                        TextInput::make('gallery_credit.url')->label('حقوق الصور (رابط)')->url()->extraInputAttributes(['dir' => 'ltr']),
                    ]),
                ]),
                Tab::make('كلمة الرئيس')->icon('heroicon-o-chat-bubble-bottom-center-text')->schema([
                    Section::make()->relationship('letter')->schema([
                        Translatable::text('title', 'العنوان (مثل: كلمة رئيس الجامعة)', required: false),
                        Translatable::text('name', 'الاسم', required: false),
                        Translatable::text('role', 'الصفة', required: false),
                        Translatable::textarea('body', 'النص (افصل الفقرات بسطر فارغ)', rows: 10),
                        Grid::make(2)->schema([
                            TextInput::make('source_text')->label('المصدر'),
                            TextInput::make('source_url')->label('رابط المصدر')->url()->extraInputAttributes(['dir' => 'ltr']),
                        ]),
                        Toggle::make('is_published')->label('ظاهرة')->default(true),
                        MediaSlot::make('photo', 'صورة صاحب الكلمة (PNG مفرّغ)', focal: false),
                    ]),
                ]),
            ]),
        ]);
    }
}
