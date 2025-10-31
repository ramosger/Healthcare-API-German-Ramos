<?php

declare(strict_types=1);

namespace Lightit\Patients\Domain\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lightit\Appointments\Domain\Models\Appointment;

class Patient extends Model
{
    /**
     * @return PatientFactory
     */
    protected static function newFactory(): Factory
    {
        return PatientFactory::new();
    }

    /**
     * @return hasMany<Appointment, Patient>
     */
    public function appointments(): HasMany
    {
        /** @var hasMany<Appointment, Patient> $relation */
        $relation = $this->hasMany(Appointment::class);

        return $relation;
    }
}
