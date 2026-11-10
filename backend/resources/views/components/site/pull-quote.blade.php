@props(['text', 'by' => null, 'role' => null, 'note' => null])
<figure class="pq">
  <span class="pq-mark" aria-hidden="true">”</span>
  <blockquote>{{ $text }}</blockquote>
  @if ($by || $role || $note)
    <figcaption>@if ($by)<b>{{ $by }}</b>@endif @if ($role)<span>{{ $role }}</span>@endif @if ($note)<small>{{ $note }}</small>@endif</figcaption>
  @endif
</figure>
