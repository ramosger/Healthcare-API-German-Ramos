<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Database\Factories\UserFactory;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;
use function Pest\Laravel\withoutMiddleware;

uses(RefreshDatabase::class);

describe('POST /api/appointments', function (): void {
    /**
     * @return array{0: Carbon\CarbonImmutable, 1: Carbon\CarbonImmutable}
     */
    $interval = static function (): array {
        $start = CarbonImmutable::now()->addHour()->seconds(0);
        $end = $start->addMinutes(60);

        return [$start, $end];
    };

    /**
     * @return array<string, int|string>
     */
    $payload = static function (
        int $doctorId,
        int $patientId,
        int $clinicId,
        CarbonImmutable $start,
        CarbonImmutable $end,
    ): array {
        return [
            'doctor_id'  => $doctorId,
            'patient_id' => $patientId,
            'clinic_id'  => $clinicId,
            'start_date' => $start->toISOString(),
            'end_date'   => $end->toISOString(),
        ];
    };

    it('creates an appointment with all valid fields', function () use ($interval, $payload): void {
        $user = actingAsApi();

        ClinicFactory::new()->count(10)->create();

        $doctor = DoctorFactory::new()->withRandomClinics(1, 3)->createOne();
        $patient = PatientFactory::new()->createOne(['user_id' => $user->id]);

        $rawClinicId = $doctor->clinics()->firstOrFail()->getKey();
        assert(is_int($rawClinicId));
        $clinicId = $rawClinicId;

        [$start, $end] = $interval();

        $body = $payload($doctor->id, $patient->id, $clinicId, $start, $end);

        $response = postJson('/api/appointments', $body)->assertCreated();

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

    it('fails with 422 when patient is not associated with logged user', function () use ($interval, $payload): void {
        actingAsApi();

        ClinicFactory::new()->count(10)->create();

        $doctor = DoctorFactory::new()->withRandomClinics(1, 3)->createOne();
        $patient = PatientFactory::new()->createOne();

        $rawClinicId = $doctor->clinics()->firstOrFail()->getKey();
        assert(is_int($rawClinicId));
        $clinicId = $rawClinicId;

        [$start, $end] = $interval();

        postJson('/api/appointments', $payload($doctor->id, $patient->id, $clinicId, $start, $end))
            ->assertUnprocessable();
    });

    it('fails with 422 when required fields are missing', function (): void {
        actingAsApi();

        postJson('/api/appointments', [])
            ->assertUnprocessable();
    });

    it('fails with 422 when doctor does not belong to given clinic', function (): void {
        actingAsApi();

        ClinicFactory::new()->count(10)->create();

        $doctor = DoctorFactory::new()->withRandomClinics(1, 3)->createOne();
        $patient = PatientFactory::new()->createOne();
        $unlinkedClinic = ClinicFactory::new()->createOne();

        postJson('/api/appointments', [
            'doctor_id'  => $doctor->id,
            'patient_id' => $patient->id,
            'clinic_id'  => $unlinkedClinic->id,
            'start_date' => CarbonImmutable::now()->addHour()->toISOString(),
            'end_date'   => CarbonImmutable::now()->addHours(2)->toISOString(),
        ])->assertUnprocessable();
    });

    it(
        'fails with 422 when doctor already has an appointment in the selected date range',
        function () use ($interval): void {
            $user = actingAsApi();
            $anotherUser = UserFactory::new()->createOne();
    
            ClinicFactory::new()->count(5)->create();
    
            $doctor = DoctorFactory::new()->withRandomClinics(1, 2)->createOne();
            $p1 = PatientFactory::new()->createOne(['user_id' => $user->id]);
            $p2 = PatientFactory::new()->createOne(['user_id' => $anotherUser->id]);
    
            $rawClinicId = $doctor->clinics()->firstOrFail()->getKey();
            assert(is_int($rawClinicId));
            $clinicId = $rawClinicId;
    
            [$start, $end] = $interval();
    
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
        }
    );

    it(
        'fails with 422 when patient already has an appointment in the selected date range',
        function () use ($interval): void {
            $user = actingAsApi();
    
            ClinicFactory::new()->count(5)->create();
    
            $doctorA = DoctorFactory::new()->withRandomClinics(1, 2)->createOne();
            $doctorB = DoctorFactory::new()->withRandomClinics(1, 2)->createOne();
            $patient = PatientFactory::new()->createOne(['user_id' => $user->id]);
    
            $rawClinicA = $doctorA->clinics()->firstOrFail()->getKey();
            $rawClinicB = $doctorB->clinics()->firstOrFail()->getKey();
            assert(is_int($rawClinicA));
            assert(is_int($rawClinicB));
            $clinicA = $rawClinicA;
            $clinicB = $rawClinicB;
    
            [$start, $end] = $interval();
    
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
        }
    );

    it('returns 401 when user is non authenticated', function (): void {
        withoutMiddleware([Authenticate::class]);

        postJson('/api/appointments', [])
            ->assertUnauthorized();
    });
});
