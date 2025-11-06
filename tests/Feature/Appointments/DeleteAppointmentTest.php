<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Database\Factories\AppointmentFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

test('soft deletes an existing appointment', function (): void {
    $user = actingAsApi();

    ClinicFactory::new()->count(10)->create();

    /** @var Doctor */
    $doctor = DoctorFactory::new()->withRandomClinics(1, 3)->createOne();

    /** @var Patient */
    $patient = PatientFactory::new()->createOne(['user_id' => $user->id]);

    $clinicId = $doctor->clinics()->firstOrFail()->getKey();

    $start = CarbonImmutable::now()->addHour()->seconds(0);
    $end = $start->addMinutes(60);

    /** @var Appointment */
    $appointment = AppointmentFactory::new()->create([
        'doctor_id' => $doctor->id,
        'patient_id' => $patient->id,
        'clinic_id' => $clinicId,
        'start_date' => $start->toISOString(),
        'end_date' => $end->toISOString(),
    ]);

    deleteJson("/api/appointments/{$appointment->id}")
        ->assertNoContent();

    assertSoftDeleted('appointments', ['id' => $appointment->id]);
});

test('fails with 404 when deleting a non existing appointment', function (): void {
    actingAsApi();

    deleteJson('/api/appointments/999999')->assertNotFound();
});
