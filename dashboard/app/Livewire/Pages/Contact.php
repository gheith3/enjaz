<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Enums\ContactMessageStatus;
use App\Models\ContactUsMessage;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.site')]
class Contact extends Component
{
    public string $name = '';

    public string $email = '';

    public string $message = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'name.required' => __('site.contact.validation.name_required'),
            'name.max' => __('site.contact.validation.name_max'),
            'email.required' => __('site.contact.validation.email_required'),
            'email.email' => __('site.contact.validation.email_invalid'),
            'email.max' => __('site.contact.validation.email_max'),
            'message.required' => __('site.contact.validation.message_required'),
            'message.max' => __('site.contact.validation.message_max'),
        ]);

        $throttleKey = 'contact-form:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->addError('message', __('site.contact.rate_limited'));

            return;
        }

        RateLimiter::hit($throttleKey, 60);

        ContactUsMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
            'ip_address' => request()->ip(),
            'status' => ContactMessageStatus::New,
        ]);

        $this->reset(['name', 'email', 'message']);

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.pages.contact', [
            'settings' => app(GeneralSettings::class),
        ]);
    }
}
