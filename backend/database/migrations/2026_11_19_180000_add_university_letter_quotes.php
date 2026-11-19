<?php

use App\Models\Award;
use App\Models\Letter;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;

/*
 * «نافذة المقام» shows a highlighted quote from the president's word (*stars* = gold highlight). Fills it for the five
 * universities and the culture award's board chairman from their own words, only when empty, so dashboard edits are
 * kept. Editable in /admin (كلمة الرئيس).
 */
return new class extends Migration
{
    private const QUOTES = [
        'squ' => '…وإن *عُمان الغد هي أنتم* بكل قدراتكم وطموحكم.',
        'unizwa' => '…نؤمن إيمانا راسخا بأن *الفكر الإيجابي سر من أسرار النجاح* للفرد والأمة معا',
        'sohar' => '…*أول جامعة خاصة في السلطنة*، وثاني جامعة يتم إنشاؤها بعد جامعة السلطان قابوس',
        'dhofar' => '…لدينا إيمان راسخ بأنكم *ستصبحون البناة الحقيقيين للمستقبل*',
        'utas' => '…نحو خمسين ألف طالب وطالبة *يتخذون من الجامعة بيتًا ثانيًا* ينمون فيه ويتعلمون',
    ];

    private const AWARD_QUOTE = '…إلا شاهدٌ على ما يزخر به هذا الوطن من *طاقات مبدعة في الثقافة والفنون والآداب*.';

    public function up(): void
    {
        foreach (self::QUOTES as $slug => $quote) {
            $this->fill(Organization::query()->where('slug', $slug)->first()?->letter, $quote);
        }
        $this->fill(Award::query()->where('slug', 'culture')->first()?->letter, self::AWARD_QUOTE);
    }

    private function fill(?Letter $letter, string $quote): void
    {
        if ($letter && blank($letter->getTranslation('quote', 'ar', false))) {
            $letter->setTranslation('quote', 'ar', $quote)->save();
        }
    }

    public function down(): void
    {
        foreach (self::QUOTES as $slug => $quote) {
            $letter = Organization::query()->where('slug', $slug)->first()?->letter;
            if ($letter && $letter->getTranslation('quote', 'ar', false) === $quote) {
                $letter->forgetTranslation('quote', 'ar')->save();
            }
        }
    }
};
