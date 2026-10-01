@section('title', __('site.about.title'))

@php
    use Illuminate\Support\Facades\Storage;

    /** @var \App\Settings\AboutSettings $settings */
    $text = fn (?string $ar, ?string $en) => app()->getLocale() === 'ar' ? ($ar ?: $en) : ($en ?: $ar);

    $introParagraphs = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $text($settings->intro_text_ar, $settings->intro_text_en)))));
    $founderParagraphs = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $text($settings->founder_message_ar, $settings->founder_message_en)))));

    $founderName = $text($settings->founder_name_ar, $settings->founder_name_en);
    $founderPosition = $text($settings->founder_position_ar, $settings->founder_position_en);

    $collageImages = [
        ['path' => $settings->intro_image_one, 'class' => 'lg:absolute lg:top-[18.9rem] lg:left-0 lg:w-[32rem] lg:h-[39.9rem] rounded-[0_9.7rem_2.9rem_0]'],
        ['path' => $settings->intro_image_two, 'class' => 'lg:absolute lg:top-[21.1rem] lg:left-[38.2rem] lg:w-[46.4rem] lg:h-[36.7rem] rounded-[0_9.7rem_9.7rem_9.7rem]'],
        ['path' => $settings->intro_image_three, 'class' => 'lg:absolute lg:top-[61.7rem] lg:left-0 lg:w-[44.4rem] lg:h-[48.9rem] rounded-[0_9.7rem_2.9rem_0]'],
        ['path' => $settings->intro_image_four, 'class' => 'lg:absolute lg:top-[61.7rem] lg:left-[52.5rem] lg:w-[32.1rem] lg:h-[40.6rem] rounded-[0_9.7rem_2.9rem_0]'],
    ];
@endphp

<div>
<!-- ===== Hero (1920 x 979, rounded bottom-left) ===== -->
<header class="relative lg:h-[97.9rem] pt-[18rem] pb-[8rem] lg:p-0 rounded-bl-[25rem] max-lg:rounded-bl-[8rem] bg-[linear-gradient(180deg,#0b626a_0%,#063135_100%)]">
  <div class="relative px-[4rem] lg:p-0 flex flex-col gap-[5rem] lg:block">
    @if ($introParagraphs)
      <p class="m-0 lg:absolute lg:top-[31rem] lg:right-[14rem] lg:w-[81.2rem] text-white text-[2.65rem] leading-[5.03rem]">
        @foreach ($introParagraphs as $paragraph)
          {{ $paragraph }}@unless ($loop->last)<br>@endunless
        @endforeach
      </p>
    @endif

    <!-- Photo collage -->
    <div class="relative h-[90rem] lg:h-auto lg:static grid grid-cols-2 gap-[3rem] lg:block">
      @foreach ($collageImages as $image)
        @if ($image['path'])
          <img src="{{ Storage::url($image['path']) }}" alt="{{ __('site.about.collage_alt') }}" class="collage {{ $image['class'] }}">
        @endif
      @endforeach
    </div>
  </div>
</header>

