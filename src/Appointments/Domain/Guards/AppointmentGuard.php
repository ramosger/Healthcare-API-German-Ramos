<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Services;

use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;

final class AppointmentGuard
{
    public function assertCanCreate(AppointmentDto $dto): void
    {
        if (! Doctor::query()->whereKey($dto->doctor_id)->exists()) {
            throw new DomainException('Doctor does not exist');
        }

        if (! Patient::query()->whereKey($dto->patient_id)->exists()) {
            throw new DomainException('Patient does not exist');
        }

        $doctorInClinic = Clinic::query()
            ->whereKey($dto->clinic_id)
            ->whereHas('doctors', fn (Builder $q) => $q->whereKey($dto->doctor_id))
            ->exists();

        if (! $doctorInClinic) {
            throw new DomainException('The doctor is not assigned to the selected clinic');
        }

        $overlap = static fn (Builder $q): Builder =>
            $q->where('start_date', '<', $dto->end_date)
              ->where('end_date', '>', $dto->start_date);

        if (Appointment::query()
            ->where('doctor_id', $dto->doctor_id)
            ->where('clinic_id', $dto->clinic_id)
            ->tap($overlap)->exists()) {
            throw new DomainException('The doctor already has an appointment in the selected date range');
        }

        if (Appointment::query()
            ->where('patient_id', $dto->patient_id)
            ->tap($overlap)->exists()) {
            throw new DomainException('The patient already has an appointment in the selected date range');
        }
    }
}
