<?php

declare(strict_types=1);

namespace Lightit\Doctors\Domain\Models;

use Database\Factories\DoctorFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Lightit\Clinics\Domain\Models\Clinic;

class Doctor extends Model
{
    /**
     * @return DoctorFactory
     */
    protected static function newFactory(): Factory
    {
        return DoctorFactory::new();
    }

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
