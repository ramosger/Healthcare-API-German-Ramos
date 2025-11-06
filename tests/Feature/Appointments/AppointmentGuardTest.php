<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Database\Factories\AppointmentFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;
use Lightit\Appointments\Domain\Guards\AppointmentGuard;

uses(RefreshDatabase::class);

test('throws domain exception when doctor does not exist', function (): void {
    $patient = PatientFactory::new()->createOne();
    $clinic = ClinicFactory::new()->createOne();

    $dto = new AppointmentDto(
        doctor_id: 999999,
        patient_id: $patient->id,
        clinic_id: $clinic->id,
        start_date: CarbonImmutable::now()->addHour()->seconds(0),
        end_date: CarbonImmutable::now()->addHours(2)->seconds(0),
    );

    expect(fn () => call_user_func([new AppointmentGuard(), 'assertCanCreate'], $dto))
        ->toThrow(\DomainException::class, 'Doctor does not exist');
});

test('throws domain exception when patient does not exist', function (): void {
    $doctor = DoctorFactory::new()->createOne();
    $clinic = ClinicFactory::new()->createOne();

    $dto = new AppointmentDto(
        doctor_id: $doctor->id,
        patient_id: 999999,
        clinic_id: $clinic->id,
        start_date: CarbonImmutable::now()->addHour()->seconds(0),
        end_date: CarbonImmutable::now()->addHours(2)->seconds(0),
    );

    expect(fn () => call_user_func([new AppointmentGuard(), 'assertCanCreate'], $dto))
        ->toThrow(\DomainException::class, 'Patient does not exist');
});

test('throws domain exception when doctor is not assigned to selected clinic', function (): void {
    $doctor = DoctorFactory::new()->createOne();
    $clinic = ClinicFactory::new()->createOne();
    $patient = PatientFactory::new()->createOne();

    $dto = new AppointmentDto(
        doctor_id: $doctor->id,
        patient_id: $patient->id,
        clinic_id: $clinic->id,
        start_date: CarbonImmutable::now()->addHour()->seconds(0),
        end_date: CarbonImmutable::now()->addHours(2)->seconds(0),
    );

    expect(fn () => call_user_func([new AppointmentGuard(), 'assertCanCreate'], $dto))
        ->toThrow(\DomainException::class, 'The doctor is not assigned to the selected clinic');
});

test('throws domain exception when doctor already has overlapping appointments in clinic', function (): void {
    ClinicFactory::new()->count(5)->create();

    $doctor = DoctorFactory::new()->withRandomClinics(1, 1)->createOne();
    $rawClinicId = $doctor->clinics()->firstOrFail()->getKey();
    assert(is_int($rawClinicId));
    $clinicId = $rawClinicId;
    $patientA = PatientFactory::new()->createOne();
    $patientB = PatientFactory::new()->createOne();

    $start = CarbonImmutable::now()->addHour()->seconds(0);
    $end = $start->addMinutes(60);

    AppointmentFactory::new()->create([
        'doctor_id' => $doctor->id,
        'patient_id' => $patientA->id,
        'clinic_id' => $clinicId,
        'start_date' => $start->toDateTimeString(),
        'end_date' => $end->toDateTimeString(),
    ]);

    $dto = new AppointmentDto(
        doctor_id: $doctor->id,
        patient_id: $patientB->id,
        clinic_id: $clinicId,
        start_date: $start->addMinutes(30),
        end_date: $end->addMinutes(30),
    );

    expect(fn () => call_user_func([new AppointmentGuard(), 'assertCanCreate'], $dto))
        ->toThrow(\DomainException::class, 'The doctor already has an appointment in the selected date range');
});

test('throws domain exception when patient already has overlapping appointments', function (): void {
    ClinicFactory::new()->count(5)->create();

    $doctorA = DoctorFactory::new()->withRandomClinics(1, 1)->createOne();
    $doctorB = DoctorFactory::new()->withRandomClinics(1, 1)->createOne();

    $rawClinicA = $doctorA->clinics()->firstOrFail()->getKey();
    assert(is_int($rawClinicA));
    $clinicA = $rawClinicA;
    $rawClinicB = $doctorB->clinics()->firstOrFail()->getKey();
    assert(is_int($rawClinicB));
    $clinicB = $rawClinicB;

    $patient = PatientFactory::new()->createOne();

    $start = CarbonImmutable::now()->addHour()->seconds(0);
    $end = $start->addMinutes(60);

    AppointmentFactory::new()->create([
        'doctor_id' => $doctorA->id,
        'patient_id' => $patient->id,
        'clinic_id' => $clinicA,
        'start_date' => $start->toDateTimeString(),
        'end_date' => $end->toDateTimeString(),
    ]);

    $dto = new AppointmentDto(
        doctor_id: $doctorB->id,
        patient_id: $patient->id,
        clinic_id: $clinicB,
        start_date: $start->addMinutes(15),
        end_date: $end->addMinutes(15),
    );

    expect(fn () => call_user_func([new AppointmentGuard(), 'assertCanCreate'], $dto))
        ->toThrow(\DomainException::class, 'The patient already has an appointment in the selected date range');
});
