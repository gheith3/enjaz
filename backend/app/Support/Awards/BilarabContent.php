<?php

namespace App\Support\Awards;

use App\Models\Award;

/**
 * Content of the approved 2050 design of the Bilarab award page (تصاميم ٢٠٥٠ / معتمد): the «الفكرة» headline and text,
 * the award's goals, the participant card summary, the «ما بعد الدورتين» lead, the special competition's numbers and the
 * pillars of «إحياء العمارة». Added once (keys and groups that already exist are left as the editors made them); after
 * that the dashboard owns them (الجوائز → النصوص الصغيرة / القوائم).
 */
class BilarabContent
{
    /** @var array<string, string> */
    public const LABELS = [
        'idea_title' => 'جائزة تحوّل أفكار الشباب المعمارية **إلى معالم حقيقية** على أرض عُمان',
        'idea_text' => 'مسابقة محلية سنوية **برؤية عالمية**، أطلقها صاحب السمو السيد بلعرب بن هيثم آل سعيد في ١٥ يناير ٢٠٢٢. في كل دورة يُطرح موقع حقيقي في محافظة عُمانية وموضوع معماري جديد، فيتنافس الشباب العُماني على تصاميم تجمع الأصالة والمعاصرة، **ثم يُسلَّم المشروع الفائز إلى جهة حكومية لتنفيذه**.',
        'who_big' => '٣٥',
        'who_big_unit' => 'عامًا',
        'who_big_text' => 'الحد الأقصى للعمر',
        'who_team' => '١ – ٣',
        'who_team_text' => 'مشاركة فردية أو فريق',
        'who_open' => 'باب المشاركة مفتوح للشباب العُماني من الطلبة والباحثين عن عمل والمهنيين',
        'more_lead' => 'لم تتوقف الجائزة عند دورتيها؛ امتدت إلى **تصميم جناح عُمان في إكسبو العالمي**، ثم إلى مبادرة وطنية **تُعيد للعمارة مكانتها في الهوية العُمانية**.',
        'expo_tag' => 'نُفِّذ فعليًا · أوساكا ٢٠٢٥',
        'expo_note_title' => 'تتويج مزدوج',
    ];

    /** @var array<string, list<array{0: string, 1: string, 2: string}>> group => [label, value, icon] */
    public const FACTS = [
        'goals' => [
            ['تمكين المواهب', 'استثمار القدرات والمهارات المعمارية الوطنية وتمكينها، لتصنع قيمة مضافة في مختلف مناطق عُمان.', 'star'],
            ['صوت الشباب', 'الاستماع إلى الشباب بطريقة مختلفة، ترجمةً لتوجيهات جلالة السلطان بالاهتمام بهم والإصغاء إليهم.', 'chat'],
            ['هوية معمارية عُمانية', 'نمط معماري مبتكر ومعاصر ومستدام، يستلهم ثراء العمارة العُمانية وتنوّعها بين المحافظات.', 'arch'],
            ['أثر على أرض الواقع', 'المشروع الفائز لا يبقى على الورق: يُسلَّم إلى جهة حكومية لتنفيذه ضمن رؤية عُمان 2040.', 'build'],
        ],
        'expo' => [
            ['٨ يناير ٢٠٢٣', 'إطلاق المسابقة', ''],
            ['١١٦', 'مشاركة من ١٩٤ مشاركًا', ''],
            ['٥', 'مشاريع في النهائي', ''],
            ['١٬٧٦٣ م²', 'جناح قائم في أوساكا', ''],
        ],
        'initiative' => [
            ['ركن الجائزة', 'يعرض دورتيها والمشاريع الفائزة', 'arch'],
            ['منطقة الإنجازات', 'مساحة مخصصة لعرض الإنجازات', 'star'],
            ['تجربة تفاعلية', 'عن التعليم المستمر والابتكار وجاهزية سوق العمل', 'pc'],
            ['حلقات عمل ونقاشات', 'جلسات حوارية وورش عمل مصاحبة للمبادرة', 'talk'],
        ],
    ];

    public static function apply(Award $award): void
    {
        $extra = $award->extra ?? [];
        foreach (self::LABELS as $key => $text) {
            if (blank($extra[$key]['ar'] ?? null)) {
                $extra[$key] = ['ar' => $text];
            }
        }
        $award->forceFill(['extra' => $extra])->save();

        foreach (self::FACTS as $group => $rows) {
            if ($award->facts()->where('group', $group)->exists()) {
                continue;
            }
            foreach ($rows as $i => [$label, $value, $icon]) {
                $award->facts()->create(['group' => $group, 'label' => ['ar' => $label], 'value' => ['ar' => $value], 'icon' => $icon ?: null, 'sort_order' => $i + 1]);
            }
        }
    }
}
