<?php

use App\Models\Achiever;
use Illuminate\Database\Migrations\Migration;

/*
 * The line under the name on the profiles of the literature achievers added on 23 November 2026 (ملف «الأدب»), like
 * Jokha Alharthi's: three short descriptors from their documented achievements. Only fills empty values, so later
 * dashboard edits are kept.
 */
return new class extends Migration
{
    private const LINES = [
        'bushra-khalfan' => "روائية وقاصّة عُمانية\nمؤسِّسة مختبر السرديات العُماني\nفائزة بجائزة كتارا للرواية العربية",
        'mohsin-alkindi' => "أكاديمي وباحث في التأريخ الثقافي والأدبي\nحائز على جائزة السلطان قابوس للثقافة والفنون والآداب\nصاحب أول رسالة ماجستير في جامعة السلطان قابوس",
        'hassan-almatroushi' => "شاعر ومترجم عُماني\n«شاعر المعلقة» في الشعر الفصيح\nأول شاعر عربي يفوز بجائزة «توليولا» الإيطالية",
        'emad-alshanfari' => "كاتب ومخرج مسرحي\nرئيس الجمعية العُمانية للمسرح\nحائز على جائزة السلطان قابوس ووسام الاستحقاق",
        'zahran-alqasmi' => "روائي وشاعر عُماني\nأول عُماني يفوز بالجائزة العالمية للرواية العربية\nصاحب رواية «تغريبة القافر»",
        'mahmoud-alrahbi' => "قاص وروائي عُماني\nحائز على جائزة السلطان قابوس للثقافة والفنون والآداب\nفائز بجائزة الملتقى للقصة القصيرة العربية",
        'saif-alrahbi' => "شاعر وكاتب عُماني\nرئيس تحرير مجلة «نزوى» الثقافية\nحائز على جائزة السلطان قابوس التقديرية",
        'mohammed-alyahyai' => "قاص وروائي وباحث في التاريخ السياسي\nفائز بجائزة كتارا للرواية العربية\nإعلامي وصانع أفلام وثائقية",
    ];

    public function up(): void
    {
        foreach (self::LINES as $slug => $lines) {
            $achiever = Achiever::query()->where('slug', $slug)->first();
            if ($achiever && blank($achiever->getTranslation('hero_lines', 'ar', false))) {
                $achiever->setTranslation('hero_lines', 'ar', $lines)->save();
            }
        }
    }

    public function down(): void
    {
        // Data only; the dashboard owns these records afterwards.
    }
};
