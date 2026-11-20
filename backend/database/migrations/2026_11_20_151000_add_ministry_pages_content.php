<?php

use App\Models\Achievement;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;

/*
 * The approved ministry pages («صفحات الوزارات», 2026-11-20):
 * - Ministry of Heritage and Tourism: about, card, «كلمة المسؤول» (the minister's word, mht.gov.om), and «المؤسسة في سطور»
 *   (numbers, vision and mission, priorities, path since 1976 and the heritage and tourism ministers, with their decrees on
 *   qanoon.om). Each part is filled only when empty, so dashboard edits are kept.
 * - Ministry of Culture, Sports and Youth: its minister's word is shown (cut-out portrait) and its sports achievements are linked.
 * Photos come from database/data/leaders and database/data/letters. Editable in /admin (المؤسسات).
 */
return new class extends Migration
{
    private const MHT_ABOUT = "تعمل وزارة التراث والسياحة على صون التراث العُماني وتنمية قطاع سياحي متنوع ومستدام، يجمع بين حماية الموروث التاريخي والثقافي وإبراز مقومات سلطنة عُمان وتجاربها للزوار.\n\nوتختص الوزارة بوضع السياسات والخطط في مجالي التراث والسياحة، وتنظيم الأنشطة والمشروعات السياحية، وتطوير التعاون مع المؤسسات والشركاء، بما يسهم في التنمية الاقتصادية والاجتماعية.";

    private const MHT_DETAILS = [
        'founded_label' => 'المسمى الحالي',
        'direction' => [
            ['label' => 'رؤية الوزارة', 'title' => 'تراثٌ مصان وتجارب سياحية مستدامة', 'text' => 'تعزيز ريادة عُمان في إدارة تراثها وتطوير وجهات تمنح الزائر تجارب متنوّعة ومستدامة.'],
            ['label' => 'رسالة الوزارة', 'title' => 'جودةٌ وتنافسية في قطاعي التراث والسياحة', 'text' => 'تطوير القطاعين بالممارسات المتقدمة ورفع تنافسية المقاصد والتجارب محليًا وإقليميًا ودوليًا.'],
        ],
        'priorities' => ['تطوير التخطيط وجذب الاستثمار', 'صون التراث وتطوير التجارب السياحية', 'تنمية القدرات الوطنية والتحول الرقمي', 'تعزيز جودة الخدمات واستدامتها'],
        'heads_note' => 'وزراء التراث ووزراء السياحة قبل جمع الاختصاصات في وزارة واحدة عام ٢٠٢٠؛ تتزامن بعض الفترات لاختلاف الحقيبة الوزارية.',
    ];

    private const MHT_STATS = [
        ['value' => '٢٠٢٠', 'label' => 'اعتماد المسمى الحالي للوزارة'],
        ['value' => '٩١/٢٠٢٠', 'label' => 'المرسوم السلطاني للمسمى والاختصاصات'],
        ['value' => 'مجالان', 'label' => 'التراث والسياحة'],
        ['value' => '٢٠٤٠', 'label' => 'رؤية عُمان للتنمية المستدامة'],
    ];

    private const MHT_MILESTONES = [
        ['year' => '1976', 'title' => 'بداية وزارة التراث', 'decree' => 'مرسوم ١٢/٧٦', 'text' => 'تعيين السيد فيصل بن علي وزيرًا للتراث العُماني.'],
        ['year' => '2002', 'title' => 'وزارة التراث والثقافة', 'decree' => 'مرسوما ١٠ و١١/٢٠٠٢', 'text' => 'تعديل المسمى إلى وزارة التراث والثقافة وتعيين سمو السيد هيثم بن طارق وزيرًا لها آنذاك.'],
        ['year' => '2004', 'title' => 'إنشاء وزارة السياحة', 'decree' => 'مرسوم ٦١/٢٠٠٤', 'text' => 'إنشاء وزارة مستقلة للسياحة وتعيين راجحة بنت عبدالأمير بن علي وزيرة لها.'],
        ['year' => '2020', 'title' => 'وزارة التراث والسياحة', 'decree' => 'مرسوم ٩١/٢٠٢٠', 'text' => 'عُدّل مسمى وزارة التراث والثقافة إلى وزارة التراث والسياحة، وحُددت اختصاصاتها واعتمد هيكلها التنظيمي.'],
    ];

    private const MHT_LEADERS = [
        ['photo' => 'mht-faisal-bin-ali', 'focal' => '50% 30%', 'title' => 'معالي السيد', 'name' => 'فيصل بن علي آل سعيد', 'role' => 'وزير التراث القومي والثقافة', 'from_year' => 1976, 'to_year' => 2002, 'note' => 'مرسوم ١٢/٧٦', 'note_url' => 'https://qanoon.om/p/1976/rd1976012/'],
        ['photo' => 'mht-haitham-bin-tarik', 'focal' => '50% 12%', 'title' => 'جلالة السلطان', 'name' => 'هيثم بن طارق المعظم', 'role' => 'وزير التراث والثقافة آنذاك', 'from_year' => 2002, 'to_year' => 2020, 'note' => 'مرسوم ١١/٢٠٠٢', 'note_url' => 'https://qanoon.om/p/2002/rd2002011/'],
        ['photo' => 'mht-rajiha-abdulamir', 'focal' => '50% 30%', 'title' => 'معالي الدكتورة', 'name' => 'راجحة بنت عبدالأمير بن علي', 'role' => 'وزيرة السياحة', 'from_year' => 2004, 'to_year' => 2011, 'note' => 'مرسوم ٦١/٢٠٠٤', 'note_url' => 'https://qanoon.om/p/2004/rd2004061/'],
        ['photo' => 'mht-mohsin-alshaikh', 'focal' => '50% 30%', 'title' => 'معالي المهندس', 'name' => 'محسن بن محمد بن علي الشيخ', 'role' => 'وزير السياحة', 'from_year' => 2011, 'to_year' => 2011, 'period' => '٢٦ فبراير – ٧ مارس ٢٠١١', 'note' => 'مرسوم ١٣/٢٠١١', 'note_url' => 'https://qanoon.om/p/2011/rd2011013/'],
        ['photo' => 'mht-abdulmalik-alkhalili', 'focal' => '50% 30%', 'title' => 'معالي الشيخ', 'name' => 'عبدالملك بن عبدالله بن علي الخليلي', 'role' => 'وزير السياحة', 'from_year' => 2011, 'to_year' => 2012, 'note' => 'مرسوم ٣١/٢٠١١', 'note_url' => 'https://qanoon.om/p/2011/rd2011031/'],
        ['photo' => 'mht-ahmed-almahrizi', 'focal' => '50% 30%', 'title' => 'معالي', 'name' => 'أحمد بن ناصر بن حمد المحرزي', 'role' => 'وزير السياحة', 'from_year' => 2012, 'to_year' => 2020, 'note' => 'مرسوم ١١/٢٠١٢', 'note_url' => 'https://qanoon.om/p/2012/rd2012011/'],
        ['photo' => 'mht-salim-almahrouqi', 'focal' => '50% 22%', 'title' => 'معالي', 'name' => 'سالم بن محمد بن سعيد المحروقي', 'role' => 'وزير التراث والثقافة ثم وزير التراث والسياحة', 'from_year' => 2020, 'to_year' => 2026, 'note' => 'مرسوم ٩/٢٠٢٠', 'note_url' => 'https://qanoon.om/p/2020/rd2020009/'],
        ['photo' => 'mht-ibrahim-albusaidi', 'focal' => null, 'title' => 'معالي السيد', 'name' => 'إبراهيم بن سعيد البوسعيدي', 'role' => 'وزير التراث والسياحة', 'from_year' => 2026, 'to_year' => null, 'is_current' => true, 'note' => 'تعيين الوزير', 'note_url' => 'https://www.fm.gov.om/ar/35198/'],
    ];

    private const MHT_SOURCES = [
        ['title' => 'اختصاصات الوزارة — الموقع الرسمي لوزارة التراث والسياحة', 'url' => 'https://mht.gov.om/ar/%D8%B9%D9%86/%D9%88%D8%B2%D8%A7%D8%B1%D8%A9/%D8%A7%D8%AE%D8%AA%D8%B5%D8%A7%D8%B5%D8%A7%D8%AA-%D8%A7%D9%84%D9%88%D8%B2%D8%A7%D8%B1%D8%A9/'],
        ['title' => 'تعيين الوزير — وزارة الخارجية', 'url' => 'https://www.fm.gov.om/ar/35198/'],
    ];

    private const MCSY_WORD = "الشكر موصول لكل من ساهم في إخراج هذا العمل وأرشفة هذه الإنجازات النيرة لإيصالها للمجتمع المحلي والدولي بصورة تليق بحجم إنجازات شباب عمان.\n\nإن شباب عُمان هم ثروة الوطن الحقيقية، وما نراه اليوم من إنجازات في ميادين الثقافة والرياضة والعلوم والفنون شاهدٌ على ما يحملونه من طموح وعزيمة، وعلى ما توليه المؤسسات من رعاية واهتمام.\n\nونحن في الوزارة نؤمن بأن توثيق هذه الإنجازات ليس مجرد حفظٍ للأسماء والتواريخ، بل هو رسالة إلهام للأجيال القادمة، ودعوة لكل شاب وشابة بأن الطريق إلى التميّز مفتوح لمن يسعى إليه.\n\nوسنواصل العمل مع شركائنا لتمكين الشباب وفتح آفاق جديدة أمامهم، ليبقى اسم عُمان حاضراً في كل محفل، مرفوعاً بسواعد أبنائها وبناتها.";

    private const MCSY_ACHIEVEMENTS = [
        'hamed-alwahaibi-2026-01-31', 'hamed-alwahaibi-2025-11-01', 'aly-alhbsy-03', 'aly-alhbsy-07', 'aly-alhbsy-10',
        'al-kathiri-u17-world-cup-golden-ball-1995', 'al-kathiri-afc-u16-third-place-1994', 'thhby-400-mtr-fy-alalaaab-alasyoy',
    ];

    public function up(): void
    {
        if ($mht = Organization::query()->where('slug', 'ministry-heritage-tourism')->first()) {
            $this->heritageTourism($mht);
        }
        if ($mcsy = Organization::query()->where('slug', 'mcsy')->first()) {
            $this->cultureSportsYouth($mcsy);
        }
    }

    public function down(): void
    {
        // Data only; the dashboard owns these records afterwards.
    }

    private function heritageTourism(Organization $o): void
    {
        $fill = [];
        if (blank($o->getTranslation('about', 'ar', false))) {
            $fill['about'] = ['ar' => self::MHT_ABOUT];
        }
        if (blank($o->website)) {
            $fill['website'] = 'https://mht.gov.om/ar/';
        }
        if (empty($o->focus_areas)) {
            $fill['focus_areas'] = [['ar' => 'التراث'], ['ar' => 'السياحة'], ['ar' => 'الاستدامة']];
        }
        if ($o->founded === null) {
            $fill['founded'] = 2020;
        }
        if (empty($o->details)) {
            $fill['details'] = self::MHT_DETAILS;
        }
        if ($fill) {
            $o->forceFill($fill)->save();
        }
        if ($o->stats()->doesntExist()) {
            foreach (self::MHT_STATS as $i => $s) {
                $o->stats()->create(['value' => ['ar' => $s['value']], 'label' => ['ar' => $s['label']], 'sort_order' => $i]);
            }
        }
        if ($o->milestones()->doesntExist()) {
            foreach (self::MHT_MILESTONES as $i => $m) {
                $o->milestones()->create(['year' => $m['year'], 'title' => ['ar' => $m['title']], 'text' => ['ar' => $m['text']], 'decree' => $m['decree'], 'sort_order' => $i]);
            }
        }
        if ($o->leaders()->where('kind', 'president')->doesntExist()) {
            foreach (self::MHT_LEADERS as $i => $l) {
                $leader = $o->leaders()->create(['kind' => 'president', 'name' => ['ar' => $l['name']], 'title' => ['ar' => $l['title']], 'role' => ['ar' => $l['role']],
                    'from_year' => $l['from_year'], 'to_year' => $l['to_year'], 'is_current' => $l['is_current'] ?? false, 'note' => ['ar' => $l['note']],
                    'period' => isset($l['period']) ? ['ar' => $l['period']] : null, 'note_url' => $l['note_url'], 'sort_order' => $i]);
                if (is_file($file = database_path('data/leaders/'.$l['photo'].'.webp'))) {
                    $leader->addMedia($file)->preservingOriginal()
                        ->withCustomProperties(array_filter(['alt' => $l['title'].' '.$l['name'], 'focal' => $l['focal']]))->toMediaCollection('photo');
                }
            }
        }
        if (! $o->letter) {
            $letter = $o->letter()->create([
                'title' => ['ar' => 'معالي السيد'], 'name' => ['ar' => 'إبراهيم بن سعيد البوسعيدي'], 'role' => ['ar' => 'وزير التراث والسياحة'],
                'quote' => ['ar' => 'نأمل أن تجد في بوابة الوزارة للخدمات الحكومية الإلكترونية معينا دائما يساعدك للوصول إلى خدمات الوزارة الإلكترونية وإنجازها بكل يسر وسهولة'],
                'body' => ['ar' => "تؤكد كلمة الوزير أهمية التقنيات الحديثة في تيسير الخدمات الحكومية وإتاحة المعلومات للمهتمين والمستثمرين في قطاعي التراث والسياحة. وتوضح دور بوابة الوزارة في تقريب هذه الخدمات وتسهيل إنجازها.\n\nكما تدعو إلى تقديم المقترحات والمشاركة عبر البوابة وحسابات الوزارة، للمساهمة في تطوير جودة الخدمات والمعلومات والتعرّف على الفرص الاستثمارية المتاحة."],
                'source_text' => 'الموقع الرسمي لوزارة التراث والسياحة',
                'source_url' => 'https://mht.gov.om/ar/%D8%B9%D9%86/%D9%88%D8%B2%D8%A7%D8%B1%D8%A9/%D9%83%D9%84%D9%85%D8%A9-%D9%85%D8%B9%D8%A7%D9%84%D9%8A-%D8%A7%D9%84%D9%88%D8%B2%D9%8A%D8%B1/',
                'photo_fit' => 'tall', 'is_published' => true,
            ]);
            $letter->addMedia(database_path('data/letters/mht-ibrahim-albusaidi.webp'))->preservingOriginal()
                ->withCustomProperties(['alt' => 'معالي السيد إبراهيم بن سعيد البوسعيدي'])->toMediaCollection('photo');
        }
        if ($o->sources()->doesntExist()) {
            foreach (self::MHT_SOURCES as $i => $s) {
                $o->sources()->create(['title' => ['ar' => $s['title']], 'url' => $s['url'], 'sort_order' => $i]);
            }
        }
    }

    private function cultureSportsYouth(Organization $o): void
    {
        $letter = $o->letter ?? $o->letter()->make();
        // As approved: the minister's name without an honorific, the approved word, and its quote taken from the first lines.
        $letter->fill(['name' => ['ar' => 'سعود بن هلال البوسعيدي'], 'role' => ['ar' => 'وزير الثقافة والرياضة والشباب'], 'body' => ['ar' => self::MCSY_WORD],
            'photo_fit' => 'wide', 'is_published' => true]);
        $letter->setRawAttributes(array_merge($letter->getAttributes(), ['title' => null, 'quote' => null]));
        $letter->save();
        $letter->clearMediaCollection('photo');
        $letter->addMedia(database_path('data/letters/mcsy-saud-bin-hilal-cutout.webp'))->preservingOriginal()
            ->withCustomProperties(['alt' => 'معالي السيد سعود بن هلال البوسعيدي'])->toMediaCollection('photo');

        $ids = Achievement::query()->whereIn('slug', self::MCSY_ACHIEVEMENTS)->pluck('id');
        $o->achievements()->syncWithoutDetaching($ids);
    }
};
