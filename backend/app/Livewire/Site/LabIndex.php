<?php

namespace App\Livewire\Site;

use App\Models\LabItem;
use App\Support\ArabicDate;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * كل الاختراعات / كل المنشورات (design lab-all.html): the lab items of one kind with the design's filter groups
 * (inventions: medal, exhibition, year; publications: field, year) and newest / oldest order.
 */
class LabIndex extends Component
{
    #[Locked]
    public string $kind = 'invention';

    #[Url(except: '')]
    public string $medal = '';

    #[Url(except: '')]
    public string $event = '';

    #[Url(except: '')]
    public string $field = '';

    #[Url(except: '')]
    public string $year = '';

    #[Url(except: 'new')]
    public string $sort = 'new';

    public function pick(string $group, string $value): void
    {
        if (array_key_exists($group, $this->groups()) || $group === 'sort') {
            $this->{$group} = $group === 'sort' ? ($value === 'old' ? 'old' : 'new') : $value;
        }
    }

    /** @return Collection<int, LabItem> */
    #[Computed]
    public function items(): Collection
    {
        return LabItem::query()->listed()->ofKind($this->kind)->with(['media', 'organization'])->get();
    }

    /**
     * Filter groups of this kind: key => [label, [value => label]].
     *
     * @return array<string, array{0: string, 1: array<string, string>}>
     */
    public function groups(): array
    {
        $items = $this->items;
        $years = $items->map(fn (LabItem $x) => $x->year())->filter()->unique()->sortDesc()
            ->mapWithKeys(fn ($y) => [(string) $y => ArabicDate::digits($y)])->all();
        $distinct = fn (string $attr) => $items->map(fn (LabItem $x) => (string) $x->{$attr})->filter()->unique()->mapWithKeys(fn ($v) => [$v => $v])->all();

        return $this->kind === 'invention'
            ? [
                'medal' => [__('site.lab.f_medal'), collect(LabItem::MEDALS)->keys()->filter(fn ($m) => $items->contains('medal', $m))
                    ->mapWithKeys(fn ($m) => [$m => __('site.lab.medal_'.$m)])->all()],
                'event' => [__('site.lab.f_event'), $distinct('event')],
                'year' => [__('site.lab.f_year'), $years],
            ]
            : [
                'field' => [__('site.lab.f_field'), $distinct('field')],
                'year' => [__('site.lab.f_year'), $years],
            ];
    }

    public function render(): View
    {
        $groups = $this->groups();
        $list = $this->items->filter(function (LabItem $x) use ($groups) {
            foreach (array_keys($groups) as $g) {
                $value = $g === 'year' ? (string) $x->year() : (string) $x->{$g};
                if ($this->{$g} !== '' && $value !== $this->{$g}) {
                    return false;
                }
            }

            return true;
        });
        $list = $this->sort === 'old' ? $list->sortBy(fn (LabItem $x) => $x->happened_at?->timestamp ?? 0) : $list;

        return view('livewire.site.lab-index', ['groups' => $groups, 'list' => $list->values(), 'total' => $this->items->count()]);
    }
}
