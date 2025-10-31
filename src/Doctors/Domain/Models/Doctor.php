<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Lightit\Clinics\Domain\Models\Clinic;

class Doctor extends Model
{
    /**
     * @return BelongsToMany<Clinic, Doctor, Pivot, 'pivot'>
     */
    public function clinics(): BelongsToMany
    {
        /** @var BelongsToMany<Clinic, Doctor, Pivot, 'pivot'> $relation */
        $relation = $this->belongsToMany(Clinic::class);

        return $relation;
    }
}
