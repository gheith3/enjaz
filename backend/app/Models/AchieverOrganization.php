<?php

namespace App\Models;

use App\Support\Localized;
use Illuminate\Database\Eloquent\Relations\Pivot;

/** Affiliation of an achiever with an organization: kind (staff | alum | student | member) and role. */
class AchieverOrganization extends Pivot
{
    public const KINDS = ['staff' => 'من منسوبيها', 'alum' => 'من خريجيها', 'student' => 'من طلبتها', 'member' => 'من أعضائها'];

    protected $table = 'achiever_organization';

    public $incrementing = true;

    protected function casts(): array
    {
        return ['role' => 'array'];
    }

    public function roleLabel(): string
    {
        return Localized::get($this->role);
    }
}
