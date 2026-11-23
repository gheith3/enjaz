<x-filament-panels::page>
    @php
        $review = $this->record;
        $progress = $review->progress();
        $sections = $this->editedSections();
        $additions = $review->additions ?? [];
        $editable = $review->isEditable();
        $decided = $review->status === 'approved';
        $invitation = $review->invitation();
    @endphp

    <div class="prv">
        {{-- The link to send --}}
        @if (in_array($review->status, ['sent', 'opened', 'returned'], true))
            <section class="prv-card prv-link" x-data="{ copied: false }">
                <div class="prv-link__head">
                    <h2>رابط المراجعة</h2>
                    <span class="prv-muted">
                        @if ($review->isExpired())
                            <strong class="prv-danger">انتهت صلاحيته</strong> — مدّده من الأعلى.
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

        {{-- Summary --}}
        <section class="prv-stats">
            <div class="prv-stat"><span>النقاط</span><strong>{{ $progress['total'] }}</strong></div>
            <div class="prv-stat"><span>راجعها المنجز</span><strong>{{ $progress['answered'] }}</strong></div>
            <div class="prv-stat prv-stat--ok"><span>أكّدها صحيحة</span><strong>{{ $progress['answered'] - $progress['edited'] }}</strong></div>
            <div class="prv-stat prv-stat--edit"><span>عدّلها</span><strong>{{ $progress['edited'] }}</strong></div>
            <div class="prv-stat prv-stat--add"><span>إنجازات أضافها</span><strong>{{ count($additions) }}</strong></div>
        </section>

        @if (filled($review->note))
            <section class="prv-card prv-note">
                <h3>ملاحظات المنجز</h3>
                <p>{!! nl2br(e($review->note), false) !!}</p>
            </section>
        @endif

        @if ($review->status === 'returned' && filled($review->admin_note))
            <section class="prv-card prv-note prv-note--team">
                <h3>ملاحظتكم للمنجز</h3>
                <p>{!! nl2br(e($review->admin_note), false) !!}</p>
            </section>
        @endif

        @if ($sections->isEmpty() && ! $additions)
            <section class="prv-card prv-empty">
                @if (in_array($review->status, ['sent', 'opened', 'returned'], true))
                    لم يرسل المنجز مراجعته بعد. ستظهر هنا تعديلاته وإضافاته فور إرسالها.
                @else
                    أكّد المنجز كل النقاط كما هي، لا توجد تعديلات.
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

            @foreach ($sections as $title => $items)
                <section class="prv-card">
                    <h3 class="prv-sec-title">{{ $title }}</h3>
                    @foreach ($items as $item)
                        @php $key = $item['key']; @endphp
                        <article class="prv-diff {{ ($this->approvals[$key] ?? false) ? 'is-on' : '' }}" wire:key="d-{{ $key }}">
                            <label class="prv-diff__check">
                                <input type="checkbox" wire:model.live="approvals.{{ $key }}" @disabled($decided)>
                                <span>{{ $item['label'] }}</span>
                                @if ($decided)
                                    <em class="{{ ($item['approved'] ?? false) ? 'prv-ok' : 'prv-danger' }}">{{ ($item['approved'] ?? false) ? 'اعتُمد' : 'لم يُعتمد' }}</em>
                                @endif
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
                                            <a href="{{ $url }}" target="_blank" rel="noopener" class="prv-small">فتح الصورة بالحجم الكامل</a>
                                        @endif
                                    @elseif ($item['type'] === 'select')
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

            @if ($additions)
                <section class="prv-card">
                    <h3 class="prv-sec-title">إنجازات أضافها المنجز</h3>
                    <p class="prv-muted">تُنشر عند الاعتماد إنجازًا جديدًا باسمه. أكمل بعدها تفاصيله (الصورة، المجال، الظهور) من صفحة الإنجاز.</p>
                    @foreach ($additions as $index => $addition)
                        <article class="prv-diff prv-add {{ ($this->additionApprovals[$index] ?? false) ? 'is-on' : '' }}" wire:key="a-{{ $index }}">
                            <label class="prv-diff__check">
                                <input type="checkbox" wire:model.live="additionApprovals.{{ $index }}" @disabled($decided)>
                                <span>{{ $addition['title'] ?: 'بدون عنوان' }}</span>
                                @if ($decided)
                                    <em class="{{ ($addition['approved'] ?? false) ? 'prv-ok' : 'prv-danger' }}">{{ ($addition['approved'] ?? false) ? 'نُشر' : 'لم يُنشر' }}</em>
                                @endif
                            </label>
                            <dl class="prv-add__facts">
                                <div><dt>التاريخ</dt><dd dir="ltr">{{ $addition['when'] ?: '—' }}</dd></div>
                                <div><dt>المستوى</dt><dd>{{ \App\Support\ProfileReviews\ProfileReviewItems::scopeOptions()[$addition['scope_id'] ?? 0] ?? '—' }}</dd></div>
                                <div><dt>التخصص</dt><dd>{{ $addition['discipline'] ?: '—' }}</dd></div>
                                <div><dt>الجهة</dt><dd>{{ $addition['issuer'] ?: '—' }}</dd></div>
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
