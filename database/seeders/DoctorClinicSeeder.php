<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Illuminate\Database\Seeder;
use Lightit\Clinics\Domain\Models\Clinic;

class DoctorClinicSeeder extends Seeder
{
    public function run(): void
    {
        if (!Clinic::query()->exists()) {
            ClinicFactory::new()->count(10)->create();
        }

        DoctorFactory::new()
            ->count(20)
            ->withRandomClinics(1, 3)
            ->create();
    }
}
