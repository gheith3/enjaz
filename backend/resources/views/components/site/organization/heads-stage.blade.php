@props(['heads', 'title' => null])
{{-- «من تعاقبوا على قيادتها» / «رؤساء الاتحاد» (css org-heads, js sections/org-heads): a stage that hands the leadership on —
     each head's portrait opens in a circle of light, the big year counts to his year, the name rises word by word, and a gold
     marker runs along the line of portraits. Plays by itself once reached, pauses on hover; a portrait jumps to its head.
     $heads: OrganizationLeader models in order. --}}
@php $d = fn ($n) => \App\Support\ArabicDate::digits($n); @endphp
<div class="op-heads oh" data-op-heads>
  @if ($title)<h3>{{ $title }}</h3>@endif
  <div class="oh-stage">
    <span class="oh-year" aria-hidden="true" data-oh-year>{{ $heads->first()->from_year ? $d($heads->first()->from_year) : '' }}</span>
    @foreach ($heads as $head)
      @php $photo = \App\Support\Media\Src::of($head->image('photo')); $name = collect([$head->title, $head->name])->filter()->implode(' '); @endphp
      <article @class(['oh-slide', 'on' => $loop->first, 'is-now' => $head->is_current]) data-year="{{ $head->from_year }}" aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
        <div class="oh-photo"><span class="oh-ring" aria-hidden="true"></span>
          <span class="oh-pic">@if ($photo)<img src="{{ $photo }}" alt="{{ $name }}" loading="lazy">@else<b>{{ mb_substr((string) $head->name, 0, 1) }}</b>@endif</span></div>
        <div class="oh-info">
          <span class="oh-no">{{ $d(str_pad($loop->iteration, 2, '0', STR_PAD_LEFT)) }} / {{ $d(str_pad($heads->count(), 2, '0', STR_PAD_LEFT)) }}</span>
          <small class="oh-yrs">@if ($head->from_year){{ $d($head->from_year) }}@endif @if ($head->to_year)– {{ $d($head->to_year) }}@elseif ($head->is_current){{ $head->from_year ? '– ' : '' }}{{ __('site.organizations.current_head') }}@endif</small>
          <h4 class="oh-name">@foreach (preg_split('/\s+/u', $name) as $w)<span style="--w: {{ $loop->index }}">{{ $w }}</span> @endforeach</h4>
          @if ($head->role)<p class="oh-role">{{ $head->role }}</p>@endif
          @if ($head->note)<span class="op-decree">{{ $head->note }}</span>@endif
        </div>
      </article>
    @endforeach
  </div>
  <div class="oh-track" role="tablist" aria-label="{{ $title }}" style="--n: {{ $heads->count() }}">
    <span class="oh-line" aria-hidden="true"><i data-oh-fill></i></span>
    @foreach ($heads as $head)
      @php $photo = \App\Support\Media\Src::of($head->image('photo')); @endphp
      <button type="button" role="tab" @class(['oh-dot', 'on' => $loop->first]) aria-selected="{{ $loop->first ? 'true' : 'false' }}" aria-label="{{ collect([$head->title, $head->name])->filter()->implode(' ') }}">
        <span class="oh-face">@if ($photo)<img src="{{ $photo }}" alt="" loading="lazy">@else{{ mb_substr((string) $head->name, 0, 1) }}@endif</span>
        <small>@if ($head->from_year){{ $d($head->from_year) }}@else{{ __('site.organizations.current_head') }}@endif</small><i class="oh-prog" aria-hidden="true"></i>
      </button>
    @endforeach
  </div>
</div>
