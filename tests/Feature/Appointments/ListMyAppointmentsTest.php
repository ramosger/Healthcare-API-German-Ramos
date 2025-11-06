<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Database\Factories\AppointmentFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lightit\Appointments\Domain\Models\Appointment;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Patients\Domain\Models\Patient;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('lists only the appointments of the logged user', function (): void {
    $user = actingAsApi();
    $anotherUser = UserFactory::new()->createOne();

    ClinicFactory::new()->count(10)->create();

    /** @var Doctor */
    $doctorA = DoctorFactory::new()->withRandomClinics(1, 3)->createOne();
    /** @var Doctor */
    $doctorB = DoctorFactory::new()->withRandomClinics(1, 3)->createOne();

    /** @var Patient */
    $patientAssignedToUser = PatientFactory::new()->createOne(['user_id' => $user->id]);
    /** @var Patient */
    $patientAssignedToAnotherUser = PatientFactory::new()->createOne(['user_id' => $anotherUser->id]);

    $clinicA = $doctorA->clinics()->firstOrFail()->getKey();
    $clinicB = $doctorB->clinics()->firstOrFail()->getKey();

    $start = CarbonImmutable::now()->addHour()->seconds(0);
    $end = $start->addMinutes(60);

    /** @var Appointment */
    $a1 = AppointmentFactory::new()->create([
        'user_id' => $user->id,
        'doctor_id' => $doctorA->id,
        'patient_id' => $patientAssignedToUser->id,
        'clinic_id' => $clinicA,
        'start_date' => $start->toDateTimeString(),
        'end_date' => $end->toDateTimeString(),
    ]);

    /** @var Appointment */
    $a2 = AppointmentFactory::new()->create([
        'user_id' => $user->id,
        'doctor_id' => $doctorB->id,
        'patient_id' => $patientAssignedToUser->id,
        'clinic_id' => $clinicB,
        'start_date' => $start->addMinutes(90)->toDateTimeString(),
        'end_date' => $end->addMinutes(90)->toDateTimeString(),
    ]);

    AppointmentFactory::new()->create([
        'user_id' => $anotherUser->id,
        'doctor_id' => $doctorA->id,
        'patient_id' => $patientAssignedToAnotherUser->id,
        'clinic_id' => $clinicA,
        'start_date' => $start->addMinutes(180)->toDateTimeString(),
        'end_date' => $end->addMinutes(180)->toDateTimeString(),
    ]);

    $response = getJson('/api/appointments/me/appointments')->assertOk();
    $response->assertJsonCount(2, 'data');
    $response->assertJsonFragment(['id' => $a1->id]);
    $response->assertJsonFragment(['id' => $a2->id]);
});

test('returns empty list when the logged user has no appointments', function (): void {
    actingAsApi();

    getJson('/api/appointments/me/appointments')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});
