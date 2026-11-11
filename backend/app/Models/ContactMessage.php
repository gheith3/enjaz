<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A message sent with the contact form. The attachment is on the private disk (downloaded from the dashboard). */
class ContactMessage extends Model
{
    public const DISK = 'local';

    protected $fillable = ['contact_topic_id', 'topic', 'name', 'email', 'phone', 'message', 'attachment_path', 'attachment_name', 'ip', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function contactTopic(): BelongsTo
    {
        return $this->belongsTo(ContactTopic::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
