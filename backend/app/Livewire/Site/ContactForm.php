<?php

namespace App\Livewire\Site;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\ContactTopic;
use App\Models\Page;
use App\Settings\GeneralSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * أرسل رسالة (contact.html): the conversation-style form. Messages are saved for the dashboard inbox and emailed to the
 * contact address (page text «email», else the site email). Honeypot field + 5 messages per 10 minutes per visitor.
 */
class ContactForm extends Component
{
    use WithFileUploads;

    public ?int $topic = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $message = '';

    public $attachment = null;

    /** Honeypot: hidden from people, bots fill it. */
    public string $website = '';

    public bool $sent = false;

    public function mount(): void
    {
        $this->topic = $this->topics->first()?->id;
    }

    /** @return Collection<int, ContactTopic> */
    #[Computed]
    public function topics(): Collection
    {
        return ContactTopic::query()->active()->get();
    }

    protected function rules(): array
    {
        return [
            'topic' => ['nullable', 'integer', 'exists:contact_topics,id'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:200'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[+0-9\s()-]*$/'],
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx'],
        ];
    }

    protected function validationAttributes(): array
    {
        return collect(['name', 'email', 'phone', 'message', 'attachment'])->mapWithKeys(fn ($f) => [$f => __('site.contact.'.$f)])->all();
    }

    public function send(): void
    {
        if ($this->website !== '') {
            $this->sent = true;   // pretend success for bots

            return;
        }
        $key = 'contact:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('message', __('site.contact.too_many'));

            return;
        }
        $data = $this->validate();
        RateLimiter::hit($key, 600);

        $topic = $this->topics->firstWhere('id', $data['topic']);
        $contact = ContactMessage::query()->create([
            'contact_topic_id' => $topic?->id, 'topic' => $topic?->name, 'name' => $data['name'], 'email' => $data['email'],
            'phone' => $data['phone'] ?: null, 'message' => $data['message'], 'ip' => request()->ip(),
        ]);
        if ($this->attachment) {
            $contact->update([
                'attachment_path' => $this->attachment->store('contact/'.$contact->id, ContactMessage::DISK),
                'attachment_name' => mb_substr($this->attachment->getClientOriginalName(), 0, 200),
            ]);
        }

        $to = Page::forKey('contact')->label('email') ?: app(GeneralSettings::class)->email;
        if ($to) {
            try {
                Mail::to($to)->send(new ContactMessageReceived($contact));
            } catch (\Throwable $e) {
                report($e);   // the message is saved in the inbox anyway
            }
        }

        $this->reset(['name', 'email', 'phone', 'message', 'attachment']);
        $this->sent = true;
    }

    public function again(): void
    {
        $this->sent = false;
    }

    public function render(): View
    {
        return view('livewire.site.contact-form');
    }
}
