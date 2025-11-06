<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Database\Factories\UserFactory;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;
use function Pest\Laravel\withoutMiddleware;

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

test('fails with 422 when doctor already has an appointment in the selected date range', function (): void {
    $user = actingAsApi();
    $anotherUser = UserFactory::new()->createOne();

    ClinicFactory::new()->count(5)->create();

    /** @var Doctor */
    $doctor = DoctorFactory::new()->withRandomClinics(1, 2)->createOne();
    /** @var Patient */
    $p1 = PatientFactory::new()->createOne(['user_id' => $user->id]);
    /** @var Patient */
    $p2 = PatientFactory::new()->createOne(['user_id' => $anotherUser->id]);

    $clinicId = $doctor->clinics()->firstOrFail()->getKey();

    $start = CarbonImmutable::now()->addHour()->seconds(0);
    $end = $start->addMinutes(60);

    postJson('/api/appointments', [
        'doctor_id'  => $doctor->id,
        'patient_id' => $p1->id,
        'clinic_id'  => $clinicId,
        'start_date' => $start->toDateTimeString(),
        'end_date'   => $end->toDateTimeString(),
    ])->assertCreated();

    $response = postJson('/api/appointments', [
        'doctor_id'  => $doctor->id,
        'patient_id' => $p2->id,
        'clinic_id'  => $clinicId,
        'start_date' => $start->addMinutes(30)->toDateTimeString(),
        'end_date'   => $end->addMinutes(30)->toDateTimeString(),
    ])->assertUnprocessable();

    $response->assertJsonPath('error.code', 'validation_failed');
    $response->assertJsonStructure(['error' => ['message', 'fields' => ['start_date']]]);
    expect($response->json('error.fields.start_date'))
        ->toBeArray()
        ->toContain('The Doctor already has an appointment in the selected date range');
});

test('fails with 422 when patient already has an appointment in the selected date range', function (): void {
    $user = actingAsApi();

    ClinicFactory::new()->count(5)->create();

    /** @var Doctor */
    $doctorA = DoctorFactory::new()->withRandomClinics(1, 2)->createOne();
    /** @var Doctor */
    $doctorB = DoctorFactory::new()->withRandomClinics(1, 2)->createOne();
    /** @var Patient */
    $patient = PatientFactory::new()->createOne(['user_id' => $user->id]);

    $clinicA = $doctorA->clinics()->firstOrFail()->getKey();
    $clinicB = $doctorB->clinics()->firstOrFail()->getKey();

    $start = CarbonImmutable::now()->addHour()->seconds(0);
    $end = $start->addMinutes(60);

    postJson('/api/appointments', [
        'doctor_id'  => $doctorA->id,
        'patient_id' => $patient->id,
        'clinic_id'  => $clinicA,
        'start_date' => $start->toDateTimeString(),
        'end_date'   => $end->toDateTimeString(),
    ])->assertCreated();

    $response = postJson('/api/appointments', [
        'doctor_id'  => $doctorB->id,
        'patient_id' => $patient->id,
        'clinic_id'  => $clinicB,
        'start_date' => $start->addMinutes(15)->toDateTimeString(),
        'end_date'   => $end->addMinutes(15)->toDateTimeString(),
    ])->assertUnprocessable();

    $response->assertJsonPath('error.code', 'validation_failed');
    $response->assertJsonStructure(['error' => ['message', 'fields' => ['start_date']]]);
    expect($response->json('error.fields.start_date'))
        ->toBeArray()
        ->toContain('The Patient already has an appointment in the selected date range');
});

test('returns 401 when no authenticated user (authenticatedUserId)', function (): void {
    withoutMiddleware([Authenticate::class]);

    $resp = postJson('/api/appointments', []);

    $resp->assertUnauthorized();
});
