<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\ManageGeneralSettings;
use App\Filament\Pages\ManageSeoSettings;
use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Resources\MenuItems\MenuItemResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Redirects\RedirectResource;
use App\Filament\Resources\SocialLinks\SocialLinkResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_without_a_role_cannot_open_the_dashboard(): void
    {
        $this->actingAs($this->userWithRole(null))->get('/admin')->assertForbidden();
    }

    public function test_super_admin_can_open_every_screen(): void
    {
        Page::syncFromConfig();
        $menuItem = MenuItem::factory()->create();
        $this->actingAs($this->userWithRole('super_admin'));

        $urls = [
            '/admin',
            PageResource::getUrl('index'),
            PageResource::getUrl('edit', ['record' => Page::first()]),
            MenuItemResource::getUrl('index'),
            MenuItemResource::getUrl('create'),
            MenuItemResource::getUrl('edit', ['record' => $menuItem]),
            SocialLinkResource::getUrl('index'),
            RedirectResource::getUrl('index'),
            UserResource::getUrl('index'),
            UserResource::getUrl('create'),
            ActivityResource::getUrl('index'),
            ManageGeneralSettings::getUrl(),
            ManageSeoSettings::getUrl(),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_editor_edits_content_but_not_settings_or_users(): void
    {
        Page::syncFromConfig();
        $this->actingAs($this->userWithRole('editor'));

        $this->get(PageResource::getUrl('index'))->assertOk();
        $this->get(MenuItemResource::getUrl('index'))->assertOk();
        $this->get(ManageGeneralSettings::getUrl())->assertForbidden();
        $this->get(UserResource::getUrl('index'))->assertForbidden();
        $this->get(RedirectResource::getUrl('index'))->assertForbidden();
    }

    public function test_admin_manages_settings_but_not_users(): void
    {
        $this->actingAs($this->userWithRole('admin'));

        $this->get(ManageGeneralSettings::getUrl())->assertOk();
        $this->get(UserResource::getUrl('index'))->assertForbidden();
    }

    public function test_pages_cannot_be_created_or_deleted_from_the_dashboard(): void
    {
        $this->assertFalse(PageResource::canCreate());
    }
}
