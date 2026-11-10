<?php

namespace App\Support\DesignImport\Steps;

use App\Models\SocialLink;
use App\Settings\GeneralSettings;
use App\Settings\SeoSettings;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Site identity and contact details from the design's <head> and footer (index.html): settings, the
 * site's social accounts, logo / favicon / default sharing image (uploaded to OneBox media/settings).
 */
class SiteStep implements ImportStep
{
    private const PLATFORMS = [
        'إنستغرام' => 'instagram', 'واتساب' => 'whatsapp', 'إكس' => 'x', 'فيسبوك' => 'facebook',
        'لينكدإن' => 'linkedin', 'يوتيوب' => 'youtube', 'سناب' => 'snapchat', 'تيك' => 'tiktok',
    ];

    public function key(): string
    {
        return 'site';
    }

    public function description(): string
    {
        return 'Site settings, social accounts, logo and sharing image';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        $home = $design->page('index.html');
        $general = app(GeneralSettings::class);
        $seo = app(SeoSettings::class);

        $description = $home->filter('meta[name="description"]')->attr('content');
        $footer = $home->filter('footer.site-footer');

        $this->fill($general, 'tagline', ['ar' => $description, 'en' => ''], $force);
        $this->fill($general, 'theme_color', $home->filter('meta[name="theme-color"]')->attr('content'), $force);
        $this->fill($general, 'copyright', ['ar' => DesignSource::text($footer->filter('p.copy')), 'en' => ''], $force);

        $details = $footer->filter('.footer-details li');
        $phones = $details->filter('a[href^="tel:"] span')->each(fn (Crawler $n) => DesignSource::text($n));
        $email = $details->filter('a[href^="mailto:"] span')->each(fn (Crawler $n) => DesignSource::text($n))[0] ?? null;
        $address = $details->reduce(fn (Crawler $n) => $n->filter('a')->count() === 0)
            ->each(fn (Crawler $n) => DesignSource::text($n))[0] ?? '';
        $this->fill($general, 'phones', $phones, $force);
        $this->fill($general, 'email', $email, $force);
        $this->fill($general, 'address', ['ar' => $address, 'en' => ''], $force);

        $general->logo = $this->upload($design, 'assets/svg/logo.svg', $general->logo, $force);
        $general->favicon = $this->upload($design, 'assets/svg/ornament.svg', $general->favicon, $force);
        $general->save();

        $this->fill($seo, 'default_description', ['ar' => $description, 'en' => ''], $force);
        $seo->default_og_image = $this->upload($design, 'assets/img/og-default.jpg', $seo->default_og_image, $force);
        $seo->save();
        $report->done('settings: name, tagline, contact details, logo, favicon, sharing image');

        $this->socials($footer, $report, $force);
        $this->compareContactPage($design, $phones, $email, $report);
    }

    private function socials(Crawler $footer, ImportReport $report, bool $force): void
    {
        if (SocialLink::query()->whereNull('linkable_type')->exists() && ! $force) {
            $report->done('social accounts: kept (already in the database)');

            return;
        }

        SocialLink::query()->whereNull('linkable_type')->delete();
        $footer->filter('.footer-socials > a, .footer-socials > span')->each(function (Crawler $node, int $i) {
            $label = $node->attr('aria-label') ?: $node->attr('title');
            $platform = collect(self::PLATFORMS)->first(fn ($p, $word) => str_contains((string) $label, $word));
            if (! $platform) {
                return;
            }
            $url = $node->nodeName() === 'a' ? $node->attr('href') : null;
            $handle = $url && in_array($platform, ['instagram', 'x'], true)
                ? '@'.trim((string) parse_url($url, PHP_URL_PATH), '/')
                : null;

            SocialLink::create(['platform' => $platform, 'url' => $url, 'handle' => $handle, 'sort_order' => $i + 1]);
        });
        $report->done('social accounts: '.SocialLink::query()->whereNull('linkable_type')->count());
    }

    /** @param  list<string>  $phones */
    private function compareContactPage(DesignSource $design, array $phones, ?string $email, ImportReport $report): void
    {
        $contact = $design->page('contact.html');
        $contactPhones = $contact->filter('a[href^="tel:"]')->each(fn (Crawler $n) => preg_replace('/[^\d+]/', '', (string) $n->attr('href')));
        $contactEmails = $contact->filter('a[href^="mailto:"]')->each(fn (Crawler $n) => strtok(substr((string) $n->attr('href'), 7), '?'));
        $footerPhones = array_map(fn ($p) => preg_replace('/[^\d+]/', '', $p), $phones);

        $otherPhones = array_values(array_diff(array_unique($contactPhones), $footerPhones));
        $otherEmails = array_values(array_diff(array_unique($contactEmails), [$email]));
        if ($otherPhones || $otherEmails) {
            $report->note('Contact details differ between the design footer and contact.html. Imported the footer ('
                .implode(', ', $phones).', '.$email.'); contact.html also has: '.implode(', ', [...$otherPhones, ...$otherEmails])
                .'. Confirm the real values in the dashboard (الإعدادات -> هوية الموقع والتواصل).');
        }
    }

    private function fill(object $settings, string $property, mixed $value, bool $force): void
    {
        $current = $settings->{$property};
        $empty = is_array($current) ? collect($current)->filter()->isEmpty() : blank($current);
        if (($force || $empty) && filled($value)) {
            $settings->{$property} = $value;
        }
    }

    private function upload(DesignSource $design, string $relative, ?string $current, bool $force): ?string
    {
        if (filled($current) && ! $force) {
            return $current;
        }

        return Storage::disk(config('media-library.disk_name'))
            ->putFileAs('media/settings', new File($design->file($relative)), basename($relative), 'public') ?: $current;
    }
}
