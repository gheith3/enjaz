<?php

namespace App\Livewire\Pages;

use App\Models\Organization;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.site')]
class InstitutionShow extends Component
{
    public Organization $organization;

    public function mount(Organization $organization): void
    {
        abort_unless($organization->is_published, 404);

        $this->organization = $organization->loadMissing('organizationType');
    }

    public function render()
    {
        $news = $this->organization->news()->published()->latest('published_at')->get();

        $items = $news->isNotEmpty()
            ? $news
            : $this->organization->achievements()->published()->latest('achieved_at')->get();

        $more = Organization::query()
            ->published()
            ->whereKeyNot($this->organization->getKey())
            ->where('organization_type_id', $this->organization->organization_type_id)
            ->ordered()
            ->take(4)
            ->get();

        if ($more->count() < 4) {
            $fill = Organization::query()
                ->published()
                ->whereKeyNot($this->organization->getKey())
                ->whereNotIn('id', $more->modelKeys())
                ->ordered()
                ->take(4 - $more->count())
                ->get();

            $more = $more->concat($fill);
        }

        return view('livewire.pages.institution-show', [
            'items' => $items,
            'more' => $more,
        ]);
    }
}
