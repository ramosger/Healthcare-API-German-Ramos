<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Models;

use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lightit\Patients\Domain\Models\Patient;

class Appointment extends Model
{
    /**
     * @return AppointmentFactory
     */
    protected static function newFactory(): Factory
    {
        return AppointmentFactory::new();
    }

    /**
     * @return belongsTo<Patient, Appointment>
     */
    public function patient(): BelongsTo
    {
        /** @var belongsTo<Patient, Appointment> $relation */
        $relation = $this->belongsTo(Patient::class);

        return $relation;
    }
}
