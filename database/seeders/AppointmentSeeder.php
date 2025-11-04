<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\AppointmentFactory;
use Lightit\Doctors\Domain\Models\Doctor;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Patients\Domain\Models\Patient;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $doctorIds  = Doctor::query()->pluck('id');
        $patientIds = Patient::query()->pluck('id');
        $clinicIds  = Clinic::query()->pluck('id');

        AppointmentFactory::new()
            ->count(100)
            ->state(fn () => [
                'doctor_id'  => $doctorIds->random(),
                'patient_id' => $patientIds->random(),
                'clinic_id'  => $clinicIds->random(),
            ])
            ->create();
    }
}
