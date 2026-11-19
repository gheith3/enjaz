<?php

use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;

/*
 * «المؤسسة في سطور» on the Ministry of Culture, Sports and Youth's page (the model for the other institutions): its
 * numbers, its path from the Ministry of Youth Affairs (1976) to the current ministry (Royal Decree 87/2020) and the
 * ministers it had and the heads of the body since 1976. Sources: the Royal Decrees on qanoon.om and Oman Daily (13 Jan 2026).
 * Each list is filled only when empty, so dashboard edits are kept. Editable in /admin (المؤسسات → الأرقام، المحطات، القادة).
 */
return new class extends Migration
{
    private const STATS = [
        ['value' => '٢٠٢٠', 'label' => 'سنة إنشاء الوزارة'],
        ['value' => '٨٧/٢٠٢٠', 'label' => 'المرسوم السلطاني بإنشائها'],
        ['value' => '١٩٧٦', 'label' => 'جذورها منذ وزارة شؤون الشباب'],
        ['value' => '٤', 'label' => 'جهات اندمجت فيها'],
    ];

    private const MILESTONES = [
        ['year' => '1976', 'title' => 'وزارة شؤون الشباب', 'text' => 'تحوّلت دائرة رعاية الشباب إلى وزارة لشؤون الشباب بمرسوم سلطاني حدّد اختصاصاتها.'],
        ['year' => '1979', 'title' => 'وزارة الإعلام وشؤون الشباب', 'text' => 'انتقلت مسؤولية قطاع الشباب واختصاصاته إلى وزارة الإعلام وشؤون الشباب.'],
        ['year' => '1982', 'title' => 'وزارة التربية والتعليم وشؤون الشباب', 'text' => 'نُقلت اختصاصات شؤون الشباب إلى وزارة التربية والتعليم بالمرسوم السلطاني رقم ٤٠/٨٢.'],
        ['year' => '1991', 'title' => 'الهيئة العامة للرياضة والأنشطة الشبابية', 'text' => 'أُنشئت بالمرسوم السلطاني رقم ١١٣/٩١، وآلت إليها مخصصات المجلس الأعلى لرعاية الشباب.'],
        ['year' => '1993', 'title' => 'الهيئة العامة لأنشطة الشباب الرياضية والثقافية', 'text' => 'عُدّلت تسمية الهيئة بالمرسوم السلطاني رقم ٢٦/٩٣.'],
        ['year' => '2004', 'title' => 'وزارة الشؤون الرياضية', 'text' => 'أُنشئت بالمرسوم السلطاني رقم ١١٢/٢٠٠٤، وانتقلت الأنشطة الثقافية إلى وزارة التراث والثقافة.'],
        ['year' => '2020', 'title' => 'وزارة الثقافة والرياضة والشباب', 'text' => 'أُنشئت بالمرسوم السلطاني رقم ٨٧/٢٠٢٠ في ١٨ أغسطس ٢٠٢٠، وآلت إليها وزارة الشؤون الرياضية ووزارة شؤون الفنون واللجنة الوطنية للشباب والشؤون الثقافية من وزارة التراث والثقافة.'],
    ];

    private const LEADERS = [
        ['photo' => 'fahr-bin-taimur', 'name' => 'فهر بن تيمور', 'title' => 'السيد', 'role' => 'وزير شؤون الشباب', 'from_year' => 1976, 'to_year' => 1979, 'note' => 'مرسوم ١٢/٧٦'],
        ['photo' => 'abdulaziz-alrowas', 'name' => 'عبدالعزيز بن محمد الرواس', 'title' => 'معالي', 'role' => 'وزير الإعلام وشؤون الشباب', 'from_year' => 1979, 'to_year' => 1982, 'note' => 'مرسوم ٢٨/٧٩'],
        ['photo' => 'yahya-almantheri', 'name' => 'يحيى بن محفوظ المنذري', 'title' => 'معالي', 'role' => 'وزير التربية والتعليم وشؤون الشباب', 'from_year' => 1982, 'to_year' => 1991, 'note' => 'مرسوم ٤٠/٨٢'],
        ['photo' => 'ibrahim-alsubhi', 'name' => 'إبراهيم بن حمود الصبحي', 'title' => '', 'role' => 'رئيس الهيئة العامة للرياضة والأنشطة الشبابية بالوكالة', 'from_year' => 1991, 'to_year' => 1993, 'note' => 'مرسوم ١١٤/٩١'],
        ['photo' => 'mohammed-almamari', 'name' => 'محمد بن مرهون المعمري', 'title' => 'الشيخ', 'role' => 'رئيس الهيئة العامة لأنشطة الشباب الرياضية والثقافية برتبة وزير', 'from_year' => 1993, 'to_year' => 2004, 'note' => 'مرسوم ٧٤/٩٣'],
        ['photo' => 'ali-alsunaidy', 'name' => 'علي بن مسعود السنيدي', 'title' => 'معالي', 'role' => 'وزير الشؤون الرياضية', 'from_year' => 2004, 'to_year' => 2012, 'note' => 'مرسوم ١١٢/٢٠٠٤'],
        ['photo' => 'saad-almardhouf', 'name' => 'سعد بن محمد المرضوف السعدي', 'title' => 'معالي الشيخ', 'role' => 'وزير الشؤون الرياضية', 'from_year' => 2012, 'to_year' => 2020, 'note' => 'مرسوم ١١/٢٠١٢'],
        ['photo' => 'theyazin-bin-haitham', 'name' => 'ذي يزن بن هيثم آل سعيد', 'title' => 'صاحب السمو السيد', 'role' => 'وزير الثقافة والرياضة والشباب', 'from_year' => 2020, 'to_year' => null, 'note' => 'مرسوم ١١١/٢٠٢٠'],
        ['photo' => 'saud-bin-hilal', 'name' => 'سعود بن هلال البوسعيدي', 'title' => 'معالي السيد', 'role' => 'وزير الثقافة والرياضة والشباب', 'from_year' => null, 'to_year' => null, 'note' => '', 'is_current' => true],
    ];

    private const SOURCES = [
        ['title' => 'المرسوم السلطاني رقم ٨٧/٢٠٢٠ — قانون عُمان', 'url' => 'https://qanoon.om/p/2020/rd2020087/'],
        ['title' => 'المرسوم السلطاني رقم ١١٢/٢٠٠٤ — قانون عُمان', 'url' => 'https://qanoon.om/p/2004/rd2004112/'],
        ['title' => 'من دائرة لرعاية الشباب لوزارة تعنى بالثقافة والرياضة والشباب — جريدة عُمان', 'url' => 'https://www.omandaily.om/article/1194490'],
    ];

    public function up(): void
    {
        $ministry = Organization::query()->where('slug', 'mcsy')->first();
        if (! $ministry) {
            return;
        }
        if ($ministry->founded === null) {
            $ministry->forceFill(['founded' => 2020])->save();
        }
        if ($ministry->stats()->doesntExist()) {
            foreach (self::STATS as $i => $stat) {
                $ministry->stats()->create(['value' => ['ar' => $stat['value']], 'label' => ['ar' => $stat['label']], 'sort_order' => $i]);
            }
        }
        if ($ministry->milestones()->doesntExist()) {
            foreach (self::MILESTONES as $i => $m) {
                $ministry->milestones()->create(['year' => $m['year'], 'title' => ['ar' => $m['title']], 'text' => ['ar' => $m['text']], 'sort_order' => $i]);
            }
        }
        if ($ministry->leaders()->where('kind', 'president')->doesntExist()) {
            foreach (self::LEADERS as $i => $l) {
                $leader = $ministry->leaders()->create(['kind' => 'president', 'name' => ['ar' => $l['name']], 'title' => $l['title'] ? ['ar' => $l['title']] : null, 'role' => ['ar' => $l['role']],
                    'from_year' => $l['from_year'], 'to_year' => $l['to_year'], 'is_current' => $l['is_current'] ?? false, 'note' => $l['note'] ? ['ar' => $l['note']] : null, 'sort_order' => $i]);
                // Photo from database/data/leaders/{photo}.webp when it was provided.
                if (is_file($file = database_path('data/leaders/'.$l['photo'].'.webp'))) {
                    $leader->addMedia($file)->preservingOriginal()->withCustomProperties(['alt' => $l['name']])->toMediaCollection('photo');
                }
            }
        }
        // The leader's word now carries the current minister's name and photo (it still named the previous minister).
        $letter = $ministry->letter;
        if ($letter && str_contains((string) $letter->getTranslation('name', 'ar', false), 'ذي يزن')) {
            $letter->setTranslation('name', 'ar', 'سعود بن هلال البوسعيدي')->save();
            $letter->clearMediaCollection('photo');
            $letter->addMedia(database_path('data/letters/mcsy-saud-bin-hilal.webp'))->preservingOriginal()
                ->withCustomProperties(['alt' => 'معالي السيد سعود بن هلال البوسعيدي'])->toMediaCollection('photo');
        }
        if ($ministry->sources()->doesntExist()) {
            foreach (self::SOURCES as $i => $s) {
                $ministry->sources()->create(['title' => ['ar' => $s['title']], 'url' => $s['url'], 'sort_order' => $i]);
            }
        }
    }

    public function down(): void
    {
        // Data only; the dashboard owns these lists afterwards.
    }
};
