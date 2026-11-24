{{-- Stage bar and legend: $counts (stage => n), $total. --}}
<span class="rh-bar">
    @foreach (\App\Support\ProfileReviews\ReviewBoard::STAGES as $stage => $label)
        @if ($total && $counts[$stage])<span class="rh-{{ $stage }}" style="width: {{ round($counts[$stage] * 100 / $total, 1) }}%"></span>@endif
    @endforeach
</span>
<span class="rh-legend">
    @foreach (\App\Support\ProfileReviews\ReviewBoard::STAGES as $stage => $label)
        <span><i class="rh-dot rh-{{ $stage }}"></i>{{ $label }} <b>{{ $counts[$stage] }}</b></span>
    @endforeach
</span>
