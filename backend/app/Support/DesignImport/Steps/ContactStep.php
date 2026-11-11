<?php

namespace App\Support\DesignImport\Steps;

use App\Models\ContactTopic;
use App\Models\Page;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;
use Symfony\Component\DomCrawler\Crawler;

/** تواصل معنا (contact.html): form topics, page texts, contact details and the map image. */
class ContactStep implements ImportStep
{
    public function key(): string
    {
        return 'contact';
    }

    public function description(): string
    {
        return 'Contact page and form topics';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        $html = $design->page('contact.html');
        $t = fn (Crawler $root, string $sel) => $root->filter($sel)->count() ? DesignSource::text($root->filter($sel)->first()) : '';

        if (! ContactTopic::query()->exists() || $force) {
            ContactTopic::query()->delete();
            $html->filter('.ct-topics input')->each(fn (Crawler $i, int $k) => ContactTopic::query()->create(['name' => ['ar' => (string) $i->attr('value')], 'sort_order' => $k + 1]));
        }

        Page::syncFromConfig();
        $page = Page::forKey('contact');
        if (! $page->hero_title || $force) {
            $h1 = $html->filter('#contact-title');
            $span = $t($h1, 'span');
            $page->fill([
                'meta_description' => ['ar' => (string) $html->filter('meta[name="description"]')->attr('content', '')],
                'hero_eyebrow' => ['ar' => $t($html, '.contact-eyebrow')],
                'hero_title' => ['ar' => trim(str_replace($span, '', DesignSource::text($h1))).' |'.$span.'|'],
                'hero_lead' => ['ar' => $t($html, '.contact-intro-copy > p')],
                'hero_cta_label' => ['ar' => trim(preg_replace('/\S+@\S+/', '', $t($html, '.ct-btn')))],
            ]);
            $mail = $html->filter('a[href^="mailto:"]')->first();
            $place = $html->filter('.ct-oman div')->last();
            $chat = $html->filter('.cv-demo .cv-b');
            $page->extra = array_map(fn ($v) => ['ar' => $v], array_filter([
                'email' => $mail->count() ? substr((string) $mail->attr('href'), 7) : '',
                'phones' => implode('، ', $html->filter('.cv-nums a')->each(fn (Crawler $a) => DesignSource::text($a))),
                'handle' => $t($html, '.cv-social-row small span'), 'team_name' => $t($html, '.ct-head b'),
                'chat1' => trim(preg_replace('/[٠-٩:✓\s]+$/u', '', $chat->eq(0)->count() ? DesignSource::text($chat->eq(0)) : '')),
                'chat2' => trim(preg_replace('/[٠-٩:✓\s]+$/u', '', $chat->eq(1)->count() ? DesignSource::text($chat->eq(1)) : '')),
                'chat3' => $t($html, '.cv-demo .cv-txt'),
                'methods_title' => trim(preg_replace('/^\d+/', '', $t($html, '#contact-methods-title'))),
                'form_title' => trim(preg_replace('/^\d+/', '', $t($html, '.contact-message .cv-sec-title'))),
                'location_title' => trim(preg_replace('/^\d+/', '', $t($html, '#contact-location-title'))),
                'place' => $t($place, 'b'), 'country' => $t($place, 'span'), 'place_text' => $t($place, 'p'),
            ])) + ($page->extra ?? []);
            $page->save();
            $img = $html->filter('.cv-place img');
            if ($img->count() && (! $page->hasMedia('cutout') || $force)) {
                $page->clearMediaCollection('cutout');
                $page->addMedia($design->file((string) $img->attr('src')))->preservingOriginal()
                    ->withCustomProperties(['alt' => (string) $img->attr('alt')])->toMediaCollection('cutout');
            }
        }
        $report->done('contact: '.ContactTopic::query()->count().' topics');
        $report->note('Contact: the design shows injazat2040@gmail.com and two phone numbers on this page (the footer has other details); confirm them in صفحات الموقع → تواصل معنا. Email delivery needs SMTP settings in the server .env (MAIL_*); until then messages are kept in «رسائل التواصل».');
    }
}
