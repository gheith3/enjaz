<?php

namespace App\Livewire\Pages;

use App\Models\Achievement;
use App\Models\AchievementMainType;
use App\Models\PublicFigure;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

class AchieversPage extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'tailwind';

    public null|string $searchQuery = null;
    public null|int $main = null;
    public null|int $sub = null;
    public string $selected = 'all';

    public function mount(): void
    {
        // Remove this since we don't need to set achievers anymore
        // $this->setAchievers();
    }

    public function search(): void
    {
        $this->searchQuery = str($this->searchQuery)->isNotEmpty()
            ? $this->searchQuery : null;
        // Remove this since we don't need to set achievers anymore
        // $this->setAchievers();
    }

    public function updatingSearchQuery(): void
    {
        $this->resetPage();
    }

    public function setType($key): void
    {
        $this->resetPage();
        $this->selected = $key;
        if ($key == 'all') {
            $this->main = null;
            $this->sub = null;
            // Remove this since we don't need to set achievers anymore
            // $this->setAchievers();
            return;
        }

        $keys = str($key)->explode('-');
        $this->main = null;
        $this->sub = null;

        if ($keys[0] == 'mt') {
            $this->main = $keys[1];
        } else {
            $this->main = $keys[1];
            $this->sub = $keys[2];
        }
        // Remove this since we don't need to set achievers anymore
        // $this->setAchievers();
    }

    public function getCategories(): array
    {
        $list = [];
        $list['all'] = 'الكل';
        foreach (AchievementMainType::query()->with('subTypes')->get() as $type) {
            $list['mt-' . $type->id . '-00'] = $type->title_ar;
            foreach ($type->subTypes as $subType) {
                $list["st-{$type->id}-{$subType->id}"] = $subType->title_ar;
            }
        }
        return $list;
    }

    public function getAchievers(): LengthAwarePaginator
    {
        return PublicFigure::query()
            ->latest()
            ->when(str($this->searchQuery)->isNotEmpty(), function ($query) {
                $query
                    ->where('name_ar', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('name_en', 'like', '%' . $this->searchQuery . '%');
            })
            ->when($this->main != null, function ($query) {
                $query->whereHas('achievements', function ($query) {
                    $query->where('achievement_main_type_id', $this->main);
                });
            })
            ->when($this->sub != null, function ($query) {
                $query->whereHas('achievements', function ($query) {
                    $query->where('sub_achievement_type_id', $this->sub);
                });
            })
            ->paginate(20);
    }

    public function render(): View
    {
        return view('livewire.pages.achievers-page', [
            'achievers' => $this->getAchievers(),
        ]);
    }
}
