{{-- Design contact.html .contact-form, sent with Livewire (no mail app). --}}
<form class="contact-form" data-contact-form novalidate wire:submit="send">
  @if ($sent)
    <p class="contact-status is-sent" role="status" aria-live="polite">{{ __('site.contact.sent') }}</p>
    <button class="contact-submit" type="button" wire:click="again"><span>{{ __('site.contact.send_another') }}</span></button>
  @else
  <div class="cv-step">
    <p class="cv-q">{{ __('site.contact.q_topic') }}</p>
    <fieldset class="ct-topics"><legend class="sr-only">{{ __('site.contact.topic') }}</legend>
      @foreach ($this->topics as $t)<label><input type="radio" name="topic" value="{{ $t->id }}" wire:model="topic"><span>{{ $t->name }}</span></label>@endforeach
    </fieldset>
  </div>
  <div class="cv-step">
    <p class="cv-q">{{ __('site.contact.q_name') }}</p>
    <div class="contact-field"><label for="contact-name">{{ __('site.contact.name') }} <span aria-hidden="true">*</span></label><input id="contact-name" name="name" type="text" autocomplete="name" placeholder="{{ __('site.contact.name_ph') }}" required maxlength="120" wire:model="name" @error('name') aria-invalid="true" @enderror></div>
  </div>
  <div class="cv-step">
    <p class="cv-q">{{ __('site.contact.q_reach') }}</p>
    <div class="contact-fields">
      <div class="contact-field"><label for="contact-email">{{ __('site.contact.email') }} <span aria-hidden="true">*</span></label><input id="contact-email" name="email" type="email" autocomplete="email" placeholder="name@example.com" dir="ltr" required maxlength="200" wire:model="email" @error('email') aria-invalid="true" @enderror></div>
      <div class="contact-field"><label for="contact-person-phone">{{ __('site.contact.phone_optional') }}</label><input id="contact-person-phone" name="phone" type="tel" autocomplete="tel" inputmode="tel" placeholder="+968 9XXX XXXX" dir="ltr" maxlength="30" wire:model="phone" @error('phone') aria-invalid="true" @enderror></div>
    </div>
  </div>
  <div class="cv-step">
    <p class="cv-q">{{ __('site.contact.q_message') }}</p>
    <div class="contact-field"><label for="contact-message">{{ __('site.contact.message') }} <span aria-hidden="true">*</span></label><textarea id="contact-message" name="message" placeholder="{{ __('site.contact.message_ph') }}" required rows="4" maxlength="5000" wire:model="message" @error('message') aria-invalid="true" @enderror></textarea></div>
  </div>
  <div class="cv-step contact-attachment">
    <p class="cv-q">{{ __('site.contact.q_file') }}</p>
    <div><label class="contact-attach-label" for="contact-file"><span aria-hidden="true">📎</span> {{ __('site.contact.choose_file') }} <small>{{ __('site.contact.optional') }}</small></label><input class="contact-file-input" id="contact-file" name="attachment" type="file" aria-describedby="contact-file-hint" wire:model="attachment" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx"><span class="contact-file-name" data-file-name aria-live="polite">{{ $attachment ? $attachment->getClientOriginalName() : __('site.contact.no_file') }}</span><p class="contact-file-hint" id="contact-file-hint">{{ __('site.contact.file_hint') }}</p></div>
  </div>
  <div class="sr-only" aria-hidden="true"><label for="contact-website">Website</label><input id="contact-website" type="text" tabindex="-1" autocomplete="off" wire:model="website"></div>
  <button class="contact-submit" type="submit" wire:loading.attr="disabled"><span>{{ __('site.contact.send') }}</span><i class="cv-send"><svg aria-hidden="true"><use href="#contact-arrow"/></svg></i></button>
  <p class="contact-form-hint">{{ __('site.contact.form_hint') }}</p>
  <p class="contact-status" role="status" aria-live="polite">@if ($errors->any()){{ $errors->first() }}@endif</p>
  @endif
</form>
