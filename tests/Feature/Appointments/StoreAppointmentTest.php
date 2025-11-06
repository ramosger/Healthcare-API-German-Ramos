<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

test('creates an appointment with all valid fields', function (): void {
    $user = actingAsApi();

    ClinicFactory::new()->count(10)->create();

    /** @var Doctor */
    $doctor = DoctorFactory::new()->withRandomClinics(1, 3)->createOne();

    /** @var Patient */
    $patient = PatientFactory::new()->createOne(['user_id' => $user->id]);

    $clinicId = $doctor->clinics()->firstOrFail()->getKey();

    $start = CarbonImmutable::now()->addHour()->seconds(0);
    $end = $start->addMinutes(60);

    $payload = [
        'doctor_id'  => $doctor->id,
        'patient_id' => $patient->id,
        'clinic_id'  => $clinicId,
        'start_date' => $start->toISOString(),
        'end_date'   => $end->toISOString(),
    ];

    $response = postJson('/api/appointments', $payload);

    $response->assertJsonFragment([
        'doctor_id'  => $doctor->id,
        'patient_id' => $patient->id,
        'clinic_id'  => $clinicId,
    ]);

    assertDatabaseHas('appointments', [
        'doctor_id'  => $doctor->id,
        'patient_id' => $patient->id,
        'clinic_id'  => $clinicId,
    ]);
});

test('fails with 422 when patient is not associated with logged user', function (): void {
    actingAsApi();

    ClinicFactory::new()->count(10)->create();

    /** @var Doctor */
    $doctor = DoctorFactory::new()->withRandomClinics(1, 3)->createOne();

    /** @var Patient */
    $patient = PatientFactory::new()->createOne();

    $clinicId = $doctor->clinics()->firstOrFail()->getKey();

    $start = CarbonImmutable::now()->addHour()->seconds(0);
    $end = $start->addMinutes(60);

    $payload = [
        'doctor_id'  => $doctor->id,
        'patient_id' => $patient->id,
        'clinic_id'  => $clinicId,
        'start_date' => $start->toISOString(),
        'end_date'   => $end->toISOString(),
    ];

    postJson('/api/appointments', $payload)->assertUnprocessable();
});

test('fails with 422 when required fields are missing', function (): void {
    actingAsApi();

    postJson('/api/appointments', [])->assertUnprocessable();
});

test('fails with 422 when doctor does not belong to given clinic', function (): void {
    actingAsApi();

    ClinicFactory::new()->count(10)->create();

    /** @var Doctor */
    $doctor = DoctorFactory::new()->withRandomClinics(1, 3)->createOne();

    /** @var Patient */
    $patient = PatientFactory::new()->createOne();

    /** @var Clinic */
    $unlinkedClinic = ClinicFactory::new()->createOne();

    $payload = [
        'doctor_id'  => $doctor->id,
        'patient_id' => $patient->id,
        'clinic_id'  => $unlinkedClinic->id,
        'start_date' => CarbonImmutable::now()->addHour()->toISOString(),
        'end_date'   => CarbonImmutable::now()->addHours(2)->toISOString(),
    ];

    postJson('/api/appointments', $payload)->assertUnprocessable();
});
