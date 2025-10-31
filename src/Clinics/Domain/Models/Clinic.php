<?php

declare(strict_types=1);

namespace Lightit\Clinics\Domain\Models;

use Database\Factories\ClinicFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Lightit\Doctors\Domain\Models\Doctor;

class Clinic extends Model
{
    /**
     * @return ClinicFactory
     */
    protected static function newFactory(): Factory
    {
        return ClinicFactory::new();
    }

    /**
     * @return BelongsToMany<Doctor, Clinic, Pivot, 'pivot'>
     */
    public function doctors(): BelongsToMany
    {
        /** @var BelongsToMany<Doctor, Clinic, Pivot, 'pivot'> $relation */
        $relation = $this->belongsToMany(Doctor::class);

        return $relation;
    }
}
