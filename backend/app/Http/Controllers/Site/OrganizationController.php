<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Page;
use Illuminate\Contracts\View\View;

class OrganizationController extends Controller
{
    public function index(): View
    {
        return view('site.pages.organizations', ['page' => Page::forKey('organizations')->load('letter.media', 'media')]);
    }

    public function universities(): View
    {
        return view('site.pages.universities', ['page' => Page::forKey('universities')]);
    }

    /**
     * One route for every organization; the type picks the design (institution / university / federation).
     */
    public function show(Organization $organization): View
    {
        $this->authorizeView($organization);
        $organization->load(['type', 'media', 'stats', 'units', 'leaders.media', 'videos', 'milestones', 'sources', 'letter.media']);

        return view('site.organizations.'.$organization->template(), [
            'organization' => $organization,
            'listPage' => Page::forKey($organization->template() === 'university' ? 'universities' : 'organizations'),
        ] + $this->{'data'.ucfirst($organization->template())}($organization));
    }

    /**
     * Achievements of an organization. Institutions: their achievements and news (institution-achievements.html);
     * universities and federations: the achievements of the organization and its people (phase 2c).
     */
    public function achievements(Organization $organization): View
    {
        $this->authorizeView($organization);

        return view('site.organizations.'.$organization->template().'-achievements', [
            'organization' => $organization->load(['type', 'media']),
            'listPage' => Page::forKey($organization->template() === 'university' ? 'universities' : 'organizations'),
            'news' => $organization->news()->published()->with('media')->orderByDesc('happened_at')->get(),
            'achievements' => $organization->allAchievements(),
        ]);
    }

    /** People of a university (design uni-people.html). */
    public function people(Organization $organization): View
    {
        $this->authorizeView($organization);
        abort_if($organization->template() === 'institution', 404);

        return view('site.organizations.university-people', [
            'organization' => $organization->load(['type', 'media']),
            'listPage' => Page::forKey($organization->template() === 'university' ? 'universities' : 'organizations'),
        ]);
    }

    private function authorizeView(Organization $organization): void
    {
        abort_unless($organization->isLive() || auth()->user()?->can('View:Organization'), 404);

        if ($organization->template() === 'university') {
            request()->attributes->set('menu_section', '/universities');
        }
    }

    /** @return array<string, mixed> */
    private function dataInstitution(Organization $organization): array
    {
        return [
            'news' => $organization->news()->published()->with('media')->orderByDesc('happened_at')->limit(5)->get(),
            'sameField' => Organization::query()->listed()->with('media')->whereKeyNot($organization->getKey())
                ->whereHas('type', fn ($q) => $q->where('template', 'institution'))->get(),
        ];
    }

    /** @return array<string, mixed> */
    private function dataUniversity(Organization $organization): array
    {
        return ['achievements' => $organization->allAchievements()];
    }

    /** @return array<string, mixed> */
    private function dataFederation(Organization $organization): array
    {
        return ['achievements' => $organization->allAchievements()];
    }
}