<main class="wash-block relative overflow-hidden">
  <img src="{{ asset('images/brand/logo-white.png') }}" alt="" aria-hidden="true" class="hidden lg:block absolute left-[40rem] top-[8rem] w-[110rem] brightness-0 opacity-[0.03] pointer-events-none">

  <!-- Values / Mission / Vision -->
  <section class="relative px-[4rem] lg:px-0 mt-[17.2rem] grid grid-cols-1 md:grid-cols-3 gap-[3rem] lg:flex lg:gap-[7.6rem] lg:mr-[14rem]">
    <article class="value-card">
      <img src="{{ asset('images/about/icon-vision.svg') }}" alt="" class="h-[7rem] mt-[5.6rem]">
      <h2>{{ __('site.about.vision') }}</h2>
      <p>{{ $text($settings->vision_ar, $settings->vision_en) }}</p>
    </article>
    <article class="value-card">
      <img src="{{ asset('images/about/icon-mission.svg') }}" alt="" class="h-[10.2rem] mt-[2.4rem]">
      <h2>{{ __('site.about.mission') }}</h2>
      <p>{{ $text($settings->message_ar, $settings->message_en) }}</p>
    </article>
    <article class="value-card">
      <img src="{{ asset('images/about/icon-values.svg') }}" alt="" class="h-[10.5rem] mt-[3.2rem]">
      <h2>{{ __('site.about.values') }}</h2>
      <p>{{ $text($settings->values_ar, $settings->values_en) }}</p>
    </article>
  </section>

  <!-- National message + founder -->
  <section class="relative mx-[4rem] lg:mx-0 lg:mr-[14rem] lg:w-[164rem] mt-[17.5rem] rounded-[25rem_25rem_14rem_14rem] max-lg:rounded-[8rem] bg-[linear-gradient(180deg,#0b626a_0%,#063135_100%)] shadow-[0_0.3rem_0.6rem_rgba(0,0,0,0.16)] lg:h-[143.6rem] px-[4rem] py-[10rem] lg:p-0 text-white">
    <div class="lg:absolute lg:top-[18.5rem] lg:right-[7.5rem] lg:w-[147.9rem] text-[2.9rem] leading-[5.03rem] space-y-0">
      @foreach ($founderParagraphs as $paragraph)
        <p class="m-0">{{ $paragraph }}</p>
      @endforeach
    </div>
    @if ($settings->founder_photo)
      <img src="{{ Storage::url($settings->founder_photo) }}" alt="{{ $founderName }}" class="block mx-auto mt-[8rem] lg:m-0 lg:absolute lg:top-[77.3rem] lg:right-[15.4rem] w-[50.5rem] max-w-full">
    @endif
    <div class="mt-[4rem] lg:mt-0 lg:absolute lg:top-[105.6rem] lg:right-[87.6rem] text-center lg:text-right">
      <p class="m-0 text-[4.6rem] leading-[5.1rem] font-bold">{{ $founderName }}</p>
      <p class="m-0 mt-[1.2rem] text-[4.3rem] leading-[5.1rem] font-bold">{{ $founderPosition }}</p>
    </div>
  </section>

  <!-- Testimonials -->
  @if ($testimonials->isNotEmpty())
    <section class="relative mt-[21.2rem]" x-data='{ ...carousel(@js($testimonials->count())), items: @js($testimonials), at(o) { const n = this.items.length; return this.items[(this.index + o + n) % n]; } }'>
      <x-section-title :title="__('site.about.testimonials')" class="px-[4rem] lg:px-0 lg:mr-[18rem] !gap-[2.1rem]" />
      <div class="relative mt-[10.8rem] lg:h-[48rem] overflow-hidden">
        <div class="quote-card max-lg:hidden lg:right-[-55.5rem]" aria-hidden="true">
          <img :src="at(-1).img" alt="" class="quote-photo">
          <p class="quote-text" x-text="at(-1).text"></p><p class="quote-name" x-text="at(-1).name"></p>
        </div>
        <div class="quote-card relative lg:absolute mx-[4rem] lg:mx-0 lg:right-[50.6rem]">
          <img :src="at(0).img" alt="" class="quote-photo">
          <p class="quote-text" x-text="at(0).text"></p><p class="quote-name" x-text="at(0).name"></p>
        </div>
        <div class="quote-card max-lg:hidden lg:right-[156.7rem]" aria-hidden="true">
          <img :src="at(1).img" alt="" class="quote-photo">
          <p class="quote-text" x-text="at(1).text"></p><p class="quote-name" x-text="at(1).name"></p>
        </div>
      </div>
      <div class="mt-[9.3rem] flex justify-center gap-[4.6rem]" dir="ltr">
        <template x-for="(t, k) in items" :key="k">
          <button type="button" @click="index = items.length - 1 - k" :class="(items.length - 1 - k) === index ? 'bg-[#5f5f5f]' : 'bg-[#d5d5d5]'" class="w-[3rem] h-[3rem] rounded-full border-0 cursor-pointer p-0" :aria-label="'{{ __('site.about.testimonial_aria') }} ' + (k + 1)"></button>
        </template>
      </div>
    </section>
  @endif

  <!-- Team -->
  @if ($teamMembers->isNotEmpty())
    <section class="relative px-[4rem] lg:px-0 mt-[18.8rem]">
      <x-section-title :title="__('site.about.team')" class="lg:mr-[18rem] !gap-[2.1rem]" />
      <div class="mt-[3.5rem] grid grid-cols-2 lg:grid-cols-[repeat(4,33.3rem)] gap-x-[3rem] lg:gap-x-[9.3rem] gap-y-[3.7rem] lg:gap-y-[1.1rem] lg:mr-[13.6rem]">
        @foreach ($teamMembers as $member)
          <div class="text-center">
            <img src="{{ $member->photo_path ? Storage::url($member->photo_path) : asset('images/brand/mark.svg') }}" alt="{{ $member->t('name') }}" class="w-full lg:w-[33.3rem] block">
            <p class="mt-[3.3rem] mb-0 text-[3.3rem] leading-[4.4rem] font-bold text-[#4d4d4d]">{{ $member->t('name') }}</p>
            <p class="m-0 text-[3rem] leading-[4rem] text-[#4d4d4d]">{{ $member->t('position') }}</p>
          </div>
        @endforeach
      </div>
    </section>
  @endif

  <!-- Our achievements carousel -->
  @if ($newsItems->isNotEmpty())
    <section class="relative px-[4rem] lg:px-0 mt-[13.9rem] pb-[16.5rem]" x-data='{ ...carousel(@js($newsItems->count())), items: @js($newsItems), get visible() { return this.items.map((_, k) => this.items[(this.index + k) % this.items.length]); } }'>
      <x-section-title :title="__('site.about.achievements')" class="lg:mr-[18rem] !gap-[2.1rem]" />
      <div class="relative mt-[7.4rem]">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[repeat(4,38.1rem)] gap-[3.8rem] lg:mr-[14.1rem]">
          <template x-for="(n, k) in visible" :key="k">
            <a :href="n.url" class="news-card">
              <img :src="n.img" :alt="n.title">
              <p class="date" x-text="n.date"></p>
              <h3 x-text="n.title"></h3>
            </a>
          </template>
        </div>
        <button type="button" @click="index = (index + 1) % items.length" class="hidden lg:flex absolute right-[5.6rem] top-[18.7rem] w-[5.1rem] h-[5.1rem] rounded-full bg-[#096770] text-white items-center justify-center border-0 cursor-pointer hover:brightness-110" aria-label="{{ __('site.about.next') }}">
          <svg class="w-[2.4rem] h-[2.4rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m9.5 6 6 6-6 6"/></svg>
        </button>
        <button type="button" @click="index = (index - 1 + items.length) % items.length" class="hidden lg:flex absolute left-[5.5rem] top-[18.7rem] w-[5.1rem] h-[5.1rem] rounded-full bg-[#096770] text-white items-center justify-center border-0 cursor-pointer hover:brightness-110" aria-label="{{ __('site.about.previous') }}">
          <svg class="w-[2.4rem] h-[2.4rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m14.5 6-6 6 6 6"/></svg>
        </button>
      </div>
    </section>
  @endif

  <!-- Media library -->
  <x-media-library />
</main>
</div>
