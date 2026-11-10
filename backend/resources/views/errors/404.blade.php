<x-site.layout :title="__('site.not_found_title')" :crumb="['title' => __('site.not_found_title'), 'url' => url('/')]" noindex>
  <section class="section wrap section-pad">
    <div class="sec-title"><h2>{{ __('site.not_found_title') }}</h2></div>
    <p>{{ __('site.not_found_text') }}</p>
    <p><a class="btn-more" href="{{ url('/') }}">{{ __('site.back_home') }}</a></p>
  </section>
</x-site.layout>
