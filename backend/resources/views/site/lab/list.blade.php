{{-- Design: lab-all.html. Filters and results: Livewire LabIndex. --}}
<x-site.layout :title="__('site.lab.all_title_'.$kind)" :description="$page->meta_description" styles="lab-all"
    :crumb="['title' => $page->label('crumb', __('site.lab.crumb')), 'url' => route('lab.index'), 'sub' => __('site.lab.'.($kind === 'invention' ? 'inventions' : 'publications'))]">
  <livewire:site.lab-index :kind="$kind" />
</x-site.layout>
