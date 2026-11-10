<?php

namespace App\Filament\Resources\Awards\Schemas;

use App\Filament\Support\MediaSlot;
use App\Filament\Support\Publishing;
use App\Filament\Support\Translatable;
use App\Models\Award;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AwardForm
{
    /**
     * Small texts of each design (stored in `extra` as {key: {ar, en}}), by kind.
     *
     * @var array<string, array<string, string>>
     */
    private const LABELS = [
        'culture' => [
            'pillars_title' => 'عنوان الفروع', 'winners_eyebrow' => 'عنوان صغير فوق الفائزين', 'winners_note' => 'شرح تحت عنوان الفائزين',
            'word_title' => 'عنوان الكلمة',
        ],
        'volunteer' => [
            'how_title' => 'عنوان «من يتنافس»', 'fields_title' => 'عنوان المجالات', 'winners_eyebrow' => 'عنوان صغير فوق الفائزين',
            'winners_title' => 'عنوان الفائزين', 'winners_note' => 'شرح تحت عنوان الفائزين',
        ],
        'architecture' => [
            'crumb' => 'الاسم في الشريط العلوي', 'blessing' => 'بطاقة المباركة (عنوان)', 'blessing_by' => 'بطاقة المباركة (نص)',
            'vision' => 'بطاقة الرؤية (عنوان)', 'vision_text' => 'بطاقة الرؤية (نص)', 'where' => 'بطاقة الموقع (عنوان)', 'where_text' => 'بطاقة الموقع (نص)',
            'path_title' => 'عنوان المسار', 'firsts_label' => 'عنوان «سابقة»', 'more_title' => 'عنوان «ما بعد الدورات»',
        ],
    ];

    public static function configure(Schema $schema): Schema
    {
        $kind = fn (Get $get) => $get('kind') ?? 'culture';

        return $schema->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('الأساسية')->icon('heroicon-o-trophy')->columns(2)->schema([
                    Translatable::text('name', 'الاسم الكامل'),
                    TextInput::make('slug')->label('المعرّف في الرابط')->required()->unique(ignoreRecord: true)->alphaDash()->maxLength(80)
                        ->extraInputAttributes(['dir' => 'ltr']),
                    Select::make('kind')->label('التصميم')->options(Award::KINDS)->required()->default('culture')->live(),
                    Translatable::text('title', 'العنوان الكبير في الهيدر', required: false),
                    Translatable::text('subtitle', 'السطر الصغير تحت العنوان', required: false),
                    Translatable::text('eyebrow', 'العنوان الصغير فوق العنوان', required: false),
                    Translatable::text('motto', 'الشعار النصي', required: false),
                    Translatable::text('date_line', 'سطر الموعد (الجزء العريض | بقية السطر)', required: false)
                        ->visible(fn (Get $get) => $kind($get) === 'volunteer'),
                    TextInput::make('decree')->label('رقم المرسوم')->maxLength(40)->visible(fn (Get $get) => $kind($get) === 'culture'),
                    TextInput::make('founded_year')->label('سنة التأسيس')->numeric()->minValue(1900)->maxValue(2100),
                    Translatable::text('launched', 'تاريخ الإطلاق (مثل: 15 يناير 2022)', required: false),
                    TextInput::make('website')->label('الموقع الإلكتروني')->url()->extraInputAttributes(['dir' => 'ltr']),
                    ...Publishing::fields(),
                ]),
                Tab::make('التعريف')->icon('heroicon-o-document-text')->schema([
                    Translatable::textarea('about', 'نبذة', rows: 6),
                    Translatable::textarea('lead', 'مقدمة (آلية المنح / الفكرة؛ **نص** يظهر عريضًا)', rows: 3),
                    Translatable::textarea('vision', 'الرؤية', rows: 2),
                    Translatable::textarea('honor', 'التكريم المعنوي', rows: 2),
                    Translatable::textarea('firsts', 'سابقة / ميزة', rows: 2),
                ]),
                Tab::make('صفحة الجوائز')->icon('heroicon-o-squares-2x2')->columns(2)->schema([
                    Select::make('card_style')->label('شكل البطاقة في صفحة «جوائز السلطان قابوس»')
                        ->options(['culture' => 'الوسام الذهبي', 'vol' => 'الكأس (صورة البطاقة)'])->placeholder('لا تظهر في الصفحة')
                        ->helperText('الجوائز التي لها بطاقة تظهر في صفحة الجوائز ويتبعها رابط القائمة.'),
                    TextInput::make('extra.field_label.ar')->label('المجال (يظهر في الهيدر وفي الشريط العلوي)'),
                    Translatable::text('card_meta', 'سطر البطاقة الصغير', required: false),
                    Translatable::text('card_text', 'نص البطاقة', required: false),
                ]),
                Tab::make('نصوص التصميم')->icon('heroicon-o-language')->schema(
                    collect(self::LABELS)->map(fn ($labels, $k) => Section::make(Award::KINDS[$k])->columns(2)
                        ->visible(fn (Get $get) => $kind($get) === $k)
                        ->schema(collect($labels)->map(fn ($label, $key) => TextInput::make("extra.$key.ar")->label($label))->values()->all()))
                        ->values()->all()
                ),
                Tab::make('الصور')->icon('heroicon-o-photo')->schema([
                    MediaSlot::make('logo', 'الشعار', focal: false),
                    MediaSlot::make('mark', 'العلامة (تظهر في الهيدر والوسط)', focal: false),
                    MediaSlot::make('hero', 'صورة خلفية الهيدر'),
                    MediaSlot::make('card_art', 'صورة البطاقة (كأس مفرّغ)', focal: false),
                    MediaSlot::make('vision_logo', 'شعار الرؤية', focal: false),
                    MediaSlot::make('emblem', 'الشعار الوطني (بطاقة المباركة)', focal: false),
                ]),
                Tab::make('الكلمة')->icon('heroicon-o-chat-bubble-bottom-center-text')->schema([
                    Section::make()->relationship('letter')->schema([
                        Translatable::text('title', 'اللقب (مثل: معالي السيد)', required: false),
                        Translatable::text('name', 'الاسم', required: false),
                        Translatable::text('role', 'الصفة', required: false),
                        Translatable::text('quote', 'اقتباس بارز', required: false),
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
