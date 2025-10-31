<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Factories\ClinicFactory;
use Database\Factories\DoctorFactory;
use Lightit\Doctors\Domain\Models\Doctor;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $clinics = ClinicFactory::new()->createMany(10);
        $doctors = DoctorFactory::new()->count(20)->create();
        UserFactory::new()->createMany(35);

        $clinicIds = $clinics->pluck('id');

        $doctors->each(function (Doctor $doctor) use ($clinicIds) {
            $doctor->clinics()->syncWithoutDetaching(
                $clinicIds->random(rand(1, 3))->all()
            );
        });
    }
}
