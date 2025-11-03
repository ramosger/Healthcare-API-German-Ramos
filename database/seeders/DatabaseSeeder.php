<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            PatientSeeder::class,
            UserSeeder::class,
            DoctorClinicSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}
