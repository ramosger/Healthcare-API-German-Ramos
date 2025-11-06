<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Database\Factories\AppointmentFactory;
use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Lightit\Appointments\Domain\DataTransferObjects\AppointmentDto;

/**
 * @return array{0: CarbonImmutable, 1: CarbonImmutable}
 */
function createInterval(): array
{
    $start = CarbonImmutable::now()->addHour()->seconds(0);
    $end = $start->addMinutes(60);

    return [$start, $end];
}

function createAppointmentDto(
    int $doctorId,
    int $patientId,
    int $clinicId,
    CarbonImmutable $start,
    CarbonImmutable $end,
): AppointmentDto {
    return new AppointmentDto(
        doctor_id: $doctorId,
        patient_id: $patientId,
        clinic_id: $clinicId,
        start_date: $start,
        end_date: $end,
    );
}

function createAppointment(
    int $doctorId,
    int $patientId,
    int $clinicId,
    CarbonImmutable $start,
    CarbonImmutable $end,
): void {
    AppointmentFactory::new()->create([
        'doctor_id'  => $doctorId,
        'patient_id' => $patientId,
        'clinic_id'  => $clinicId,
        'start_date' => $start->toDateTimeString(),
        'end_date'   => $end->toDateTimeString(),
    ]);
}

dataset('invalid-appointments', [

    'doctor does not exist' => function (): array {
        [$start, $end] = createInterval();
        $patient = PatientFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();

        $dto = createAppointmentDto(
            doctorId: 999999,
            patientId: $patient->id,
            clinicId: $clinic->id,
            start: $start,
            end: $end,
        );

        return [$dto, 'Doctor does not exist'];
    },

    'patient does not exist' => function (): array {
        [$start, $end] = createInterval();
        $doctor = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();

        $dto = createAppointmentDto(
            doctorId: $doctor->id,
            patientId: 999999,
            clinicId: $clinic->id,
            start: $start,
            end: $end,
        );

        return [$dto, 'Patient does not exist'];
    },

    'doctor not assigned to clinic' => function (): array {
        [$start, $end] = createInterval();
        $doctor = DoctorFactory::new()->createOne();
        $clinic = ClinicFactory::new()->createOne();
        $patient = PatientFactory::new()->createOne();

        $dto = createAppointmentDto(
            doctorId: $doctor->id,
            patientId: $patient->id,
            clinicId: $clinic->id,
            start: $start,
            end: $end,
        );

        return [$dto, 'The doctor is not assigned to the selected clinic'];
    },

    'doctor overlapping in clinic' => function (): array {
        [$start, $end] = createInterval();
        ClinicFactory::new()->count(5)->create();

        $doctor = DoctorFactory::new()->withRandomClinics(1, 1)->createOne();

        $rawClinicId = $doctor->clinics()->firstOrFail()->getKey();
        assert(is_int($rawClinicId));
        $clinicId = $rawClinicId;

        $patientA = PatientFactory::new()->createOne();
        $patientB = PatientFactory::new()->createOne();

        createAppointment($doctor->id, $patientA->id, $clinicId, $start, $end);

        $dto = createAppointmentDto(
            doctorId: $doctor->id,
            patientId: $patientB->id,
            clinicId: $clinicId,
            start: $start->addMinutes(30),
            end: $end->addMinutes(30),
        );

        return [$dto, 'The doctor already has an appointment in the selected date range'];
    },

    'patient overlapping' => function (): array {
        [$start, $end] = createInterval();
        ClinicFactory::new()->count(5)->create();

        $doctorA = DoctorFactory::new()->withRandomClinics(1, 1)->createOne();
        $doctorB = DoctorFactory::new()->withRandomClinics(1, 1)->createOne();

        $rawClinicA = $doctorA->clinics()->firstOrFail()->getKey();
        $rawClinicB = $doctorB->clinics()->firstOrFail()->getKey();
        assert(is_int($rawClinicA));
        assert(is_int($rawClinicB));
        $clinicA = $rawClinicA;
        $clinicB = $rawClinicB;

        $patient = PatientFactory::new()->createOne();

        createAppointment($doctorA->id, $patient->id, $clinicA, $start, $end);

        $dto = createAppointmentDto(
            doctorId: $doctorB->id,
            patientId: $patient->id,
            clinicId: $clinicB,
            start: $start->addMinutes(15),
            end: $end->addMinutes(15),
        );

        return [$dto, 'The patient already has an appointment in the selected date range'];
    },
]);
