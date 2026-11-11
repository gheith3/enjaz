<?php

namespace Tests\Feature\Site;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Livewire\Site\ContactForm;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\ContactTopic;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Page::syncFromConfig();
        Page::forKey('contact')->forceFill(['extra' => ['email' => ['ar' => 'team@example.com'], 'phones' => ['ar' => '9022 9422، 9955 5564']]])->save();
    }

    public function test_contact_page_shows_details_and_topics(): void
    {
        ContactTopic::query()->create(['name' => ['ar' => 'مشاركة إنجاز']]);

        $this->get('/contact')->assertOk()->assertSee('team@example.com')->assertSee('9955 5564')->assertSee('مشاركة إنجاز')
            ->assertSee('class="flush contact-page"', false);
    }

    public function test_a_message_is_saved_with_its_attachment_and_mailed(): void
    {
        Mail::fake();
        Storage::fake(ContactMessage::DISK);
        $topic = ContactTopic::query()->create(['name' => ['ar' => 'شراكة']]);

        Livewire::test(ContactForm::class)
            ->set('topic', $topic->id)->set('name', 'سالم')->set('email', 'salim@example.com')->set('message', 'أود التعاون معكم')
            ->set('attachment', UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'))
            ->call('send')->assertHasNoErrors()->assertSet('sent', true);

        $msg = ContactMessage::query()->sole();
        $this->assertSame('شراكة', $msg->topic);
        $this->assertSame('cv.pdf', $msg->attachment_name);
        Storage::disk(ContactMessage::DISK)->assertExists($msg->attachment_path);
        Mail::assertSent(ContactMessageReceived::class, fn ($m) => $m->hasTo('team@example.com') && $m->hasReplyTo('salim@example.com'));
    }

    public function test_validation_and_honeypot(): void
    {
        Mail::fake();
        Livewire::test(ContactForm::class)->set('email', 'not-an-email')->call('send')->assertHasErrors(['name', 'email', 'message']);
        Livewire::test(ContactForm::class)->set('website', 'spam')->set('name', 'x')->set('email', 'x@example.com')->set('message', 'buy')
            ->call('send')->assertSet('sent', true);
        $this->assertSame(0, ContactMessage::query()->count());
        Mail::assertNothingSent();
    }

    public function test_editor_reads_a_message_in_the_inbox(): void
    {
        $this->actingAs($this->userWithRole('editor'));
        $msg = ContactMessage::query()->create(['name' => 'سالم', 'email' => 'salim@example.com', 'message' => 'مرحبا', 'topic' => 'استفسار']);

        $this->get(ContactMessageResource::getUrl('index'))->assertOk()->assertSee('سالم');
        $this->get(ContactMessageResource::getUrl('view', ['record' => $msg]))->assertOk()->assertSee('مرحبا');
        $this->assertNotNull($msg->fresh()->read_at);
    }
}
