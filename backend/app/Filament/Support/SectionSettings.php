<?php

namespace App\Filament\Support;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

/**
 * The settings form of each page section (stored in page_sections.settings; texts as {ar, en}), and which sections have
 * hand-picked items (page_section_items). Empty fields use the design's text.
 */
class SectionSettings
{
    /** Sections with items, and what an item is in each. */
    public const ITEMS = [
        'home.spotlight' => 'الشخص المختار (الأول فقط يظهر؛ الصورة: PNG مفرّغ)',
        'home.awards' => 'البطاقة العريضة (الأولى فقط تظهر؛ الصورة: الخلفية؛ العنوان: | لسطر جديد؛ «بيانات» مفصولة بـ |)',
        'home.sports' => 'الأشخاص (الصورة: PNG مفرّغ؛ الوصف: الرياضة)',
        'home.culture' => 'الأشخاص (الصورة: صورة شخصية؛ الوصف: الشارة)',
        'about.fields' => 'المجالات في الحلقة الدوّارة (العنوان، النص، الصورة، الأيقونة)',
        'about.journey' => 'محطات خارطة العمل (العنوان المختصر؛ النص الكامل؛ الوصف: التاريخ؛ بيانات إضافية: السنة)',
        'about.founder' => 'صاحب الكلمة (الأول فقط؛ الوصف: الصفة؛ النص: الكلمة، افصل الفقرات بسطر فارغ؛ بيانات إضافية: العنوان؛ صورة ثانية: البطاقة)',
        'about.testimonials' => 'الشهادات (الاسم، الصفة في الوصف، النص؛ «مميّز» = شهادة رسمية)',
        'about.team' => 'فريق العمل (الاسم، الصفة في الوصف، الصورة)',
        'about.achievements' => 'إنجازاتنا (العنوان، التصنيف في الوصف، الصورة)',
        'about.media' => 'المكتبة الإعلامية (العنوان، الصورة، الأيقونة، الرابط)',
    ];

    /** @var array<string, array<string, string>> extra text fields per section key (key => label) */
    private const TEXTS = [
        'home.hero' => ['title' => 'اسم العرض (لقارئات الشاشة)'],
        'home.achievers' => ['title' => 'اسم القسم (لقارئات الشاشة)', 'more_label' => 'نص الرابط'],
        'home.latest' => ['title' => 'العنوان', 'more_label' => 'نص الزر'],
        'home.spotlight' => ['title' => 'العنوان الصغير'],
        'home.awards' => ['title' => 'العنوان', 'lead' => 'النص تحت العنوان'],
        'home.inspiring' => ['title' => 'العنوان', 'lead' => 'النص تحت العنوان'],
        'home.articles' => ['title' => 'العنوان'],
        'home.sports' => ['title' => 'العنوان', 'more_label' => 'نص الزر'],
        'home.culture' => ['title' => 'العنوان', 'lead' => 'النص تحت العنوان'],
        'home.science' => ['title' => 'العنوان', 'lead' => 'النص تحت العنوان', 'more_label' => 'نص الزر'],
        'about.fields' => ['nav' => 'الاسم في شريط الأقسام', 'eyebrow' => 'العنوان الصغير', 'title' => 'العنوان (| حول الكلمة الملوّنة)', 'lead' => 'النص'],
        'about.journey' => ['nav' => 'الاسم في شريط الأقسام', 'title' => 'العنوان', 'lead' => 'الملاحظة تحت الخارطة'],
        'about.founder' => ['nav' => 'الاسم في شريط الأقسام'],
        'about.testimonials' => ['nav' => 'الاسم في شريط الأقسام', 'title' => 'العنوان'],
        'about.team' => ['nav' => 'الاسم في شريط الأقسام', 'title' => 'العنوان'],
        'about.achievements' => ['nav' => 'الاسم في شريط الأقسام', 'title' => 'العنوان'],
        'about.media' => ['nav' => 'الاسم في شريط الأقسام', 'title' => 'العنوان'],
        'home.partners' => ['eyebrow' => 'العنوان الصغير', 'title' => 'العنوان', 'more_label' => 'نص الرابط',
            'rows.gov' => 'اسم صف الحكومي', 'rows.private' => 'اسم صف الخاص', 'rows.military' => 'اسم صف العسكري', 'rows.sports' => 'اسم صف الرياضي'],
    ];

    /** @return list<Component> */
    public static function schema(?string $key): array
    {
        $fields = collect(self::TEXTS[$key] ?? ['title' => 'العنوان'])
            ->map(fn (string $label, string $name) => Grid::make(2)->schema([
                TextInput::make("settings.$name.ar")->label($label),
                TextInput::make("settings.$name.en")->label($label.' (English)')->extraInputAttributes(['dir' => 'ltr']),
            ]))->values()->all();

        if (in_array($key, ['home.sports', 'home.culture', 'home.science'], true)) {
            $fields[] = TextInput::make('settings.more_url')->label('رابط الزر (فارغ = الرابط الافتراضي)')->extraInputAttributes(['dir' => 'ltr']);
        }
        if ($key === 'home.partners') {
            $fields[] = TagsInput::make('settings.row_order')->label('ترتيب الصفوف (معرّفات أنواع المؤسسات)')
                ->helperText('مثل: gov, private, military, sports. الأنواع غير المذكورة تأتي بعدها.');
        }
        if ($key === 'home.stats') {
            $defaults = ['عدد المنجزين', 'عدد الإنجازات', 'عدد المجالات'];
            foreach ($defaults as $i => $label) {
                $fields[] = Grid::make(3)->schema([
                    TextInput::make("settings.stats.$i.label.ar")->label('الرقم '.($i + 1).': الوصف')->placeholder($label),
                    TextInput::make("settings.stats.$i.label.en")->label('الوصف (English)'),
                    TextInput::make("settings.stats.$i.value")->label('الرقم (فارغ = يُحسب تلقائيًا)')->numeric(),
                ]);
            }
        }

        return [Section::make('نصوص القسم')->description('اتركها فارغة لاستخدام نص التصميم.')->schema($fields)->columnSpanFull()];
    }
}
