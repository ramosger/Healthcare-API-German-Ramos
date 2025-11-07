<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Database\Factories\AppointmentFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

describe('DELETE /api/appointments/{id}', function (): void {
    it('soft deletes an existing appointment', function (): void {
        $user = actingAsApi();

        ClinicFactory::new()->count(10)->create();

        $doctor = DoctorFactory::new()->withRandomClinics(1, 3)->createOne();
        $patient = PatientFactory::new()->createOne(['user_id' => $user->id]);

        $rawClinicId = $doctor->clinics()->firstOrFail()->getKey();
        assert(is_int($rawClinicId));
        $clinicId = $rawClinicId;

        $start = CarbonImmutable::now()->addHour()->seconds(0);
        $end = $start->addMinutes(60);

        $appointment = AppointmentFactory::new()->createOne([
            'doctor_id'  => $doctor->id,
            'patient_id' => $patient->id,
            'clinic_id'  => $clinicId,
            'start_date' => $start->toISOString(),
            'end_date'   => $end->toISOString(),
        ]);

        deleteJson("/api/appointments/{$appointment->id}")
            ->assertNoContent();

        assertSoftDeleted('appointments', ['id' => $appointment->id]);
    });

    it('fails with 404 when deleting a non existing appointment', function (): void {
        actingAsApi();

        deleteJson('/api/appointments/999999')
            ->assertNotFound();
    });
});
