<x-site.layout :page="$page" styles="home" body-class="home">
  {{-- The design has no visible h1 on the home page; one for screen readers and search engines. --}}
  <h1 class="sr-only">{{ $page->hero_title ?: app(\App\Settings\GeneralSettings::class)->t('site_name') }}</h1>
  @include('site.partials.sections')
</x-site.layout>
