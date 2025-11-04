<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\PatientFactory;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        PatientFactory::new()->count(50)->create();
    }
}
