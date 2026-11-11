<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent to the site's address when someone uses the contact form (reply goes to the sender). */
class ContactMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contact) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('site.contact.mail_subject', ['topic' => $this->contact->topic, 'name' => $this->contact->name]),
            replyTo: [new Address($this->contact->email, $this->contact->name)],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.contact-message');
    }

    /** @return list<Attachment> */
    public function attachments(): array
    {
        return $this->contact->attachment_path
            ? [Attachment::fromStorageDisk(ContactMessage::DISK, $this->contact->attachment_path)->as((string) $this->contact->attachment_name)]
            : [];
    }
}
