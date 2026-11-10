{{-- The page's enabled sections in the editors' order (dashboard: صفحات الموقع -> أقسام الصفحة). --}}
@foreach ($page->visibleSections() as $section)
  @include($section->view(), ['section' => $section])
@endforeach
