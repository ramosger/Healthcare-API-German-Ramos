<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lightit\Clinics\Domain\Models\Clinic;
use Lightit\Doctors\Domain\Models\Doctor;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }

    public function withRandomClinics(int $min = 1, int $max = 3): self
    {
        return $this->afterCreating(function (Doctor $doctor) use ($min, $max) {
            $take = random_int($min, $max);
            $clinicIds = Clinic::query()
                ->inRandomOrder()
                ->limit($take)
                ->pluck('id')
                ->all();

            $doctor->clinics()->attach($clinicIds);
        });
    }
}
