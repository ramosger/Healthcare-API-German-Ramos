<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Appointments\Domain\Guards\AppointmentGuard;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Patients\Domain\Models\Patient;

class StoreAppointmentAction
{
    public function __construct(private readonly AppointmentGuard $guard)
    {
    }

    public function execute(AppointmentDto $appointmentDto): Appointment
    {
        $this->guard->assertCanCreate($appointmentDto);

        $appointment = new Appointment();

        $appointment->doctor_id = $appointmentDto->doctor_id;
        $appointment->patient_id = $appointmentDto->patient_id;
        $appointment->clinic_id = $appointmentDto->clinic_id;
        $appointment->start_date = $appointmentDto->start_date->toDateTimeString();
        $appointment->end_date = $appointmentDto->end_date->toDateTimeString();

        $patient = Patient::query()->findOrFail($appointmentDto->patient_id);
        $appointment->user()->associate($patient->user);

        $appointment->saveOrFail();

        return $appointment->refresh()->load(['doctor', 'patient', 'user', 'clinic']);
    }
}
