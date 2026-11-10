<?php

namespace App\View\Components\Site;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SocialLink;
use App\Settings\GeneralSettings;
use App\Settings\SeoSettings;
use App\Support\Site\SiteMedia;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

/**
 * The design's page shell: <head> (meta from the page/record + settings, the design's stylesheets in
 * order, the vp-lock phone scaling), header with crumb and desktop nav, full-screen menu, footer.
 */
class Layout extends Component
{
    public GeneralSettings $general;

    /** @var list<string> */
    public array $stylesheets;

    public string $documentTitle;

    public string $metaDescription;

    public ?string $socialImage;

    /** @var Collection<string, Collection<int, MenuItem>> */
    public Collection $menus;

    /** @var Collection<int, SocialLink> */
    public Collection $socials;

    /**
     * @param  string|null  $styles  key in config('site.styles'); null = shared stylesheets only
     * @param  array{title?: string, url?: string|null, sub?: string|null}  $crumb  section name next to the logo
     */
    public function __construct(
        public ?Page $page = null,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $styles = null,
        public array $crumb = [],
        public string $bodyClass = '',
        public bool $noindex = false,
        public string $htmlClass = '',
    ) {
        $this->general = app(GeneralSettings::class);
        $seo = app(SeoSettings::class);
        $siteName = $this->general->t('site_name');

        $pageTitle = $title ?? $page?->title;
        $this->documentTitle = $pageTitle ? $pageTitle.$seo->title_separator.$siteName : $siteName;
        $this->metaDescription = $description ?? ($page?->meta_description ?: $seo->t('default_description'));
        $this->socialImage = $image
            ?? $page?->getFirstMediaUrl('og_image') ?: SiteMedia::url($seo->default_og_image);
        $this->noindex = $noindex || (bool) $page?->noindex;

        $this->crumb = $crumb + ['title' => $pageTitle ?? $siteName, 'url' => $page?->publicUrl(), 'sub' => null];
        if ($this->crumb['url'] && rtrim($this->crumb['url'], '/') === rtrim(url()->current(), '/')) {
            $this->crumb['url'] = null;
        }

        $this->stylesheets = self::stylesheetsFor($styles);
        $this->menus = MenuItem::grouped();
        $this->socials = SocialLink::query()->forSite()->get();
    }

    /**
     * The design's order: main, site-menu, <page before>, desktop, <page after>, phone-footer; then the site's
     * small overrides (resources/site/css-site/overrides.css).
     *
     * @return list<string>
     */
    public static function stylesheetsFor(?string $key): array
    {
        $page = $key ? config("site.styles.$key", []) : [];

        return [
            ...array_map(
                fn (string $name) => "resources/site/css/$name.css",
                ['main', 'site-menu', ...($page['before'] ?? []), 'desktop', ...($page['after'] ?? []), 'phone-footer'],
            ),
            'resources/site/css-site/overrides.css',
        ];
    }

    public function logoUrl(): string
    {
        return SiteMedia::url($this->general->logo, 'svg/logo.svg');
    }

    public function render(): View
    {
        return view('components.site.layout');
    }
}
