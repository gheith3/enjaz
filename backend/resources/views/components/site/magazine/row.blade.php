{{-- An article row of the magazine (design .mag-row): cover, or the quote mark when the article has none. --}}
@props(['article', 'withId' => false])
@php $a = $article; $cover = $a->imageUrl('cover', 'card'); @endphp
<article class="mag-row" data-cat="{{ $a->category?->slug }}" @if ($withId) id="a-{{ $a->slug }}" @endif>
  <a class="cover-link" href="{{ $a->url() }}" aria-label="{{ $a->title }}"></a>
  @if ($cover)<img class="gold-ph" src="{{ $cover }}" alt="" loading="lazy">@else<span class="mr-type" aria-hidden="true">«</span>@endif
  <div class="mr-b"><p class="kick">@if ($a->category){{ $a->category->name }} · @endif{{ $a->minutesLabel() }}</p><h3>{{ $a->title }}</h3>
    @if ($a->writer)<p class="mr-by"><span class="av sm" aria-hidden="true">@if ($p = $a->writer->photoUrl())<img src="{{ $p }}" alt="">@endif</span>{{ $a->writer->name }}</p>@endif</div>
</article>
