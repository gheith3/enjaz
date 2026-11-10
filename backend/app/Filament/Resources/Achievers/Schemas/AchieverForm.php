<?php

namespace App\Filament\Resources\Achievers\Schemas;

use App\Filament\Support\MediaSlot;
use App\Filament\Support\Publishing;
use App\Filament\Support\Translatable;
use App\Models\Achiever;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AchieverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('الأساسية')->icon('heroicon-o-user')->columns(2)->schema([
                    Translatable::text('name', 'الاسم'),
                    TextInput::make('slug')->label('المعرّف في الرابط')->required()->unique(ignoreRecord: true)
                        ->alphaDash()->maxLength(80)->helperText('بالإنجليزية، مثل ali-alhabsi')->extraInputAttributes(['dir' => 'ltr']),
                    Select::make('gender')->label('الجنس')->options(Achiever::GENDERS)->required(),
                    Select::make('field_id')->label('المجال')->relationship('field', 'name')->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->preload()->searchable(),
                    Translatable::text('headline', 'الوصف المختصر (يظهر تحت الاسم في البطاقة)', required: false),
                    Toggle::make('is_featured')->label('مميّز (علامة التميّز ويظهر أولًا)'),
                    ...Publishing::fields(),
                ]),
                Tab::make('الصفحة الشخصية')->icon('heroicon-o-identification')->schema([
                    Translatable::textarea('hero_lines', 'أسطر التعريف أعلى الصفحة (سطر لكل معلومة)', rows: 3),
                    Translatable::textarea('short_bio', 'نبذة البطاقة التعريفية', rows: 3),
                    Translatable::textarea('story', 'قصتي', rows: 8),
                    Section::make('مسيرتي')->description('عنوان قسم المسيرة. محطاتها في تبويب «محطات المسيرة» أسفل الصفحة.')->schema([
                        Translatable::text('journey_kicker', 'العنوان الصغير (مثل: أبرز محطات)', required: false),
                        Translatable::text('journey_title', 'العنوان (ضع الكلمات المميزة بين نجمتين: رحلتي *في الملاعب*)', required: false),
                    ]),
                    Section::make('اقتباس شخصي')->schema([
                        Translatable::textarea('personal_quote', 'الاقتباس'),
                        Translatable::text('personal_quote_context', 'المناسبة والتاريخ', required: false),
                        Translatable::text('personal_quote_source', 'المصدر', required: false),
                    ]),
                ]),
                Tab::make('الصور')->icon('heroicon-o-photo')->schema([
                    MediaSlot::make('portrait', 'صورة البطاقة (مربعة)', 'تظهر في قوائم المنجزين والشرائط.'),
                    MediaSlot::make('cutout', 'صورة مفرّغة (PNG شفاف)', 'أعلى الصفحة الشخصية، وبطاقة منجز الأسبوع، والبطاقة التعريفية.', focal: false),
                    MediaSlot::make('avatar', 'صورة صغيرة دائرية', 'بجانب الاسم في الشريط العلوي وصفحة الإنجاز.', focal: false),
                    MediaSlot::make('journey', 'صورة قسم «مسيرتي» (PNG شفاف)', focal: false),
                    Textarea::make('portrait_trace')->label('مسار الخط الذهبي حول الصورة المفرّغة (SVG path، اختياري)')->rows(2)
                        ->extraInputAttributes(['dir' => 'ltr']),
                ]),
                Tab::make('الظهور في الموقع')->icon('heroicon-o-sparkles')->columns(2)->schema([
                    Toggle::make('show_in_home_strip')->label('في شريط المنجزين بالرئيسية')->columnSpanFull(),
                    DatePicker::make('week_from')->label('منجز / منجزة الأسبوع ابتداءً من')
                        ->helperText('يظهر في صفحة المنجزين من هذا التاريخ حتى يُختار غيره من نفس الجنس.')->native(false),
                    Translatable::textarea('week_teaser', 'نص بطاقة الأسبوع', rows: 2),
                    Toggle::make('is_inspiring')->label('من «ملهمون من عُمان»')->live()->columnSpanFull(),
                    Translatable::text('inspiring_tagline', 'عبارة الإلهام', required: false)
                        ->visible(fn (Get $get) => (bool) $get('is_inspiring')),
                ]),
            ]),
        ]);
    }
}
