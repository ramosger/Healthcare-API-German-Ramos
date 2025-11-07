<?php

declare(strict_types=1);

namespace Lightit\Appointments\Domain\Actions;

use Illuminate\Support\Facades\Log;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Appointments\Domain\Guards\AppointmentGuard;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Patients\Domain\Models\Patient;
use Lightit\Shared\App\Notifications\AppointmentCreated;

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

        $patient = Patient::query()->with('user')->findOrFail($appointmentDto->patient_id);

        $user = $patient->user;

        if ($user !== null) {
            $appointment->user()->associate($user);

            $appointment->saveOrFail();

            $notification = new AppointmentCreated($appointment);
            $notification->afterCommit();

            $user->notify($notification);

            Log::info('Appointment Confirmation sent', [
                'to_user_id' => $user->id,
                'appointment_id' => $appointment->id,
            ]);
        }

        return $appointment->refresh()->load(['doctor', 'patient', 'user', 'clinic']);
    }
}
