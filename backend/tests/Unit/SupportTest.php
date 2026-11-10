<?php

namespace Tests\Unit;

use App\Models\Redirect;
use App\Support\Localized;
use Tests\TestCase;

class SupportTest extends TestCase
{
    public function test_localized_falls_back_to_arabic_then_any_translation(): void
    {
        app()->setLocale('en');
        $this->assertSame('English', Localized::get(['ar' => 'عربي', 'en' => 'English']));
        $this->assertSame('عربي', Localized::get(['ar' => 'عربي', 'en' => '']));
        $this->assertSame('only', Localized::get(['fr' => 'only']));
        $this->assertSame('', Localized::get(null));
    }

    public function test_redirect_paths_are_normalized(): void
    {
        $this->assertSame('/achievers.html', Redirect::normalize('achievers.html'));
        $this->assertSame('/', Redirect::normalize('/'));
        $this->assertSame('/federation.html?org=oman-fa', Redirect::normalize('/federation.html/?org=oman-fa'));
    }
}
