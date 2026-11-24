<x-filament-panels::page>
    @php
        $review = $this->record;
        $progress = $review->progress();
        $sections = $this->editedSections();
        $additions = $review->additions ?? [];
        $gallery = $review->gallery ?? [];
        $decided = $review->status === 'approved';
        $invitation = $review->invitation();
        $parts = collect(['journey' => 'محطات المسيرة', 'socials' => 'حسابات التواصل', 'videos' => 'المقابلات واللقاءات', 'contact' => 'بيانات التواصل (خاصة)'])
            ->filter(fn ($label, $part) => $review->changed($part));
        $socialLabels = \App\Support\ProfileReviews\ProfileReviewItems::SOCIALS;
        $nothing = $sections->isEmpty() && ! $additions && ! $gallery && $parts->isEmpty();
        $badge = fn ($ok) => $ok ? '<em class="prv-ok">اعتُمد</em>' : '<em class="prv-danger">لم يُعتمد</em>';
    @endphp

    <div class="prv">
        {{-- The link to send --}}
        @if (in_array($review->status, ['sent', 'opened', 'returned'], true))
            <section class="prv-card prv-link" x-data="{ copied: false }">
                <div class="prv-link__head">
                    <h2>رابط المراجعة</h2>
                    <span class="prv-muted">
                        @if ($review->isExpired())
                            <strong class="prv-danger">انتهت صلاحيته</strong>، مدّده من الأعلى.
                        @else
                            صالح حتى {{ $review->expires_at?->format('Y-m-d') }}
                        @endif
                        @if ($review->opened_at) · فتحه المنجز {{ $review->opened_at->diffForHumans() }} @endif
                    </span>
                </div>
                <div class="prv-link__row">
                    <input type="text" readonly value="{{ $review->url() }}" dir="ltr" class="prv-link__input" x-ref="url" x-on:focus="$el.select()">
                    <button type="button" class="prv-btn" x-on:click="navigator.clipboard.writeText($refs.url.value); copied = true; setTimeout(() => copied = false, 2000)">
                        <span x-show="! copied">نسخ الرابط</span><span x-show="copied" x-cloak>نُسخ ✓</span>
                    </button>
                    <a class="prv-btn prv-btn--wa" target="_blank" rel="noopener" href="https://wa.me/?text={{ rawurlencode($invitation) }}">واتساب</a>
                    <a class="prv-btn prv-btn--ghost" href="mailto:?subject={{ rawurlencode(__('site.review.invite_subject')) }}&body={{ rawurlencode($invitation) }}">البريد</a>
                    <button type="button" class="prv-btn prv-btn--ghost" x-on:click="navigator.clipboard.writeText(@js($invitation)); copied = true; setTimeout(() => copied = false, 2000)">نسخ الرسالة كاملة</button>
                </div>
                <p class="prv-muted">الرابط خاص بالمنجز ولا يحتاج إلى حساب. صفحته لا تشبه تصميم الموقع، فلا تكشف شيئًا من التدشين.</p>
            </section>
        @endif

        <section class="prv-stats">
            <div class="prv-stat"><span>نقاط الصفحة</span><strong>{{ $progress['total'] }}</strong></div>
            <div class="prv-stat prv-stat--edit"><span>عدّلها</span><strong>{{ $progress['edited'] }}</strong></div>
            <div class="prv-stat prv-stat--edit"><span>أقسام غيّرها</span><strong>{{ $progress['lists'] }}</strong></div>
            <div class="prv-stat prv-stat--add"><span>صور جديدة</span><strong>{{ $progress['photos'] }}</strong></div>
            <div class="prv-stat prv-stat--add"><span>إنجازات أضافها</span><strong>{{ $progress['additions'] }}</strong></div>
        </section>

        @if ($review->status === 'returned' && filled($review->admin_note))
            <section class="prv-card prv-note prv-note--team"><h3>ملاحظتكم للمنجز</h3><p>{!! nl2br(e($review->admin_note), false) !!}</p></section>
        @endif

        @if ($nothing)
            <section class="prv-card prv-empty">
                @if (in_array($review->status, ['sent', 'opened', 'returned'], true))
                    لم يرسل المنجز مراجعته بعد. ستظهر هنا تعديلاته وإضافاته فور إرسالها.
                @else
                    أكّد المنجز صفحته كما هي، لا توجد تعديلات.
                @endif
            </section>
        @else
            @if (! $decided)
                <div class="prv-toolbar">
                    <span>حدّد ما تعتمده. ما لا يُحدَّد لا يُنشر.</span>
                    <button type="button" class="prv-textbtn" wire:click="selectAll(true)">تحديد الكل</button>
                    <button type="button" class="prv-textbtn" wire:click="selectAll(false)">إلغاء التحديد</button>
                </div>
            @endif

            {{-- Corrected points --}}
            @foreach ($sections as $title => $items)
                <section class="prv-card">
                    <h3 class="prv-sec-title">{{ $title }}</h3>
                    @foreach ($items as $item)
                        @php $key = $item['key']; @endphp
                        <article class="prv-diff {{ ($this->approvals[$key] ?? false) ? 'is-on' : '' }}" wire:key="d-{{ $key }}">
                            <label class="prv-diff__check">
                                <input type="checkbox" wire:model.live="approvals.{{ $key }}" @disabled($decided)>
                                <span>{{ $item['label'] }}</span>
                                @if ($decided) {!! $badge($item['approved'] ?? false) !!} @endif
                            </label>
                            <div class="prv-diff__cols">
                                <div class="prv-diff__old">
                                    <span class="prv-diff__tag">الحالي</span>
                                    @if ($item['type'] === 'image')
                                        @if (filled($item['old'] ?? null)) <img src="{{ $item['old'] }}" alt=""> @else <p class="prv-muted">لا توجد صورة</p> @endif
                                    @else
                                        <p>{!! nl2br(e($item['shown'] ?? ($item['old'] ?? ''))) ?: '<span class="prv-muted">فارغ</span>' !!}</p>
                                    @endif
                                </div>
                                <div class="prv-diff__new">
                                    <span class="prv-diff__tag">المقترح من المنجز</span>
                                    @if ($item['type'] === 'image')
                                        @if ($url = $this->newUrl($item['new'] ?? null))
                                            <a href="{{ $url }}" target="_blank" rel="noopener"><img src="{{ $url }}" alt=""></a>
                                        @endif
                                    @elseif (in_array($item['type'], ['select', 'scope'], true))
                                        <p>{{ $item['options'][$item['new']] ?? $item['new'] ?? '' }}</p>
                                    @else
                                        <p>{!! nl2br(e((string) ($item['new'] ?? ''))) ?: '<span class="prv-muted">فارغ (حذف النص)</span>' !!}</p>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </section>
            @endforeach

            {{-- Lists: stations, accounts, interviews, contact --}}
            @foreach ($parts as $part => $label)
                @php $value = $review->{$part}; $old = $value['old'] ?? []; $new = $value['new'] ?? []; @endphp
                <section class="prv-card">
                    <article class="prv-diff {{ ($this->partApprovals[$part] ?? false) ? 'is-on' : '' }}" wire:key="p-{{ $part }}">
                        <label class="prv-diff__check">
                            <input type="checkbox" wire:model.live="partApprovals.{{ $part }}" @disabled($decided)>
                            <span>{{ $label }}</span>
                            @if ($decided) {!! $badge($value['approved'] ?? false) !!} @endif
                        </label>
                        <div class="prv-diff__cols">
                            @foreach (['الحالي' => $old, 'المقترح من المنجز' => $new] as $tag => $list)
                                <div class="{{ $loop->first ? 'prv-diff__old' : 'prv-diff__new' }}">
                                    <span class="prv-diff__tag">{{ $tag }}</span>
                                    @switch($part)
                                        @case('journey')
                                            @forelse ($list as $station)<p><b>{{ $station['year'] ?? '' }}</b> · {{ $station['text'] ?? '' }}</p>@empty<p class="prv-muted">لا محطات</p>@endforelse
                                            @break
                                        @case('socials')
                                            @foreach ($socialLabels as $platform => $name)<p><b>{{ $name }}:</b> <span dir="ltr">{{ ($list[$platform] ?? '') ?: '—' }}</span></p>@endforeach
                                            @break
                                        @case('videos')
                                            @forelse ($list as $video)<p>{{ $video['title'] ?: 'بدون عنوان' }} · <a href="{{ $video['url'] }}" target="_blank" rel="noopener" dir="ltr">{{ $video['url'] }}</a></p>@empty<p class="prv-muted">لا مقابلات</p>@endforelse
                                            @break
                                        @default
                                            <p><b>الهاتف:</b> <span dir="ltr">{{ ($list['phone'] ?? '') ?: '—' }}</span></p>
                                            <p><b>البريد:</b> <span dir="ltr">{{ ($list['email'] ?? '') ?: '—' }}</span></p>
                                    @endswitch
                                </div>
                            @endforeach
                        </div>
                    </article>
                </section>
            @endforeach

            {{-- New photos for «المكتبة المرئية» --}}
            @if ($gallery)
                <section class="prv-card">
                    <h3 class="prv-sec-title">صور جديدة للمكتبة المرئية</h3>
                    <div class="prv-gallery">
                        @foreach ($gallery as $index => $photo)
                            @php $url = $this->newUrl($photo['path'] ?? null); @endphp
                            <label class="prv-photo {{ ($this->photoApprovals[$index] ?? false) ? 'is-on' : '' }}" wire:key="g-{{ $index }}">
                                <a href="{{ $url }}" target="_blank" rel="noopener"><img src="{{ $url }}" alt=""></a>
                                <span class="prv-photo__cap">{{ $photo['caption'] ?: 'بدون عنوان' }}</span>
                                <span class="prv-photo__check"><input type="checkbox" wire:model.live="photoApprovals.{{ $index }}" @disabled($decided)> اعتماد @if ($decided) {!! $badge($photo['approved'] ?? false) !!} @endif</span>
                            </label>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Achievements the achiever added --}}
            @if ($additions)
                <section class="prv-card">
                    <h3 class="prv-sec-title">إنجازات أضافها المنجز</h3>
                    <p class="prv-muted">تُنشر عند الاعتماد إنجازًا جديدًا باسمه. أكمل بعدها تفاصيله (المجال، الظهور) من صفحة الإنجاز.</p>
                    @foreach ($additions as $index => $addition)
                        <article class="prv-diff prv-add {{ ($this->additionApprovals[$index] ?? false) ? 'is-on' : '' }}" wire:key="a-{{ $index }}">
                            <label class="prv-diff__check">
                                <input type="checkbox" wire:model.live="additionApprovals.{{ $index }}" @disabled($decided)>
                                <span>{{ $addition['title'] ?: 'بدون عنوان' }}</span>
                                @if ($decided) {!! $badge($addition['approved'] ?? false) !!} @endif
                            </label>
                            <dl class="prv-add__facts">
                                <div><dt>التاريخ</dt><dd dir="ltr">{{ $addition['when'] ?: '—' }}</dd></div>
                                <div><dt>المستوى</dt><dd>{{ \App\Support\ProfileReviews\ProfileReviewItems::scopeOptions()[$addition['scope_id'] ?? 0] ?? '—' }}</dd></div>
                                <div><dt>التخصص</dt><dd>{{ $addition['discipline'] ?: '—' }}</dd></div>
                                <div><dt>المكان</dt><dd>{{ $addition['country'] ?: '—' }}</dd></div>
                            </dl>
                            @if (filled($addition['description'] ?? null))
                                <p class="prv-add__desc">{!! nl2br(e($addition['description'])) !!}</p>
                            @endif
                            @if ($url = $this->newUrl($addition['cover'] ?? null))
                                <a href="{{ $url }}" target="_blank" rel="noopener"><img src="{{ $url }}" alt="" class="prv-add__img"></a>
                            @endif
                        </article>
                    @endforeach
                </section>
            @endif
        @endif
    </div>
</x-filament-panels::page>
