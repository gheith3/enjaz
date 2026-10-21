<?php

use App\Enums\ContactMessageStatus;
use App\Livewire\Pages\About;
use App\Livewire\Pages\Contact;
use App\Livewire\Pages\Media;
use App\Livewire\Pages\MediaImageAlbums;
use App\Livewire\Pages\MediaImageAlbumShow;
use App\Livewire\Pages\MediaPublications;
use App\Livewire\Pages\MediaVideos;
use App\Models\ContactUsMessage;
use App\Models\ImageMedia;
use App\Models\News;
use App\Models\PublicationMedia;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\VideoMedia;
use App\Settings\AboutSettings;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // The suite asserts Arabic copy; CI boots with APP_LOCALE=en from .env.example.
    app()->setLocale('ar');

    AboutSettings::fake([
        'intro_text_ar' => 'نص تعريفي تجريبي',
        'vision_ar' => 'رؤية تجريبية',
        'message_ar' => 'رسالة تجريبية',
        'values_ar' => 'قيم تجريبية',
        'founder_message_ar' => 'رسالة المؤسس التجريبية',
        'founder_name_ar' => 'المؤسس التجريبي',
        'founder_position_ar' => 'المؤسس والرئيس',
    ]);

    GeneralSettings::fake([
        'phone' => '90229422',
        'phone_secondary' => '99555564',
        'email' => 'injazat2040@gmail.com',
        'social_media_accounts' => [
            ['platform' => 'instagram', 'url' => 'https://instagram.com/injazat_o'],
        ],
    ]);
});

test('about page renders settings, testimonials, team and news', function () {
    Testimonial::create([
        'person_name_ar' => 'صاحب السمو التجريبي',
        'position_ar' => 'منصب تجريبي',
        'quote_ar' => 'شهادة تجريبية',
        'is_published' => true,
        'sort_order' => 1,
    ]);

    TeamMember::create([
        'name_ar' => 'عضو الفريق التجريبي',
        'position_ar' => 'منصب الفريق',
        'is_published' => true,
        'sort_order' => 1,
    ]);

    News::create([
        'title_ar' => 'خبر انجازاتنا التجريبي',
        'body_ar' => 'نص الخبر',
        'published_at' => now(),
        'show_on_about' => true,
        'is_published' => true,
        'slug' => 'about-news-test',
    ]);

    Livewire::test(About::class)
        ->assertOk()
        ->assertSee('رؤية تجريبية')
        ->assertSee('رسالة المؤسس التجريبية')
        ->assertSee('المؤسس التجريبي')
        ->assertSee('عضو الفريق التجريبي')
        ->assertSee('quote-card', false);
});

test('media page renders and filters news by search', function () {
    News::create([
        'title_ar' => 'حفل تدشين الكتاب',
        'body_ar' => 'نص',
        'cover_image_path' => 'seed/news-one.jpg',
        'published_at' => now(),
        'is_published' => true,
        'slug' => 'media-news-one',
    ]);

    News::create([
        'title_ar' => 'قرعة متكافئة',
        'body_ar' => 'نص',
        'cover_image_path' => 'seed/news-two.jpg',
        'published_at' => now(),
        'is_published' => true,
        'slug' => 'media-news-two',
    ]);

    Livewire::test(Media::class)
        ->assertOk()
        ->assertSee('حفل تدشين الكتاب')
        ->assertSee('قرعة متكافئة')
        ->set('q', 'حفل')
        ->assertSee('حفل تدشين الكتاب')
        ->assertDontSee('قرعة متكافئة');
});

test('media listing pages render published records', function () {
    VideoMedia::create([
        'title_ar' => 'لقاء تجريبي',
        'url' => 'https://example.com/video',
        'is_published' => true,
        'slug' => 'video-test',
    ]);

    PublicationMedia::create([
        'title_ar' => 'إصدار تجريبي',
        'file_path' => 'seed/publication.pdf',
        'is_published' => true,
        'slug' => 'publication-test',
    ]);

    ImageMedia::create([
        'title_ar' => 'ألبوم تجريبي',
        'images' => ['seed/album-1.jpg', 'seed/album-2.jpg'],
        'is_published' => true,
        'slug' => 'album-test',
    ]);

    Livewire::test(MediaVideos::class)->assertOk()->assertSee('لقاء تجريبي', false);
    Livewire::test(MediaPublications::class)->assertOk()->assertSee('إصدار تجريبي', false);
    Livewire::test(MediaImageAlbums::class)->assertOk();
});

test('image album show renders images and blocks unpublished albums', function () {
    $published = ImageMedia::create([
        'title_ar' => 'ألبوم منشور',
        'images' => ['seed/album-1.jpg'],
        'is_published' => true,
        'slug' => 'album-published',
    ]);

    $draft = ImageMedia::create([
        'title_ar' => 'ألبوم غير منشور',
        'images' => ['seed/album-2.jpg'],
        'is_published' => false,
        'slug' => 'album-draft',
    ]);

    Route::get('/_test/albums/{imageMedia:slug}', MediaImageAlbumShow::class);

    $this->withoutVite();
    $this->get('/_test/albums/album-published')->assertOk()->assertSee('ألبوم منشور');
    $this->get('/_test/albums/album-draft')->assertNotFound();
});

test('contact form validates input and stores the message', function () {
    Livewire::test(Contact::class)
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'message'])
        ->set('name', 'مستخدم تجريبي')
        ->set('email', 'not-an-email')
        ->set('message', 'مرحبا')
        ->call('submit')
        ->assertHasErrors(['email'])
        ->set('email', 'user@example.com')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true)
        ->assertSee('شكرًا لتواصلكم معنا');

    $message = ContactUsMessage::sole();

    expect($message->name)->toBe('مستخدم تجريبي')
        ->and($message->email)->toBe('user@example.com')
        ->and($message->message)->toBe('مرحبا')
        ->and($message->ip_address)->toBe('127.0.0.1')
        ->and($message->status)->toBe(ContactMessageStatus::New);
});

test('contact form is rate limited to five submissions per minute', function () {
    $component = Livewire::test(Contact::class);

    foreach (range(1, 5) as $i) {
        $component
            ->set('name', 'مستخدم '.$i)
            ->set('email', "user{$i}@example.com")
            ->set('message', 'رسالة '.$i)
            ->call('submit')
            ->assertHasNoErrors();
    }

    $component
        ->set('name', 'مستخدم إضافي')
        ->set('email', 'extra@example.com')
        ->set('message', 'رسالة إضافية')
        ->call('submit')
        ->assertHasErrors(['message']);

    expect(ContactUsMessage::count())->toBe(5);
});

test('about page renders in the site layout and contact page hides the footer', function () {
    Route::get('/_test/about', About::class);
    Route::get('/_test/contact', Contact::class);

    $this->withoutVite();

    $this->get('/_test/about')
        ->assertOk()
        ->assertSee('إنجاز | عنا')
        ->assertSee('site-footer', false);

    $this->get('/_test/contact')
        ->assertOk()
        ->assertSee('تواصل معنا')
        ->assertDontSee('site-footer');
});
