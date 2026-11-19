<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\Page;
use App\Support\Site\OrganizationFeed;
use Illuminate\Contracts\View\View;

class OrganizationController extends Controller
{
    public function index(): View
    {
        return view('site.pages.organizations', [
            'page' => Page::forKey('organizations')->load('letter.media', 'media'),
            'types' => OrganizationType::counted()->where('organizations_count', '>', 0)->values(),
        ]);
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
     * Achievements of an organization. Institutions: their linked achievements (their own and their people's)
     * and their news in one list; universities and federations: the achievements of the organization and its people.
     */
    public function achievements(Organization $organization): View
    {
        $this->authorizeView($organization);
        $news = $organization->news()->published()->with('media')->orderByDesc('happened_at')->get();
        $achievements = $organization->allAchievements();

        return view('site.organizations.'.$organization->template().'-achievements', [
            'organization' => $organization->load(['type', 'media']),
            'listPage' => Page::forKey($organization->template() === 'university' ? 'universities' : 'organizations'),
            'news' => $news,
            'achievements' => $achievements,
            'feed' => OrganizationFeed::of($achievements, $news),
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

    /**
     * «ملف المؤسسة»: its achievements (linked to it or to its people) with its news, its people, and the other
     * institutions (ministries, military, companies).
     *
     * @return array<string, mixed>
     */
    private function dataInstitution(Organization $organization): array
    {
        $news = $organization->news()->published()->with('media')->orderByDesc('happened_at')->get();

        return [
            'feed' => OrganizationFeed::of($organization->allAchievements(), $news),
            'people' => $organization->achievers()->published()->with(['media', 'field'])->get()->unique('id')->values(),
            'sameField' => Organization::query()->listed()->with(['media', 'type'])->whereKeyNot($organization->getKey())
                ->where('organization_type_id', $organization->organization_type_id)->get(),
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
