<?php

namespace Tests\Unit;

use App\Support\Paragraphs;
use PHPUnit\Framework\TestCase;

class ParagraphsTest extends TestCase
{
    private function sentence(int $i): string
    {
        return 'هذه جملة طويلة رقم '.$i.' تصف جانبًا من مسيرة المنجز وإنجازاته في الداخل والخارج بتفصيل واضح.';
    }

    public function test_a_long_story_is_split_at_sentence_ends_without_losing_a_word(): void
    {
        $story = implode(' ', array_map(fn ($i) => $this->sentence($i), range(1, 8)));

        $paragraphs = explode("\n\n", Paragraphs::text($story));

        $this->assertGreaterThan(1, count($paragraphs));
        $this->assertSame($story, implode(' ', $paragraphs));
        foreach ($paragraphs as $paragraph) {
            $this->assertStringEndsWith('.', $paragraph);
        }
    }

    public function test_short_texts_and_written_paragraphs_are_kept(): void
    {
        $this->assertSame('قصة قصيرة.', Paragraphs::text('قصة قصيرة.'));
        $this->assertSame("الفقرة الأولى.\n\nالفقرة الثانية.", Paragraphs::text("الفقرة الأولى.\n\nالفقرة الثانية."));
    }

    public function test_one_letter_abbreviations_do_not_end_a_sentence(): void
    {
        $story = implode(' ', array_map(fn ($i) => 'قالت د. جوخة الحارثية في المقابلة '.$i.' إن الرواية رحلة طويلة مع الذاكرة والمكان والناس.', range(1, 8)));

        foreach (explode("\n\n", Paragraphs::text($story)) as $paragraph) {
            $this->assertStringStartsWith('قالت د. جوخة', $paragraph);
        }
    }

    public function test_html_paragraphs_split_outside_bold_and_links(): void
    {
        $sentences = array_map(fn ($i) => $this->sentence($i), range(1, 8));
        $sentences[2] = 'نال <strong>جائزة كبرى. ثم أخرى</strong> في العام نفسه وسط احتفاء واسع من الجمهور والنقاد والصحافة.';
        $block = '<p>'.implode(' ', $sentences).'</p>';

        $parts = Paragraphs::html($block);

        $this->assertGreaterThan(1, count($parts));
        foreach ($parts as $part) {
            $this->assertMatchesRegularExpression('#^<p>.*</p>$#su', $part);
            $this->assertSame(substr_count($part, '<strong>'), substr_count($part, '</strong>'));
        }
        $this->assertSame(strip_tags($block), implode(' ', array_map('strip_tags', $parts)));
        $this->assertSame(['<ul><li>نقطة</li></ul>'], Paragraphs::html('<ul><li>نقطة</li></ul>'));
        $this->assertSame(['<p>فقرة قصيرة.</p>'], Paragraphs::html('<p>فقرة قصيرة.</p>'));
    }
}
