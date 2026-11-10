<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Draft / scheduled / live. The public site only shows published records whose publish time has passed.
 */
trait HasPublishing
{
    public function initializeHasPublishing(): void
    {
        $this->mergeCasts(['is_published' => 'boolean', 'published_at' => 'datetime']);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where($this->qualifyColumn('is_published'), true)
            ->where(fn (Builder $q) => $q->whereNull($this->qualifyColumn('published_at'))
                ->orWhere($this->qualifyColumn('published_at'), '<=', now()));
    }

    public function isLive(): bool
    {
        return $this->is_published && ($this->published_at === null || $this->published_at->isPast());
    }
}
