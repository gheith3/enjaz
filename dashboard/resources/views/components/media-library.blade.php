@php
    use App\Models\ImageMedia;
    use App\Models\PublicationMedia;
    use App\Models\VideoMedia;
    use App\Settings\AboutSettings;
    use Illuminate\Support\Facades\Storage;

    $aboutSettings = app(AboutSettings::class);
    $publications = PublicationMedia::query()->published()->active()->ordered()->take(2)->get();
    $firstPublication = $publications->get(0);
    $secondPublication = $publications->get(1);
    $latestVideo = VideoMedia::query()->published()->active()->ordered()->first();
    $latestAlbum = ImageMedia::query()->published()->active()->ordered()->first();
    $albumCover = $latestAlbum?->images[0] ?? null;
    $profileDocumentUrl = $aboutSettings->profile_document ? Storage::url($aboutSettings->profile_document) : null;
@endphp

<!-- ===== Media library ===== -->
<section class="bg-[#f7f7f7] lg:h-[124.5rem] px-[4rem] lg:px-0 pt-[15.6rem] pb-[8rem] lg:pb-0">
  <x-section-title :title="__('site.media.library.title')" class="lg:mr-[18rem] !gap-[2.1rem]" />
  <div class="mt-[5.8rem] grid grid-cols-1 sm:grid-cols-2 gap-[3rem] lg:grid-cols-[57.1rem_52.4rem_46.2rem] lg:grid-rows-[34.5rem_34.5rem] lg:gap-x-[4.1rem] lg:gap-y-[3.6rem] lg:mr-[14rem]">
    @if ($firstPublication)
      <a href="{{ Storage::url($firstPublication->file_path) }}" download class="lib-tile">
        @if ($firstPublication->cover_image_path)
          <img src="{{ Storage::url($firstPublication->cover_image_path) }}" alt="{{ $firstPublication->t('title') }}">
        @endif
        <span class="lib-label"><span class="media-btn !static"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><path stroke-linejoin="round" d="M6 3.5h11a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6.5A1.5 1.5 0 0 1 5 19V5a1.5 1.5 0 0 1 1-1.5Z"/><path d="M5 18.5A1.5 1.5 0 0 1 6.5 17H18"/></svg></span>{{ $firstPublication->t('title') }}</span>
      </a>
    @endif
    <a href="{{ route('media.videos') }}" class="lib-tile">
      @if ($latestVideo?->cover_image_path)
        <img src="{{ Storage::url($latestVideo->cover_image_path) }}" alt="{{ __('site.media.library.videos') }}">
      @endif
      <span class="lib-label"><span class="media-btn !static"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path fill="#fff" stroke="none" d="M10 9.2v5.6l4.6-2.8L10 9.2Z"/></svg></span>{{ __('site.media.library.videos') }}</span>
    </a>
    @if ($profileDocumentUrl)
      <a href="{{ $profileDocumentUrl }}" download class="lib-tile lib-profile lg:row-span-2"><svg class="w-[6.4rem] h-[8.2rem]" viewBox="0 0 64 82" fill="none" stroke="#fff" stroke-width="2.6" stroke-linejoin="round"><path d="M10 2h34l18 18v58a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z"/><path d="M44 2v18h18"/><rect x="16" y="14" width="16" height="20" rx="1.5"/><circle cx="24" cy="21" r="3.2"/><path d="M19 31c1-4 9-4 10 0"/><path stroke-linecap="round" d="M16 44h32M16 52h32M16 60h32M16 68h22M38 24h10"/></svg><span>{{ __('site.media.library.profile') }}</span></a>
    @endif
    @if ($secondPublication)
      <a href="{{ Storage::url($secondPublication->file_path) }}" download class="lib-tile">
        @if ($secondPublication->cover_image_path)
          <img src="{{ Storage::url($secondPublication->cover_image_path) }}" alt="{{ $secondPublication->t('title') }}">
        @endif
        <span class="lib-label"><span class="media-btn !static"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><path stroke-linejoin="round" d="M6 3.5h11a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6.5A1.5 1.5 0 0 1 5 19V5a1.5 1.5 0 0 1 1-1.5Z"/><path d="M5 18.5A1.5 1.5 0 0 1 6.5 17H18"/></svg></span>{{ $secondPublication->t('title') }}</span>
      </a>
    @endif
    <a href="{{ route('media.images-albums') }}" class="lib-tile">
      @if ($albumCover)
        <img src="{{ Storage::url($albumCover) }}" alt="{{ __('site.media.library.photos') }}">
      @endif
      <span class="lib-label"><span class="media-btn !static"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="10" r="1.6"/><path stroke-linejoin="round" d="m4 18 5.5-5.5 3.5 3.5 2.5-2.5L20 18"/></svg></span>{{ __('site.media.library.photos') }}</span>
    </a>
  </div>
</section>
