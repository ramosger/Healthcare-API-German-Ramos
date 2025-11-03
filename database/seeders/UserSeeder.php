<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\UserFactory;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        UserFactory::new()->count(35)->create();
    }
}
