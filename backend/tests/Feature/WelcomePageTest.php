<?php

namespace Tests\Feature;

use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    public function test_welcome_page_has_the_theme_switch_and_favicon(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('window.appTheme', false)
            ->assertSee('data-theme-toggle', false)
            ->assertSee('/favicon.svg', false);
    }
}
