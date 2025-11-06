<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Database\Factories\AppointmentFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

describe('GET /api/appointments/me/appointments', function (): void {
    it('lists only the appointments of the logged user', function (): void {
        $user = actingAsApi();
        $anotherUser = UserFactory::new()->createOne();

        ClinicFactory::new()->count(10)->create();

        $doctorA = DoctorFactory::new()->withRandomClinics(1, 3)->createOne();
        $doctorB = DoctorFactory::new()->withRandomClinics(1, 3)->createOne();

        $patientAssignedToUser = PatientFactory::new()->createOne(['user_id' => $user->id]);
        $patientAssignedToAnotherUser = PatientFactory::new()->createOne(['user_id' => $anotherUser->id]);

        $rawClinicA = $doctorA->clinics()->firstOrFail()->getKey();
        $rawClinicB = $doctorB->clinics()->firstOrFail()->getKey();
        assert(is_int($rawClinicA));
        assert(is_int($rawClinicB));
        $clinicA = $rawClinicA;
        $clinicB = $rawClinicB;

        $start = CarbonImmutable::now()->addHour()->seconds(0);
        $end = $start->addMinutes(60);

        $a1 = AppointmentFactory::new()->createOne([
            'user_id' => $user->id,
            'doctor_id' => $doctorA->id,
            'patient_id' => $patientAssignedToUser->id,
            'clinic_id' => $clinicA,
            'start_date' => $start->toDateTimeString(),
            'end_date' => $end->toDateTimeString(),
        ]);

        $a2 = AppointmentFactory::new()->createOne([
            'user_id' => $user->id,
            'doctor_id' => $doctorB->id,
            'patient_id' => $patientAssignedToUser->id,
            'clinic_id' => $clinicB,
            'start_date' => $start->addMinutes(90)->toDateTimeString(),
            'end_date' => $end->addMinutes(90)->toDateTimeString(),
        ]);

        AppointmentFactory::new()->createOne([
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

    it('returns empty list when the logged user has no appointments', function (): void {
        actingAsApi();

        getJson('/api/appointments/me/appointments')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    });
});
