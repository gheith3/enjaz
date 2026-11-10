<?php

namespace Tests\Unit;

use App\Support\ArabicCount;
use App\Support\ArabicSearch;
use PHPUnit\Framework\TestCase;

class ArabicTextTest extends TestCase
{
    public function test_counts_follow_arabic_number_agreement(): void
    {
        $this->assertSame('لا توجد إنجازات', ArabicCount::of(0, 'achievements'));
        $this->assertSame('إنجاز واحد', ArabicCount::of(1, 'achievements'));
        $this->assertSame('إنجازان', ArabicCount::of(2, 'achievements'));
        $this->assertSame('٧ إنجازات', ArabicCount::of(7, 'achievements'));
        $this->assertSame('٣٧ إنجازًا', ArabicCount::of(37, 'achievements'));
        $this->assertSame('٦ منجزين', ArabicCount::of(6, 'achievers'));
    }

    public function test_search_normalization(): void
    {
        $this->assertSame('جايزه الادب', ArabicSearch::normalize('جائزة الأدب'));
        $this->assertSame('مسوول', ArabicSearch::normalize('مسؤول'));
        $this->assertSame('علي', ArabicSearch::normalize('عَلى'));
    }
}
