<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SyncSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_roles_pages_and_the_first_super_admin(): void
    {
        config(['site.admin_email' => 'owner@example.com']);
        $owner = User::factory()->create(['email' => 'owner@example.com']);

        $this->artisan('site:sync')->assertSuccessful();

        $this->assertTrue($owner->fresh()->hasRole('super_admin'));
        $this->assertEqualsCanonicalizing(['super_admin', 'admin', 'editor'], Role::pluck('name')->all());
        $this->assertSame(count(config('site.pages')), Page::count());
        $this->assertSame(count(config('site.pages.home.sections')), Page::where('key', 'home')->first()->sections()->count());
    }

    public function test_running_it_again_keeps_role_changes_and_page_order(): void
    {
        $this->artisan('site:sync')->assertSuccessful();
        Role::findByName('editor')->revokePermissionTo('Update:MenuItem');
        $section = Page::where('key', 'home')->first()->sections()->where('key', 'home.partners')->first();
        $section->update(['sort_order' => 0, 'is_enabled' => false]);

        $this->artisan('site:sync')->assertSuccessful();

        $this->assertFalse(Role::findByName('editor')->hasPermissionTo('Update:MenuItem'));
        $this->assertSame(0, $section->fresh()->sort_order);
        $this->assertFalse($section->fresh()->is_enabled);
    }
}
